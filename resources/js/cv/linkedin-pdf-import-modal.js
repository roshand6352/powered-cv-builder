export function registerLinkedinPdfImportModal() {
    window.Alpine.data('linkedinPdfImportModal', (config = {}) => ({
        open: Boolean(config.open),
        processing: Boolean(config.processing),
        importStatus: config.importStatus ?? null,
        currentStep: config.currentStep ?? null,
        importMessage: config.importMessage ?? null,
        statusUrl: config.statusUrl ?? '',
        continueUrl: config.continueUrl ?? '',
        consumeUrl: config.consumeUrl ?? '',
        pollTimer: null,
        pollAttempts: 0,
        maxPollAttempts: 150,
        consecutiveEmptyStatuses: 0,
        redirectingToTemplates: false,
        selectedFileName: '',
        clientError: config.clientError ?? '',
        stages: [
            { key: 'uploading_pdf', label: 'Uploading PDF...' },
            { key: 'reading_linkedin_profile', label: 'Reading LinkedIn Profile...' },
            { key: 'extracting_information', label: 'Extracting Information...' },
            { key: 'analyzing_profile', label: 'Analyzing Profile...' },
            { key: 'preparing_cv', label: 'Preparing CV...' },
            { key: 'almost_done', label: 'Almost Done...' },
        ],

        init() {
            if (this.processing) {
                this.startPolling();
            }

            // One-time redirect only while server reports state=completed.
            // After continue, state becomes ready_for_builder and statusForClient returns null.
            if (this.importStatus === 'completed') {
                this.redirectToTemplateSelection();
            }
        },

        openModal() {
            this.processing = false;
            this.importStatus = null;
            this.currentStep = null;
            this.importMessage = null;
            this.clientError = '';
            this.pollAttempts = 0;
            this.consecutiveEmptyStatuses = 0;
            this.redirectingToTemplates = false;
            this.open = true;
        },

        closeModal() {
            const hadFailure = this.importStatus === 'failed';

            this.open = false;
            this.processing = false;
            clearTimeout(this.pollTimer);
            this.selectedFileName = '';
            this.clientError = '';
            this.importStatus = null;
            this.importMessage = null;
            this.currentStep = null;

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }

            if (hadFailure) {
                this.consumeFailedImportStatus();
            }
        },

        markPollingFailed(message) {
            clearTimeout(this.pollTimer);
            this.processing = false;
            this.importStatus = 'failed';
            this.importMessage = message;
            this.open = true;
            this.consumeFailedImportStatus();
        },

        async consumeFailedImportStatus() {
            if (! this.consumeUrl) {
                return;
            }

            try {
                await fetch(this.consumeUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    credentials: 'same-origin',
                });
            } catch (error) {
                // Local UI already cleared; ignore consume failures.
            }
        },

        csrfToken() {
            return document.querySelector('meta[name=csrf-token]')?.getAttribute('content') ?? '';
        },

        async redirectToTemplateSelection() {
            if (this.redirectingToTemplates || ! this.continueUrl) {
                return;
            }

            this.redirectingToTemplates = true;
            this.processing = false;
            this.importStatus = 'completed';
            this.open = true;

            try {
                const response = await fetch(this.continueUrl, {
                    method: 'POST',
                    headers: {
                        Accept: 'application/json',
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': this.csrfToken(),
                    },
                    credentials: 'same-origin',
                });

                const data = await response.json().catch(() => ({}));

                if (! response.ok || ! data.redirect) {
                    throw new Error(data.message || 'Unable to open template selection.');
                }

                // Local UI no longer treats this as a redirectable completed import.
                this.importStatus = null;
                this.processing = false;
                this.open = false;

                window.location.assign(data.redirect);
            } catch (error) {
                this.redirectingToTemplates = false;
                this.markPollingFailed(error?.message || 'Unable to open template selection. Please try again.');
            }
        },

        startPolling() {
            clearTimeout(this.pollTimer);
            this.pollAttempts = 0;
            this.consecutiveEmptyStatuses = 0;
            this.pollStatus();
        },

        async pollStatus() {
            if (! this.statusUrl) {
                return;
            }

            this.pollAttempts += 1;

            if (this.pollAttempts > this.maxPollAttempts) {
                this.markPollingFailed('Import is taking longer than expected. Please try again or enter your CV manually.');

                return;
            }

            try {
                const response = await fetch(this.statusUrl, {
                    headers: {
                        Accept: 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                    },
                });

                if (! response.ok) {
                    throw new Error('Status request failed');
                }

                const data = await response.json();
                const status = data.import ?? null;

                if (! status) {
                    this.consecutiveEmptyStatuses += 1;

                    if (this.consecutiveEmptyStatuses >= 5) {
                        this.markPollingFailed('We could not track the LinkedIn PDF import status. Please try again or enter your CV manually.');

                        return;
                    }

                    this.pollTimer = setTimeout(() => this.pollStatus(), 1500);

                    return;
                }

                this.consecutiveEmptyStatuses = 0;
                this.importStatus = status.state ?? null;
                this.currentStep = status.step ?? this.currentStep;
                this.importMessage = status.message ?? null;

                if (status.state === 'processing') {
                    this.processing = true;
                    this.pollTimer = setTimeout(() => this.pollStatus(), 1200);

                    return;
                }

                this.processing = false;
                clearTimeout(this.pollTimer);

                if (status.state === 'completed') {
                    this.redirectToTemplateSelection();

                    return;
                }

                if (status.state === 'failed') {
                    this.open = true;
                    // One-shot: clear cached failure so refresh cannot reopen the modal.
                    this.consumeFailedImportStatus();
                }
            } catch (error) {
                if (this.pollAttempts >= this.maxPollAttempts) {
                    this.markPollingFailed('We could not finish checking your LinkedIn PDF import. Please try again or enter your CV manually.');

                    return;
                }

                this.pollTimer = setTimeout(() => this.pollStatus(), 2000);
            }
        },

        stageState(stepKey) {
            const currentIndex = this.stages.findIndex((stage) => stage.key === this.currentStep);
            const targetIndex = this.stages.findIndex((stage) => stage.key === stepKey);

            if (this.importStatus === 'completed') {
                return 'completed';
            }

            if (this.importStatus === 'failed') {
                if (currentIndex === -1 || targetIndex > currentIndex) {
                    return 'pending';
                }

                return targetIndex === currentIndex ? 'current' : 'completed';
            }

            if (currentIndex === -1) {
                return targetIndex === 0 ? 'current' : 'pending';
            }

            if (targetIndex < currentIndex) {
                return 'completed';
            }

            if (targetIndex === currentIndex) {
                return 'current';
            }

            return 'pending';
        },

        handleFileSelection(event) {
            this.selectedFileName = event.target.files?.[0]?.name ?? '';
            this.validateFile();
        },

        validateFile() {
            const file = this.$refs.fileInput?.files?.[0] ?? null;

            if (! file) {
                this.clientError = 'Please choose a LinkedIn profile PDF to upload.';

                return false;
            }

            const fileName = (file.name ?? '').toLowerCase();
            const mimeType = file.type ?? '';
            const isPdf = fileName.endsWith('.pdf') || mimeType === 'application/pdf';

            if (! isPdf) {
                this.clientError = 'Only PDF files are accepted.';

                return false;
            }

            if (file.size > 5 * 1024 * 1024) {
                this.clientError = 'The PDF may not be larger than 5 MB.';

                return false;
            }

            this.clientError = '';

            return true;
        },

        submitForm() {
            if (! this.validateFile()) {
                return;
            }

            this.processing = true;
            this.importStatus = 'processing';
            this.currentStep = 'uploading_pdf';
            this.$refs.form.submit();
        },

        resetForRetry() {
            this.consumeFailedImportStatus();
            this.importStatus = null;
            this.importMessage = null;
            this.currentStep = null;
            this.processing = false;
            this.clientError = '';
            this.selectedFileName = '';

            if (this.$refs.fileInput) {
                this.$refs.fileInput.value = '';
            }

            this.open = true;
        },
    }));
}
