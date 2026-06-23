import axios from 'axios';
import { ClientPreviewRenderer } from './preview-renderer';
import {
    buildPdfSyncPayload,
    prepareAutosavePayload,
} from './builder-payload';
import { downloadCvPdf, resolveCvPdfDownloadError } from './pdf-download';
import { applyTemplateMeta, inlineStyle, normalizePhotoPosition } from './layout-resolver';
import {
    isAboutEnabledInTemplate,
    isBuilderSectionInTemplate,
    isPersonalInfoEnabledInTemplate,
    isProfilePhotoEnabledInTemplate,
    previewSectionMatchesFocus,
    profilePhotoPositionLabel,
    syncBuilderSectionsFromTemplate,
    templateEnabledSections,
} from './builder-sections';
import { broadcastPreviewState, openPreviewChannel } from './preview-sync';
import { applyPreviewPatches } from './preview-patcher';
import { dedupeWithinMs, perfLog, scheduleFrame } from './builder-perf';
import { prepareEditorHtml, richTextToPlainText } from '../shared/rich-text-sanitize';

const clone = (value) => JSON.parse(JSON.stringify(value));

export function registerCvBuilder() {
    window.Alpine.data('cvBuilder', (config) => ({
        id: config.id,
        slug: config.slug,
        title: config.title,
        status: config.status,
        // LIVE PREVIEW FIX: single builder state — preview reads only from document
        document: clone(config.document),
        sections: config.sections,
        templates: config.templates,
        templateCatalog: config.template_catalog ?? [],
        activeTemplate: config.active_template ?? config.document?.template?.key ?? 'classic',
        urls: config.urls,
        debounceMs: config.autosave_debounce_ms ?? 1000,
        previewDebounceMs: config.preview_debounce_ms ?? 200,
        activeSection: config.document.meta.section_order?.[0] ?? 'personal_details',
        saveStatus: 'idle',
        previewLoading: false,
        previewHtml: '',
        previewRenderer: new ClientPreviewRenderer(),
        previewBroadcastTimer: null,
        previewFrameReady: false,
        previewResizeObserver: null,
        previewScaleTimer: null,
        pdfExportStatus: 'idle',
        pdfExportError: null,
        aiLoading: false,
        aiFieldErrors: {},
        profileImageError: null,
        aiUsage: { used: 0, limit: 0, remaining: null },
        hasAiAccess: config.has_ai_access ?? false,
        errors: {},
        debounceTimer: null,
        previewTimer: null,
        isDirty: false,
        autosaveInFlight: null,
        leaveSaveBound: null,
        pdfSyncPayload: null,
        hydrating: true,
        lastFieldEventAt: 0,
        pendingPreviewSection: null,
        previewScaleSnapshot: null,
        previewChannel: null,

        init() {
            this.syncTemplateState(this.activeTemplate);
            this.ensureDocumentSections();
            this.activeSection = this.document.meta?.section_order?.[0]
                ?? this.document.meta?.enabled_sections?.[0]
                ?? 'personal_details';
            this.$store.builderStore.bind(this.document, this.sections);
            this.previewChannel = openPreviewChannel(this.id, null);

            this.$watch('title', () => {
                this.markDirty();
            });

            this.bindLeaveSaveHandlers();

            this.$nextTick(() => {
                scheduleFrame(() => {
                    this.refreshPreview();
                    this.hydrating = false;
                    this.isDirty = false;
                    this.saveStatus = 'idle';
                });
            });
            this.loadAiUsage();
            this.initPreviewResizeObserver();
        },

        initPreviewResizeObserver() {
            const frame = this.$refs.previewFrame;

            if (! frame || typeof ResizeObserver !== 'function') {
                return;
            }

            this.previewResizeObserver?.disconnect();
            this.previewResizeObserver = new ResizeObserver(() => {
                const scroll = this.capturePreviewScroll();
                this.schedulePreviewScale(() => {
                    this.restorePreviewScroll(scroll);
                });
            });
            this.previewResizeObserver.observe(frame);
        },

        schedulePreviewScale(afterScale = null) {
            clearTimeout(this.previewScaleTimer);
            this.previewScaleTimer = setTimeout(() => {
                this.fitPreviewScale();
                afterScale?.();
            }, 32);
        },

        fitPreviewScale() {
            const frame = this.$refs.previewFrame;
            const doc = frame?.contentDocument;
            const page = doc?.querySelector('.cv-page');
            const body = doc?.body;
            const html = doc?.documentElement;

            if (! page || ! body || ! frame) {
                return;
            }

            const pageWidth = page.offsetWidth || 794;
            const pageHeight = Math.max(page.scrollHeight, page.offsetHeight, 1123);
            const availableW = Math.max(frame.clientWidth, 280);
            const availableH = Math.max(frame.clientHeight, 200);
            const minScale = 0.28;

            let scale = availableW / pageWidth;

            if (scale < minScale) {
                scale = minScale;
            }

            const snapshot = this.previewScaleSnapshot;

            if (
                snapshot
                && snapshot.pageWidth === pageWidth
                && snapshot.pageHeight === pageHeight
                && snapshot.availableW === availableW
                && snapshot.availableH === availableH
                && Math.abs(snapshot.scale - scale) < 0.0001
            ) {
                return;
            }

            this.previewScaleSnapshot = {
                pageWidth,
                pageHeight,
                availableW,
                availableH,
                scale,
            };

            page.style.transform = '';
            page.style.margin = '';
            page.style.marginBottom = '';
            body.style.height = '';
            body.style.minHeight = '';
            body.style.overflow = '';
            body.style.overflowX = '';
            body.style.overflowY = '';

            if (html) {
                html.style.height = '';
                html.style.overflow = '';
            }

            body.style.overflowX = 'hidden';
            body.style.overflowY = 'auto';
            body.style.minHeight = '100%';
            body.style.background = '#ffffff';

            if (html) {
                html.style.height = '100%';
                html.style.overflow = 'hidden';
            }

            page.style.transform = `scale(${scale})`;
            page.style.transformOrigin = 'top left';
            page.style.margin = '0';
            page.style.marginBottom = `${-(pageHeight * (1 - scale))}px`;

            const scaledHeight = pageHeight * scale;

            if (scaledHeight <= availableH) {
                body.style.overflowY = 'hidden';
            }
        },

        activeTemplateName() {
            return this.templateCatalog.find((item) => item.key === this.activeTemplate)?.name
                ?? this.activeTemplate;
        },

        destroy() {
            clearTimeout(this.previewBroadcastTimer);
            clearTimeout(this.previewScaleTimer);
            this.previewResizeObserver?.disconnect();
        },

        bindLeaveSaveHandlers() {
            this.leaveSaveBound = () => {
                this.flushAutosaveKeepalive();
                clearTimeout(this.previewBroadcastTimer);
                this.previewChannel?.close();
            };

            window.addEventListener('beforeunload', this.leaveSaveBound);
            document.addEventListener('visibilitychange', () => {
                if (document.visibilityState === 'hidden' && this.isDirty) {
                    this.flushAutosaveKeepalive();
                }
            });
        },

        broadcastPreviewState() {
            clearTimeout(this.previewBroadcastTimer);
            this.previewBroadcastTimer = setTimeout(() => {
                broadcastPreviewState(this.previewChannel, {
                    document: clone(this.document),
                    title: this.title,
                    activeTemplate: this.activeTemplate,
                });
            }, 300);
        },

        // PERF: lightweight handler — no JSON clone, no deep watch, deduped events
        onBuilderFieldChange(event) {
            const startedAt = performance.now();
            const dedupe = dedupeWithinMs(this.lastFieldEventAt, 16);

            if (dedupe.skip) {
                return;
            }

            this.lastFieldEventAt = dedupe.now;

            if (event?.target?.type === 'file' && event.target.files?.[0]) {
                this.handleImagePreview(event.target);
                return;
            }

            if (event?.type === 'change' && ['text', 'email', 'url', 'tel'].includes(event?.target?.type)) {
                return;
            }

            const sectionKey = event?.target?.closest('[data-builder-section]')?.dataset?.builderSection
                ?? this.activeSection;

            this.scheduleChange(sectionKey);
            perfLog('FORM_RENDER_TIME', startedAt);
        },

        // LIVE PREVIEW FIX: instant local image preview via data URL (autosave + PDF compatible)
        handleImagePreview(input) {
            const file = input.files?.[0];

            if (! file) {
                return;
            }

            if (! file.type.startsWith('image/')) {
                this.profileImageError = 'Please choose a valid image file.';

                return;
            }

            this.compressProfilePhoto(file)
                .then((dataUrl) => {
                    this.profileImageError = null;
                    this.document.personal_details.photo_preview = dataUrl;
                    this.previewRenderer.invalidateAll();
                    this.scheduleChange('personal_details');
                })
                .catch(() => {
                    this.profileImageError = 'Could not load the selected image. Please try another file.';
                })
                .finally(() => {
                    input.value = '';
                });
        },

        compressProfilePhoto(file, maxSize = 400, quality = 0.85) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();

                reader.onload = () => {
                    const img = new Image();

                    img.onload = () => {
                        const scale = Math.min(1, maxSize / Math.max(img.width, img.height, 1));
                        const canvas = document.createElement('canvas');
                        canvas.width = Math.max(1, Math.round(img.width * scale));
                        canvas.height = Math.max(1, Math.round(img.height * scale));

                        const context = canvas.getContext('2d');

                        if (! context) {
                            reject(new Error('Canvas unavailable'));

                            return;
                        }

                        context.drawImage(img, 0, 0, canvas.width, canvas.height);
                        resolve(canvas.toDataURL('image/jpeg', quality));
                    };

                    img.onerror = () => reject(new Error('Image decode failed'));
                    img.src = reader.result;
                };

                reader.onerror = () => reject(new Error('File read failed'));
                reader.readAsDataURL(file);
            });
        },

        // PERF: batch preview + autosave without blocking input
        scheduleChange(sectionKey = null) {
            if (this.hydrating) {
                return;
            }

            this.pendingPreviewSection = sectionKey ?? this.activeSection;
            this.isDirty = true;
            this.saveStatus = 'pending';
            this.queuePreviewRefresh();
            this.queueAutosave();
        },

        onBuilderStateChange() {
            this.scheduleChange(this.activeSection);
        },

        // PERF: JSON payload built lazily on autosave/export only — not every keystroke
        syncJsonState() {
            this.pdfSyncPayload = buildPdfSyncPayload({
                title: this.title,
                document: this.document,
                activeTemplate: this.activeTemplate,
                templateCatalog: this.templateCatalog,
            });
        },

        async loadAiUsage() {
            if (! this.urls.aiUsage) {
                return;
            }

            try {
                const { data } = await axios.get(this.urls.aiUsage);
                this.aiUsage = data.usage ?? this.aiUsage;

                if (typeof data.has_ai_access === 'boolean') {
                    this.hasAiAccess = data.has_ai_access;
                }
            } catch (error) {
                // ignore usage fetch errors in builder
            }
        },

        aiLogUrl(logId) {
            return (this.urls.aiLog ?? '').replace('__ID__', logId);
        },

        applyAiUsage(usage) {
            if (usage) {
                this.aiUsage = usage;
            }
        },

        setAiFieldError(fieldKey, message) {
            if (! fieldKey) {
                return;
            }

            this.aiFieldErrors = {
                ...this.aiFieldErrors,
                [fieldKey]: message,
            };
        },

        clearAiFieldError(fieldKey) {
            if (! fieldKey || ! this.aiFieldErrors[fieldKey]) {
                return;
            }

            const next = { ...this.aiFieldErrors };
            delete next[fieldKey];
            this.aiFieldErrors = next;
        },

        guardAiAccess(fieldKey) {
            if (this.hasAiAccess) {
                return true;
            }

            this.promptAiUpgrade(fieldKey);

            return false;
        },

        requireAiText(value, fieldKey, message = 'Add some text before clicking Improve.') {
            if (richTextToPlainText(value) !== '') {
                this.clearAiFieldError(fieldKey);

                return true;
            }

            this.setAiFieldError(fieldKey, message);

            return false;
        },

        promptAiUpgrade(fieldKey) {
            this.setAiFieldError(
                fieldKey,
                'AI writing assistance is available on Pro plans. Upgrade to unlock Improve.',
            );

            if (window.confirm('AI features require a Pro plan. View upgrade options now?')) {
                window.location.href = this.urls.subscription ?? '/dashboard/subscription';
            }
        },

        async runAiRequest(url, payload, { queued = false, apply = null, fieldKey = null } = {}) {
            if (! url || this.aiLoading) {
                return null;
            }

            this.aiLoading = true;

            if (fieldKey) {
                this.clearAiFieldError(fieldKey);
            }

            try {
                const { data } = await axios.post(url, payload);
                this.applyAiUsage(data.usage);

                let log = data.log;

                if (queued || log?.status === 'pending' || log?.status === 'processing') {
                    log = await this.pollAiLog(log.id);
                }

                if (log?.status === 'failed') {
                    throw new Error(log.error_message ?? 'AI request failed.');
                }

                if (apply && log) {
                    apply(log);
                }

                this.onBuilderStateChange();

                return log;
            } catch (error) {
                const validationErrors = error.response?.data?.errors ?? {};
                const firstValidationMessage = Object.values(validationErrors)
                    .flat()
                    .find((message) => typeof message === 'string');

                const message = error.response?.status === 403
                    ? (error.response?.data?.message
                        ?? 'AI writing assistance is available on Pro plans. Upgrade to unlock Improve.')
                    : (firstValidationMessage
                        ?? error.response?.data?.message
                        ?? error.message
                        ?? 'AI request failed.');

                if (error.response?.status === 403) {
                    this.hasAiAccess = false;
                }

                if (fieldKey) {
                    this.setAiFieldError(fieldKey, message);
                }

                this.applyAiUsage(error.response?.data?.usage);

                return null;
            } finally {
                this.aiLoading = false;
            }
        },

        async pollAiLog(logId, attempts = 60) {
            for (let attempt = 0; attempt < attempts; attempt += 1) {
                const { data } = await axios.get(this.aiLogUrl(logId));
                const log = data.log;

                if (log?.status === 'completed' || log?.status === 'failed') {
                    this.applyAiUsage(data.usage);

                    return log;
                }

                await new Promise((resolve) => setTimeout(resolve, 2000));
            }

            throw new Error('AI request timed out.');
        },

        aiImproveSummary(fieldKey = 'about-summary') {
            if (! this.guardAiAccess(fieldKey)) {
                return null;
            }

            if (! this.requireAiText(this.document.personal_details.summary, fieldKey)) {
                return null;
            }

            return this.runAiRequest(this.urls.aiSummary, {
                personal_details: {
                    summary: richTextToPlainText(this.document.personal_details.summary),
                },
            }, {
                fieldKey,
                apply: (log) => {
                    const text = log.result ?? log.response?.text;

                    if (text) {
                        this.document.personal_details.summary = prepareEditorHtml(text);
                    }
                },
            });
        },

        aiImproveExperience(index, fieldKey = null) {
            const resolvedFieldKey = fieldKey ?? `experience-${index}`;

            if (! this.guardAiAccess(resolvedFieldKey)) {
                return null;
            }

            const item = this.document.experience[index];

            if (! item) {
                return null;
            }

            if (! this.requireAiText(item.description, resolvedFieldKey)) {
                return null;
            }

            return this.runAiRequest(this.urls.aiExperience, {
                company: item.company,
                position: item.position,
                location: item.location,
                start_date: item.start_date,
                end_date: item.current ? 'Present' : item.end_date,
                description: richTextToPlainText(item.description),
            }, {
                fieldKey: resolvedFieldKey,
                apply: (log) => {
                    const text = log.result ?? log.response?.text;

                    if (text) {
                        item.description = prepareEditorHtml(text);
                    }
                },
            });
        },

        aiImproveRichText(text, setter, fieldKey = null) {
            if (! fieldKey) {
                return null;
            }

            if (! this.guardAiAccess(fieldKey)) {
                return null;
            }

            if (! this.requireAiText(text, fieldKey)) {
                return null;
            }

            return this.runAiRequest(this.urls.aiGrammar, {
                text: richTextToPlainText(text),
            }, {
                fieldKey,
                apply: (log) => {
                    const corrected = log.result ?? log.response?.text;

                    if (corrected && typeof setter === 'function') {
                        setter(prepareEditorHtml(corrected));
                    }
                },
            });
        },

        syncTemplateState(templateKey, { force = false } = {}) {
            applyTemplateMeta(this.document, templateKey, this.templateCatalog);
            syncBuilderSectionsFromTemplate(this.document, templateKey, this.templateCatalog, { force });

            this.activeTemplate = templateKey;

            if (! this.document.template) {
                this.document.template = {};
            }

            this.document.template.key = templateKey;
            this.document.template.slug = templateKey;

            const selected = this.templateCatalog.find((item) => item.key === templateKey);
            this.document.template.id = selected?.id ?? this.document.template.id ?? null;
        },

        markDirty() {
            if (this.hydrating) {
                return;
            }

            this.isDirty = true;
            this.saveStatus = 'pending';
            this.queueAutosave();
        },

        queueAutosave() {
            clearTimeout(this.debounceTimer);
            this.saveStatus = 'pending';
            this.debounceTimer = setTimeout(() => this.autosave(), this.debounceMs);
        },

        // PERF: debounced preview in rAF — partial section updates only
        queuePreviewRefresh() {
            clearTimeout(this.previewTimer);
            this.previewTimer = setTimeout(() => {
                const focusSection = this.pendingPreviewSection;
                scheduleFrame(() => {
                    this.refreshPreview(focusSection);
                });
            }, this.previewDebounceMs);
        },

        payload() {
            return prepareAutosavePayload({
                title: this.title,
                document: this.document,
                activeTemplate: this.activeTemplate,
                templateCatalog: this.templateCatalog,
            });
        },

        pdfStatusUrl(exportId) {
            return (this.urls.pdfStatus ?? '').replace('__ID__', exportId);
        },

        applyCvResponse(cv) {
            const previousSlug = this.slug;

            this.id = cv.id;
            this.slug = cv.slug;
            this.title = cv.title;
            this.status = cv.status;

            if (cv.slug && previousSlug && cv.slug !== previousSlug) {
                this.refreshCvScopedUrls(previousSlug, cv.slug);
            }

            if (cv.urls) {
                this.urls = { ...this.urls, ...cv.urls };
            }
        },

        refreshCvScopedUrls(fromSlug, toSlug) {
            const from = `/cvs/${encodeURIComponent(fromSlug)}/`;
            const to = `/cvs/${encodeURIComponent(toSlug)}/`;

            Object.keys(this.urls).forEach((key) => {
                if (typeof this.urls[key] === 'string' && this.urls[key].includes(from)) {
                    this.urls[key] = this.urls[key].replace(from, to);
                }
            });
        },

        // LIVE PREVIEW FIX: preserve sticky panel + iframe scroll during preview updates
        capturePreviewScroll() {
            const frame = this.$refs.previewFrame;

            return {
                panel: this.$refs.previewPanel?.scrollTop ?? 0,
                iframe: frame?.contentWindow?.scrollY ?? 0,
            };
        },

        restorePreviewScroll(scroll) {
            if (this.$refs.previewPanel) {
                this.$refs.previewPanel.scrollTop = scroll.panel;
            }

            const frameWindow = this.$refs.previewFrame?.contentWindow;

            if (frameWindow) {
                frameWindow.scrollTo(0, scroll.iframe);
            }
        },

        // LIVE PREVIEW FIX: update isolated preview iframe only (no Alpine srcdoc remount)
        updatePreviewFrame(html, { preserveScroll = true } = {}) {
            const frame = this.$refs.previewFrame;

            if (! frame) {
                this.previewHtml = html;

                return;
            }

            const scroll = preserveScroll ? this.capturePreviewScroll() : { panel: 0, iframe: 0 };

            const afterWrite = () => {
                this.previewScaleSnapshot = null;
                this.fitPreviewScale();
                this.restorePreviewScroll(scroll);
            };

            if (this.previewFrameReady && frame.contentDocument?.documentElement) {
                const doc = frame.contentDocument;
                doc.open();
                doc.write(html);
                doc.close();
                this.previewHtml = html;

                scheduleFrame(afterWrite);

                return;
            }

            frame.srcdoc = html;
            this.previewHtml = html;
            this.previewFrameReady = true;

            frame.onload = () => {
                afterWrite();
                frame.onload = null;
            };
        },

        patchSidebarPhoto(result) {
            const doc = this.$refs.previewFrame?.contentDocument;

            if (! doc || ! this.previewFrameReady) {
                return;
            }

            const photoPosition = normalizePhotoPosition(result.settings?.photo_position);
            const sidebarPhotoHtml = result.sectionHtml.__sidebar_photo ?? '';
            const aside = doc.querySelector('.cv-aside');

            if (photoPosition !== 'sidebar' || ! aside) {
                doc.querySelector('.cv-sidebar-photo')?.remove();

                return;
            }

            const existing = aside.querySelector('.cv-sidebar-photo');

            if (! sidebarPhotoHtml) {
                existing?.remove();

                return;
            }

            const wrapper = doc.createElement('div');
            wrapper.innerHTML = sidebarPhotoHtml.trim();
            const replacement = wrapper.firstElementChild;

            if (! replacement) {
                return;
            }

            if (existing) {
                existing.replaceWith(replacement);
            } else {
                aside.insertBefore(replacement, aside.firstChild);
            }
        },

        applySidebarPhotoPatch(result) {
            if (! result?.sectionHtml) {
                return;
            }

            this.patchSidebarPhoto(result);
        },

        // LIVE PREVIEW FIX: swap layout shell without remounting iframe document
        patchPreviewLayout(result) {
            const doc = this.$refs.previewFrame?.contentDocument;

            if (! doc || ! this.previewFrameReady) {
                return false;
            }

            this.previewScaleSnapshot = null;
            const scroll = this.capturePreviewScroll();
            const page = doc.querySelector('.cv-page');

            if (! page) {
                return false;
            }

            page.className = `cv-page cv-layout-${result.layout}`;
            page.setAttribute('data-layout', result.layout);
            page.setAttribute('data-sidebar', result.settings?.sidebar_position ?? 'none');
            page.setAttribute('data-photo', result.settings?.photo_position ?? 'top');
            page.style.cssText = inlineStyle(result.settings ?? {});

            const existingLayout = page.querySelector('.cv-grid-sidebar, .cv-content');
            const wrapper = doc.createElement('div');
            wrapper.innerHTML = result.bodyHtml.trim();
            const replacement = wrapper.firstElementChild;

            if (existingLayout && replacement) {
                existingLayout.replaceWith(replacement);
            } else {
                page.innerHTML = result.bodyHtml;
            }

            this.restorePreviewScroll(scroll);

            this.$nextTick(() => {
                const scroll = this.capturePreviewScroll();
                this.fitPreviewScale();
                this.restorePreviewScroll(scroll);
            });

            return true;
        },

        finishPreviewUpdate(result = null) {
            if (result) {
                this.applySidebarPhotoPatch(result);
            }

            const scroll = this.capturePreviewScroll();

            scheduleFrame(() => {
                this.fitPreviewScale();
                this.restorePreviewScroll(scroll);
            });
        },

        tryPatchPreviewSections(result, focusSection = null) {
            const doc = this.$refs.previewFrame?.contentDocument;

            if (! doc || ! this.previewFrameReady || ! result?.changedSections?.length) {
                return false;
            }

            const sectionsToPatch = focusSection
                ? result.changedSections.filter((key) => previewSectionMatchesFocus(key, focusSection))
                : result.changedSections;

            if (! sectionsToPatch.length) {
                return false;
            }

            const patchResult = applyPreviewPatches(doc, {
                sectionHtml: result.sectionHtml,
                changedSections: sectionsToPatch,
                personalDetails: this.document.personal_details,
                settings: result.settings,
            });

            return patchResult.ok;
        },

        ensureDocumentSections() {
            if (! this.document.personal_details || typeof this.document.personal_details !== 'object') {
                this.document.personal_details = {};
            }

            const repeaters = [
                'experience',
                'education',
                'skills',
                'languages',
                'projects',
                'certifications',
                'references',
                'custom_sections',
            ];

            repeaters.forEach((key) => {
                if (! Array.isArray(this.document[key])) {
                    this.document[key] = [];
                }
            });
        },

        refreshPreview(focusSection = null) {
            const startedAt = performance.now();

            const result = this.previewRenderer.build(
                this.document,
                this.sections,
                this.activeTemplate,
                this.templateCatalog,
                { focusSection },
            );

            if (result.layoutChanged || ! this.previewFrameReady) {
                this.updatePreviewFrame(result.html, { preserveScroll: this.previewFrameReady && ! result.layoutChanged });
                perfLog('PREVIEW_RENDER_TIME', startedAt);
                this.broadcastPreviewState();

                return;
            }

            const doc = this.$refs.previewFrame?.contentDocument;
            const domSectionCount = doc ? doc.querySelectorAll('[data-section]').length : 0;
            const visibleSectionCount = Object.keys(result.sectionHtml).filter((key) => {
                return key !== '__sidebar_photo' && String(result.sectionHtml[key] ?? '').trim() !== '';
            }).length;
            const structureChanged = domSectionCount !== visibleSectionCount;

            if (structureChanged && this.patchPreviewLayout(result)) {
                this.finishPreviewUpdate(result);
                perfLog('PREVIEW_RENDER_TIME', startedAt);
                this.broadcastPreviewState();

                return;
            }

            if (this.tryPatchPreviewSections(result, focusSection)) {
                this.finishPreviewUpdate(result);
                perfLog('PREVIEW_RENDER_TIME', startedAt);
                this.broadcastPreviewState();

                return;
            }

            this.updatePreviewFrame(result.html, { preserveScroll: true });
            perfLog('PREVIEW_RENDER_TIME', startedAt);
            this.broadcastPreviewState();
        },

        flushPreviewNow() {
            clearTimeout(this.previewTimer);
            this.refreshPreview();
        },

        async flushAutosave() {
            clearTimeout(this.debounceTimer);

            if (! this.isDirty) {
                return;
            }

            await this.autosave();
        },

        flushAutosaveKeepalive() {
            if (! this.isDirty || ! this.urls.autosave) {
                return;
            }

            clearTimeout(this.debounceTimer);
            const payload = prepareAutosavePayload({
                title: this.title,
                document: this.document,
                activeTemplate: this.activeTemplate,
                templateCatalog: this.templateCatalog,
            });

            const token = document.head.querySelector('meta[name="csrf-token"]')?.content;

            fetch(this.urls.autosave, {
                method: 'PATCH',
                headers: {
                    'Content-Type': 'application/json',
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': token ?? '',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                body: JSON.stringify(payload),
                keepalive: true,
            }).catch(() => {});
        },

        async exportPdf() {
            if (! this.urls.pdfDownload || this.pdfExportStatus === 'generating') {
                return;
            }

            this.pdfExportStatus = 'generating';
            this.pdfExportError = null;
            const startedAt = performance.now();

            try {
                await downloadCvPdf({
                    pdfDownloadUrl: this.urls.pdfDownload,
                    previewFrame: this.$refs.previewFrame,
                    title: this.title,
                    slug: this.slug,
                    cvDocument: this.document,
                    activeTemplate: this.activeTemplate,
                    templateCatalog: this.templateCatalog,
                    previewHtmlFallback: this.previewHtml,
                    beforeExport: async () => {
                        clearTimeout(this.previewTimer);
                        clearTimeout(this.debounceTimer);

                        await this.$nextTick();

                        this.syncJsonState();
                        this.flushAutosaveKeepalive();

                        if (this.isDirty) {
                            await this.autosave();
                        }

                        this.flushPreviewNow();
                        await this.$nextTick();
                    },
                });

                this.pdfExportStatus = 'idle';
                perfLog('PDF_TIME', startedAt);
            } catch (error) {
                this.pdfExportStatus = 'error';
                perfLog('PDF_TIME', startedAt);
                this.pdfExportError = await resolveCvPdfDownloadError(error);
            }
        },

        async pollPdfExport(exportId, attempts = 60) {
            for (let attempt = 0; attempt < attempts; attempt += 1) {
                const { data } = await axios.get(this.pdfStatusUrl(exportId));
                const status = data.export?.status;

                if (status === 'completed' && data.export?.download_url) {
                    this.pdfExportStatus = 'idle';
                    window.location.href = data.export.download_url;

                    return;
                }

                if (status === 'failed') {
                    this.pdfExportStatus = 'error';
                    this.pdfExportError = data.export?.error_message ?? 'PDF generation failed.';

                    return;
                }

                await new Promise((resolve) => setTimeout(resolve, 2000));
            }

            this.pdfExportStatus = 'error';
            this.pdfExportError = 'PDF export timed out. Check Billing for status.';
        },

        async autosave() {
            if (! this.isDirty) {
                return;
            }

            if (this.autosaveInFlight) {
                return this.autosaveInFlight;
            }

            const startedAt = performance.now();
            this.saveStatus = 'saving';
            const payload = prepareAutosavePayload({
                title: this.title,
                document: this.document,
                activeTemplate: this.activeTemplate,
                templateCatalog: this.templateCatalog,
            });

            this.autosaveInFlight = axios.patch(this.urls.autosave, payload)
                .then(({ data }) => {
                    this.applyCvResponse(data.cv);
                    this.saveStatus = 'saved';
                    this.isDirty = false;
                    this.errors = {};
                    perfLog('AUTO_SAVE_TIME', startedAt);
                })
                .catch((error) => {
                    this.saveStatus = 'error';
                    this.errors = error.response?.data?.errors ?? {};
                    perfLog('AUTO_SAVE_TIME', startedAt);
                })
                .finally(() => {
                    this.autosaveInFlight = null;
                });

            return this.autosaveInFlight;
        },

        async save(publish = false) {
            clearTimeout(this.debounceTimer);
            clearTimeout(this.previewTimer);
            this.saveStatus = 'saving';
            this.syncJsonState();

            try {
                const payload = {
                    title: this.pdfSyncPayload.title,
                    json_data: this.pdfSyncPayload.json_data,
                    publish,
                };

                const { data } = await axios.put(this.urls.update, payload);

                this.applyCvResponse(data.cv);
                this.saveStatus = publish ? 'published' : 'saved';
                this.isDirty = false;
                this.errors = {};
                this.refreshPreview();
            } catch (error) {
                this.saveStatus = 'error';
                this.errors = error.response?.data?.errors ?? {};
            }
        },

        setActiveSection(key) {
            this.activeSection = key;
            this.document.meta.last_edited_section = key;
        },

        isSectionEnabled(key) {
            return this.document.meta.enabled_sections.includes(key);
        },

        adminEnabledSections() {
            return templateEnabledSections(this.activeTemplate, this.templateCatalog, this.document);
        },

        showPersonalInfoSection() {
            return isPersonalInfoEnabledInTemplate(this.adminEnabledSections());
        },

        showAboutSection() {
            return isAboutEnabledInTemplate(this.adminEnabledSections());
        },

        showBuilderSection(key) {
            return isBuilderSectionInTemplate(key, this.adminEnabledSections());
        },

        showProfileImageSection() {
            return isProfilePhotoEnabledInTemplate(this.activeTemplate, this.templateCatalog, this.document)
                && this.showPersonalInfoSection();
        },

        profilePhotoUrl() {
            return this.document.personal_details?.photo_preview ?? '';
        },

        profilePhotoPositionHint() {
            return `Displayed in preview: ${profilePhotoPositionLabel(this.activeTemplate, this.templateCatalog, this.document)}`;
        },

        removeProfilePhoto() {
            this.document.personal_details.photo_preview = '';
            this.previewRenderer.invalidateAll();
            this.scheduleChange('personal_details');
        },

        toggleSection(key) {
            const enabled = this.document.meta.enabled_sections;
            const index = enabled.indexOf(key);

            if (index >= 0) {
                enabled.splice(index, 1);
            } else {
                enabled.push(key);
            }

            this.previewRenderer.invalidateAll();
            this.pendingPreviewSection = key;
            this.scheduleChange(key);
        },

        addItem(sectionKey) {
            const template = clone(this.templates[sectionKey] ?? {});
            template.id = crypto.randomUUID();
            this.document[sectionKey].push(template);
            this.scheduleChange(sectionKey);
        },

        removeItem(sectionKey, index) {
            this.document[sectionKey].splice(index, 1);
            this.scheduleChange(sectionKey);
        },

        addCustomSection() {
            const section = clone(this.templates.custom_sections ?? {});
            section.id = crypto.randomUUID();
            section.items = [];
            this.document.custom_sections.push(section);
            this.scheduleChange('custom_sections');
        },

        removeCustomSection(index) {
            this.document.custom_sections.splice(index, 1);
            this.scheduleChange('custom_sections');
        },

        addCustomItem(sectionIndex) {
            const item = clone(this.templates.custom_section_item ?? {});
            item.id = crypto.randomUUID();
            this.document.custom_sections[sectionIndex].items.push(item);
            this.scheduleChange('custom_sections');
        },

        removeCustomItem(sectionIndex, itemIndex) {
            this.document.custom_sections[sectionIndex].items.splice(itemIndex, 1);
            this.scheduleChange('custom_sections');
        },

        saveStatusLabel() {
            return {
                idle: 'All changes saved',
                pending: 'Unsaved changes',
                saving: 'Saving draft...',
                saved: 'Draft saved',
                published: 'Published',
                error: 'Save failed',
            }[this.saveStatus] ?? '';
        },

        orderedSections() {
            return (this.document.meta.section_order ?? []).filter((key) => this.sections[key]);
        },

        previewSections() {
            return this.orderedSections().filter((key) => this.isSectionEnabled(key));
        },
    }));
}
