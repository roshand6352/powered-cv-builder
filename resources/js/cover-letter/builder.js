import axios from 'axios';
import Alpine from 'alpinejs';
import { prepareEditorHtml } from '../shared/rich-text-sanitize';

const CONTENT_FIELDS = [
    'company_name',
    'job_title',
    'hiring_manager_name',
    'company_address',
    'subject',
    'greeting',
    'introduction',
    'body',
    'closing_paragraph',
    'signature_name',
];

export function registerCoverLetterBuilder() {
    Alpine.data('coverLetterBuilder', (payload = {}) => ({
        urls: payload.urls ?? {},
        cv: payload.cv ?? null,
        templates: payload.templates ?? [],
        aiUsage: payload.aiUsage ?? { used: 0, limit: 0, remaining: 0 },
        hasAiAccess: payload.hasAiAccess ?? true,

        title: payload.coverLetter?.title ?? '',
        template: payload.template?.slug ?? '',
        fields: { ...emptyFields(), ...(payload.coverLetter?.fields ?? {}) },

        jobDescription: '',
        tone: 'professional',

        saveStatus: 'idle',
        saveError: null,
        isDirty: false,
        previewLoading: false,
        aiLoading: false,
        aiError: null,
        pdfStatus: 'idle',
        pdfError: null,

        previewTimer: null,
        autosaveTimer: null,

        init() {
            this.refreshPreview();
        },

        onInput() {
            this.isDirty = true;
            this.schedulePreview();
            this.scheduleAutosave();
        },

        onTemplateChange() {
            this.isDirty = true;
            this.refreshPreview();
            this.scheduleAutosave();
        },

        renderPayload() {
            return {
                ...this.collectFields(),
                template: this.template,
            };
        },

        collectFields() {
            const data = { title: this.title };
            CONTENT_FIELDS.forEach((key) => {
                data[key] = this.fields[key] ?? '';
            });
            return data;
        },

        schedulePreview() {
            clearTimeout(this.previewTimer);
            this.previewTimer = setTimeout(() => this.refreshPreview(), 300);
        },

        async refreshPreview() {
            if (! this.urls.render) {
                return;
            }

            this.previewLoading = true;

            try {
                const { data } = await axios.post(this.urls.render, this.renderPayload(), {
                    headers: { Accept: 'text/html' },
                    responseType: 'text',
                });

                const frame = this.$refs.preview;
                if (frame) {
                    frame.srcdoc = data;
                }
            } catch (error) {
                // Preview failures are non-blocking.
            } finally {
                this.previewLoading = false;
            }
        },

        scheduleAutosave() {
            clearTimeout(this.autosaveTimer);
            this.autosaveTimer = setTimeout(() => this.autosave(), 2500);
        },

        async autosave() {
            if (! this.isDirty || ! this.urls.autosave) {
                return;
            }

            this.saveStatus = 'saving';

            try {
                await axios.patch(this.urls.autosave, { ...this.collectFields(), template: this.template });
                this.isDirty = false;
                this.saveStatus = 'saved';
            } catch (error) {
                this.saveStatus = 'error';
                this.saveError = error.response?.data?.message ?? 'Autosave failed.';
            }
        },

        async save() {
            if (! this.urls.save) {
                return;
            }

            this.saveStatus = 'saving';
            this.saveError = null;

            try {
                await axios.put(this.urls.save, {
                    ...this.collectFields(),
                    template: this.template,
                    status: 'published',
                });
                this.isDirty = false;
                this.saveStatus = 'saved';
            } catch (error) {
                this.saveStatus = 'error';
                this.saveError = error.response?.data?.message ?? 'Unable to save cover letter.';
            }
        },

        aiLogUrl(logId) {
            return (this.urls.aiLog ?? '').replace('__ID__', logId);
        },

        async generateWithAi() {
            if (this.aiLoading || ! this.urls.ai) {
                return;
            }

            if (! this.hasAiAccess) {
                this.aiError = 'AI generation is available on paid plans only.';
                return;
            }

            if (! this.fields.company_name?.trim() || ! this.fields.job_title?.trim()) {
                this.aiError = 'Add a company name and job title first.';
                return;
            }

            this.aiLoading = true;
            this.aiError = null;

            try {
                const { data } = await axios.post(this.urls.ai, {
                    company_name: this.fields.company_name,
                    job_title: this.fields.job_title,
                    job_description: this.jobDescription,
                    tone: this.tone,
                });

                if (data.usage) {
                    this.aiUsage = data.usage;
                }

                let log = data.log;

                if (! log?.id) {
                    throw new Error(data.message ?? 'AI generation failed.');
                }

                if (log.status !== 'completed' && log.status !== 'failed') {
                    log = await this.pollAiLog(log.id);
                }

                if (log?.status === 'failed') {
                    throw new Error(log.error_message ?? 'AI generation failed.');
                }

                this.applyAiResult(log?.response?.cover_letter);
                this.isDirty = true;
                this.refreshPreview();
                this.scheduleAutosave();
            } catch (error) {
                const validationErrors = error.response?.data?.errors ?? {};
                const firstValidationMessage = Object.values(validationErrors)
                    .flat()
                    .find((message) => typeof message === 'string');

                this.aiError = firstValidationMessage
                    ?? error.response?.data?.message
                    ?? error.message
                    ?? 'AI generation failed.';

                if (error.response?.data?.usage) {
                    this.aiUsage = error.response.data.usage;
                }
            } finally {
                this.aiLoading = false;
            }
        },

        async pollAiLog(logId, attempts = 60) {
            for (let attempt = 0; attempt < attempts; attempt += 1) {
                const { data } = await axios.get(this.aiLogUrl(logId));
                const log = data.log;

                if (log?.status === 'completed' || log?.status === 'failed') {
                    if (data.usage) {
                        this.aiUsage = data.usage;
                    }
                    return log;
                }

                await new Promise((resolve) => setTimeout(resolve, 2000));
            }

            throw new Error('AI request timed out.');
        },

        applyAiResult(result) {
            if (! result) {
                return;
            }

            const map = {
                subject: 'subject',
                greeting: 'greeting',
                introduction: 'introduction',
                body: 'body',
                closing_paragraph: 'closing_paragraph',
                signature: 'signature_name',
            };

            const richTextTargets = new Set(['introduction', 'body', 'company_address']);

            Object.entries(map).forEach(([source, target]) => {
                if (result[source] === undefined || result[source] === null || `${result[source]}`.trim() === '') {
                    return;
                }

                const value = `${result[source]}`;

                this.fields[target] = richTextTargets.has(target)
                    ? prepareEditorHtml(value)
                    : value;
            });

            this.$nextTick(() => {
                window.dispatchEvent(new CustomEvent('cover-letter-ai-applied', {
                    detail: { ...this.fields },
                }));
            });
        },

        async downloadPdf() {
            if (this.pdfStatus === 'loading' || ! this.urls.pdfDownload) {
                return;
            }

            this.pdfStatus = 'loading';
            this.pdfError = null;

            try {
                // Persist current state so the exported PDF matches the preview.
                await axios.put(this.urls.save, { ...this.collectFields(), template: this.template });
                this.isDirty = false;

                const response = await axios.post(this.urls.pdfDownload, {}, { responseType: 'blob' });

                const blob = new Blob([response.data], { type: 'application/pdf' });
                const url = window.URL.createObjectURL(blob);
                const link = document.createElement('a');
                link.href = url;
                link.download = `${slugify(this.title) || 'cover-letter'}.pdf`;
                document.body.appendChild(link);
                link.click();
                link.remove();
                window.URL.revokeObjectURL(url);

                this.pdfStatus = 'idle';
            } catch (error) {
                this.pdfStatus = 'error';
                this.pdfError = error.response?.data?.message ?? 'PDF download failed.';
            }
        },
    }));
}

function emptyFields() {
    const data = {};
    CONTENT_FIELDS.forEach((key) => {
        data[key] = '';
    });
    return data;
}

function slugify(value) {
    return `${value ?? ''}`
        .toLowerCase()
        .replace(/[^a-z0-9]+/g, '-')
        .replace(/^-+|-+$/g, '');
}
