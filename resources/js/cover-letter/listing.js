import axios from 'axios';
import Alpine from 'alpinejs';
import { downloadCoverLetterPdf } from './pdf-download';
import { cvPdfReadySheet } from '../cv/pdf-download';

/**
 * Cover Letters listing page (/dashboard/cover-letters):
 * - per-row "Download PDF" with loading, friendly error and Retry
 * - "Create Cover Letter" CV picker modal
 */
export function registerCoverLetterListing() {
    Alpine.data('coverLetterPdfDownload', (config = {}) => ({
        url: config.url ?? '',
        filename: config.filename ?? 'cover-letter.pdf',
        status: config.initialError ? 'error' : 'idle',
        error: config.initialError ?? null,
        ...cvPdfReadySheet(),

        async download() {
            if (this.status === 'loading' || ! this.url) {
                return;
            }

            this.status = 'loading';
            this.error = null;

            try {
                const result = await downloadCoverLetterPdf(this.url, this.filename);
                this.status = 'idle';

                if (result.needsUserAction) {
                    this.showPdfReady(result.file);
                }
            } catch (error) {
                this.status = 'error';
                this.error = error.message;
            }
        },
    }));

    Alpine.data('coverLetterCreateModal', (config = {}) => ({
        optionsUrl: config.optionsUrl ?? '',
        createCvUrl: config.createCvUrl ?? '',
        open: false,
        state: 'idle', // idle | loading | ready | error
        cvs: [],
        search: '',
        selectedId: null,
        navigating: false,

        get filtered() {
            const term = this.search.trim().toLowerCase();

            if (! term) {
                return this.cvs;
            }

            return this.cvs.filter((cv) => [cv.title, cv.full_name, cv.professional_title, cv.email, cv.location, cv.template]
                .filter(Boolean)
                .some((value) => `${value}`.toLowerCase().includes(term)));
        },

        get selected() {
            return this.cvs.find((cv) => cv.id === this.selectedId) ?? null;
        },

        show() {
            this.open = true;
            this.search = '';
            this.selectedId = null;
            this.navigating = false;
            document.body.classList.add('overflow-hidden');
            this.load();
            this.$nextTick(() => this.$refs.search?.focus());
        },

        close() {
            if (this.navigating) {
                return;
            }

            this.open = false;
            document.body.classList.remove('overflow-hidden');
        },

        async load() {
            if (! this.optionsUrl) {
                this.state = 'error';
                return;
            }

            this.state = 'loading';

            try {
                const { data } = await axios.get(this.optionsUrl, { headers: { Accept: 'application/json' } });
                this.cvs = Array.isArray(data?.cvs) ? data.cvs : [];
                this.createCvUrl = data?.links?.create_cv ?? this.createCvUrl;
                this.state = 'ready';
            } catch {
                this.cvs = [];
                this.state = 'error';
            }
        },

        select(cv) {
            this.selectedId = cv.id;
        },

        proceed() {
            const cv = this.selected;

            if (! cv?.continue_url || this.navigating) {
                return;
            }

            this.navigating = true;
            window.location.assign(cv.continue_url);
        },
    }));
}