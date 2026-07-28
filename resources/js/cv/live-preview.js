import { ClientPreviewRenderer } from './preview-renderer';
import { applyTemplateMeta } from './layout-resolver';
import { openPreviewChannel } from './preview-sync';
import { downloadCvPdf, resolveCvPdfDownloadError } from './pdf-download';
const clone = (value) => JSON.parse(JSON.stringify(value));

export function registerCvLivePreview() {
    window.Alpine.data('cvLivePreview', (config) => ({
        id: config.id,
        title: config.title,
        document: clone(config.document),
        sections: config.sections,
        templateCatalog: config.template_catalog ?? [],
        activeTemplate: config.active_template ?? config.document?.template?.key ?? 'classic',
        urls: config.urls ?? {},
        previewRenderer: new ClientPreviewRenderer(),
        previewFrameReady: false,
        lastPreviewHtml: '',
        pdfExportStatus: 'idle',
        pdfExportError: null,
        syncStatus: 'waiting',
        lastUpdateAt: null,
        channel: null,
        pollTimer: null,
        pollIntervalMs: config.poll_interval_ms ?? 5000,
        isUserScrolling: false,
        scrollIdleTimer: null,
        pendingPreviewHtml: null,

        init() {
            applyTemplateMeta(this.document, this.activeTemplate, this.templateCatalog);
            this.activeTemplate = this.document.template?.slug ?? this.document.template?.key ?? this.activeTemplate;
            this.channel = openPreviewChannel(this.id, (data) => this.handleSyncMessage(data));

            // Track page-level scrolling so preview updates never land mid-scroll.
            window.addEventListener('scroll', () => this.markScrolling(), { passive: true });

            this.$nextTick(() => {
                this.refreshPreview();
            });

            this.startPollingFallback();

            window.addEventListener('beforeunload', () => this.destroy());
        },

        destroy() {
            clearInterval(this.pollTimer);
            clearTimeout(this.scrollIdleTimer);
            this.channel?.close();
        },

        markScrolling() {
            this.isUserScrolling = true;
            clearTimeout(this.scrollIdleTimer);

            this.scrollIdleTimer = setTimeout(() => {
                this.isUserScrolling = false;

                // Apply the update that was deferred while scrolling.
                if (this.pendingPreviewHtml !== null) {
                    const html = this.pendingPreviewHtml;
                    this.pendingPreviewHtml = null;
                    this.updatePreviewFrame(html);
                }
            }, 250);
        },

        attachFrameScrollListener() {
            try {
                this.$refs.previewFrame?.contentWindow?.addEventListener(
                    'scroll',
                    () => this.markScrolling(),
                    { passive: true },
                );
            } catch {
                // Frame not accessible yet — listener reattached on next write.
            }
        },

        handleSyncMessage(data) {
            if (! data || data.type !== 'document-update') {
                return;
            }

            if (data.document) {
                this.document = clone(data.document);
            }

            if (data.title) {
                this.title = data.title;
            }

            if (data.activeTemplate) {
                this.activeTemplate = data.activeTemplate;
                applyTemplateMeta(this.document, data.activeTemplate, this.templateCatalog);
                this.previewRenderer.invalidateAll();
            }

            this.syncStatus = 'live';
            this.lastUpdateAt = data.updatedAt ?? Date.now();

            // Editor is pushing realtime updates — the polling fallback is no
            // longer needed and would only cause extra re-renders.
            if (this.pollTimer) {
                clearInterval(this.pollTimer);
                this.pollTimer = null;
            }

            this.refreshPreview();
        },

        startPollingFallback() {
            if (! this.urls.render) {
                return;
            }

            // Standalone tab (no opener): nothing is editing this CV from a
            // parent window, so skip auto-refresh polling entirely. Realtime
            // updates still arrive via BroadcastChannel if a builder tab opens.
            if (window.opener === null) {
                return;
            }

            this.pollTimer = window.setInterval(() => {
                if (this.syncStatus === 'live') {
                    return;
                }

                // Don't refresh a hidden tab or interrupt active scrolling.
                if (document.hidden || this.isUserScrolling) {
                    return;
                }

                this.fetchServerPreview();
            }, this.pollIntervalMs);
        },

        async fetchServerPreview() {
            if (! this.urls.render) {
                return;
            }

            try {
                const response = await fetch(this.urls.render, {
                    headers: { Accept: 'text/html' },
                    credentials: 'same-origin',
                });

                if (! response.ok) {
                    return;
                }

                const html = await response.text();
                this.updatePreviewFrame(html);
                this.syncStatus = 'polling';
                this.lastUpdateAt = Date.now();
            } catch {
                // ignore network errors during polling
            }
        },

        refreshPreview() {
            const result = this.previewRenderer.build(
                this.document,
                this.sections,
                this.activeTemplate,
                this.templateCatalog,
            );

            this.updatePreviewFrame(result.html);
        },

        updatePreviewFrame(html) {
            const frame = this.$refs.previewFrame;

            if (! frame) {
                return;
            }

            // Identical content — skip the update entirely so polling never
            // reloads the iframe (and resets scroll) while the user reads.
            if (html === this.lastPreviewHtml) {
                return;
            }

            // User is mid-scroll: defer the update until scrolling settles so
            // the page never jumps under their finger. Only the latest HTML
            // is kept — intermediate states are dropped.
            if (this.isUserScrolling && this.previewFrameReady) {
                this.pendingPreviewHtml = html;

                return;
            }

            this.lastPreviewHtml = html;
            this.pendingPreviewHtml = null;

            // First render: plain srcdoc load.
            if (! this.previewFrameReady || ! frame.contentDocument?.documentElement) {
                frame.srcdoc = html;
                this.previewFrameReady = true;

                frame.addEventListener('load', () => this.attachFrameScrollListener(), { once: true });

                return;
            }

            // Subsequent renders: rewrite the document in place instead of
            // reassigning srcdoc (which reloads the iframe), and keep the
            // reader's scroll position stable.
            const frameWindow = frame.contentWindow;
            const scrollX = frameWindow?.scrollX ?? 0;
            const scrollY = frameWindow?.scrollY ?? 0;

            const doc = frame.contentDocument;
            doc.open();
            doc.write(html);
            doc.close();

            this.attachFrameScrollListener();

            window.requestAnimationFrame(() => {
                frame.contentWindow?.scrollTo(scrollX, scrollY);
            });
        },

        syncStatusLabel() {
            return {
                waiting: 'Waiting for editor…',
                live: 'Live — updating as you edit',
                polling: 'Synced from saved CV',
            }[this.syncStatus] ?? 'Preview';
        },

        lastUpdateLabel() {
            if (! this.lastUpdateAt) {
                return '';
            }

            return `Updated ${new Date(this.lastUpdateAt).toLocaleTimeString()}`;
        },

        async exportPdf() {
            if (! this.urls.pdfDownload || this.pdfExportStatus === 'generating') {
                return;
            }

            this.pdfExportStatus = 'generating';
            this.pdfExportError = null;

            try {
                await downloadCvPdf({
                    pdfDownloadUrl: this.urls.pdfDownload,
                    previewFrame: this.$refs.previewFrame,
                    title: this.title,
                    slug: String(this.id),
                    cvDocument: this.document,
                    activeTemplate: this.activeTemplate,
                    templateCatalog: this.templateCatalog,
                    beforeExport: async () => {
                        await this.$nextTick();
                    },
                });

                this.pdfExportStatus = 'idle';
            } catch (error) {
                this.pdfExportStatus = 'error';
                this.pdfExportError = await resolveCvPdfDownloadError(error);
            }
        },
    }));
}
