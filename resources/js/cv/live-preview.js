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
        pdfExportStatus: 'idle',
        pdfExportError: null,
        syncStatus: 'waiting',
        lastUpdateAt: null,
        channel: null,
        pollTimer: null,
        pollIntervalMs: config.poll_interval_ms ?? 5000,

        init() {
            applyTemplateMeta(this.document, this.activeTemplate, this.templateCatalog);
            this.activeTemplate = this.document.template?.slug ?? this.document.template?.key ?? this.activeTemplate;
            this.channel = openPreviewChannel(this.id, (data) => this.handleSyncMessage(data));

            this.$nextTick(() => {
                this.refreshPreview();
            });

            this.startPollingFallback();

            window.addEventListener('beforeunload', () => this.destroy());
        },

        destroy() {
            clearInterval(this.pollTimer);
            this.channel?.close();
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
            this.refreshPreview();
        },

        startPollingFallback() {
            if (! this.urls.render) {
                return;
            }

            this.pollTimer = window.setInterval(() => {
                if (this.syncStatus === 'live') {
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

            frame.srcdoc = html;
            this.previewFrameReady = true;
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
