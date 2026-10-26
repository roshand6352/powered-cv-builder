@php
    $linkedinImportStatus = auth()->check()
        ? app(\App\Services\Cv\LinkedInPdfImportService::class)->statusForPageLoad(auth()->user())
        : null;

    $linkedinImportModalConfig = [
        'open' => $errors->linkedinImport->isNotEmpty()
            || session('linkedin_pdf_import_state') === 'processing',
        'processing' => session('linkedin_pdf_import_state') === 'processing'
            || (($linkedinImportStatus['state'] ?? null) === 'processing'),
        'importStatus' => $linkedinImportStatus['state'] ?? null,
        'currentStep' => $linkedinImportStatus['step'] ?? null,
        'importMessage' => $linkedinImportStatus['message'] ?? null,
        'statusUrl' => route('linkedin-pdf-import.status'),
        'continueUrl' => route('linkedin-pdf-import.continue'),
        'consumeUrl' => route('linkedin-pdf-import.consume'),
        'clientError' => $errors->linkedinImport->first('linkedin_pdf') ?: '',
    ];
@endphp

<div
    x-data="linkedinPdfImportModal(@js($linkedinImportModalConfig))"
    x-on:open-linkedin-pdf-import-modal.window="openModal()"
    x-cloak
>
    <div
        x-show="open"
        class="fixed inset-0 z-50 overflow-y-auto px-4 py-6 sm:px-0"
        role="dialog"
        aria-modal="true"
        aria-labelledby="linkedin-import-title"
    >
        <div
            x-show="open"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-slate-900/50"
            @click="closeModal()"
        ></div>

        <div class="flex min-h-full items-center justify-center">
            <div
                x-show="open"
                x-transition:enter="ease-out duration-200"
                x-transition:enter-start="opacity-0 translate-y-2 sm:scale-95"
                x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave="ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                x-transition:leave-end="opacity-0 translate-y-2 sm:scale-95"
                @keydown.escape.window="open && closeModal()"
                class="relative w-full max-w-lg rounded-2xl border border-line bg-white p-6 shadow-xl"
            >
                <div class="flex items-start gap-3">
                    <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-700">
                        <svg x-show="!processing" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V14.25z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 2.625V6.75A1.125 1.125 0 0014.625 7.875H18.75" />
                        </svg>
                        <svg x-show="processing" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364 6.364-2.121-2.121M7.757 7.757 5.636 5.636m12.728 0-2.121 2.121M7.757 16.243l-2.121 2.121" />
                        </svg>
                    </span>
                    <div class="min-w-0">
                        <h2
                            id="linkedin-import-title"
                            class="text-lg font-semibold text-navy"
                            x-text="processing
                                ? 'Import Processing'
                                : (importStatus === 'failed'
                                    ? 'Import Failed'
                                    : (importStatus === 'completed' ? 'Import Ready' : 'Import LinkedIn Profile PDF'))"
                        ></h2>
                        <p x-show="!processing && !importStatus" class="mt-2 text-sm text-muted">
                            Upload your exported LinkedIn profile PDF to start importing its content into your CV.
                        </p>
                        <p x-show="processing" class="mt-2 text-sm text-muted">
                            Your LinkedIn PDF is being processed. We will update each step automatically while the queue job runs.
                        </p>
                        <p x-show="importStatus === 'completed'" class="mt-2 text-sm text-emerald-700">
                            Your LinkedIn profile is ready. Next, choose a CV template, then review the imported fields in the builder before saving.
                        </p>
                        <div
                            x-show="importStatus === 'failed'"
                            class="alert mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                            role="alert"
                        >
                            <p x-text="importMessage"></p>
                        </div>
                    </div>
                </div>

                <form
                    x-show="!processing && !importStatus"
                    x-ref="form"
                    method="POST"
                    action="{{ route('linkedin-pdf-import.store') }}"
                    enctype="multipart/form-data"
                    @submit.prevent="submitForm()"
                    novalidate
                >
                    @csrf

                    <div class="mt-6">
                        <label for="linkedin-profile-pdf" class="block text-sm font-medium text-slate-700">PDF file</label>
                        <input
                            x-ref="fileInput"
                            id="linkedin-profile-pdf"
                            name="linkedin_pdf"
                            type="file"
                            accept=".pdf,application/pdf"
                            required
                            @change="handleFileSelection($event)"
                            :aria-invalid="clientError ? 'true' : 'false'"
                            class="mt-2 block w-full min-w-0 max-w-full rounded-lg border bg-white px-3 py-2 text-sm text-slate-600 shadow-sm file:mr-3 file:max-w-[45%] file:truncate file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-semibold file:text-brand-700 hover:file:bg-brand-100"
                            :class="clientError ? 'border-red-300 focus:border-red-500 focus:ring-red-500' : 'border-slate-300 focus:border-brand-500 focus:ring-brand-500'"
                        >
                        <p class="mt-2 text-xs text-slate-500">Accepted format: PDF only. Maximum file size: 5 MB.</p>
                        <div
                            x-show="clientError"
                            class="alert mt-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
                            role="alert"
                        >
                            <p x-text="clientError"></p>
                        </div>
                        <p
                            x-show="selectedFileName && !clientError"
                            x-text="`Selected file: ${selectedFileName}`"
                            class="mt-3 break-all rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-sm text-slate-600"
                        ></p>
                    </div>

                    <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="dash-btn-secondary w-full justify-center px-4 py-2 sm:w-auto"
                        >
                            Cancel
                        </button>
                        <button
                            type="submit"
                            class="inline-flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700 sm:w-auto"
                        >
                            Upload &amp; Import
                        </button>
                    </div>
                </form>

                <div x-show="processing" class="mt-6 rounded-2xl border border-brand-100 bg-brand-50 px-4 py-5">
                    <div class="flex items-center gap-3">
                        <span class="spinner-border inline-block h-5 w-5 animate-spin rounded-full border-2 border-brand-600 border-r-transparent text-brand-600" aria-hidden="true"></span>
                        <div>
                            <p class="text-sm font-semibold text-navy">Processing your LinkedIn PDF</p>
                            <p class="mt-1 text-sm text-slate-600">Please keep this window open while we prepare your CV data.</p>
                        </div>
                    </div>

                    <div class="mt-5 space-y-3">
                        <template x-for="stage in stages" :key="stage.key">
                            <div class="flex items-center gap-3 rounded-xl border border-white/70 bg-white/70 px-3 py-3">
                                <span x-show="stageState(stage.key) === 'completed'" class="flex h-6 w-6 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                </span>
                                <span x-show="stageState(stage.key) === 'current'" class="spinner-border inline-block h-6 w-6 animate-spin rounded-full border-2 border-brand-600 border-r-transparent text-brand-600" aria-hidden="true"></span>
                                <span x-show="stageState(stage.key) === 'pending'" class="flex h-6 w-6 items-center justify-center rounded-full bg-slate-200 text-slate-500">
                                    <span class="h-2.5 w-2.5 rounded-full bg-current"></span>
                                </span>
                                <div class="min-w-0">
                                    <p
                                        x-text="stage.label"
                                        class="text-sm font-medium"
                                        :class="{
                                            'text-navy': stageState(stage.key) !== 'pending',
                                            'text-slate-500': stageState(stage.key) === 'pending',
                                        }"
                                    ></p>
                                </div>
                            </div>
                        </template>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button
                            type="button"
                            @click="closeModal()"
                            class="dash-btn-secondary px-4 py-2"
                        >
                            Close
                        </button>
                    </div>
                </div>

                <div x-show="importStatus === 'completed'" class="mt-6 rounded-2xl border border-emerald-100 bg-emerald-50 px-4 py-5">
                    <div class="flex items-start gap-3">
                        <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-emerald-100 text-emerald-700">
                            <svg x-show="!redirectingToTemplates" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <svg x-show="redirectingToTemplates" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor" aria-hidden="true">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v3m0 12v3m9-9h-3M6 12H3m15.364 6.364-2.121-2.121M7.757 7.757 5.636 5.636m12.728 0-2.121 2.121M7.757 16.243l-2.121 2.121" />
                            </svg>
                        </span>
                        <div>
                            <p class="text-sm font-semibold text-navy">Import complete</p>
                            <p class="mt-1 text-sm text-slate-600">
                                Taking you to template selection so you can choose a design before reviewing the imported CV.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex justify-end">
                        <button
                            type="button"
                            @click="redirectToTemplateSelection()"
                            :disabled="redirectingToTemplates"
                            class="inline-flex items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700 disabled:cursor-wait disabled:opacity-70"
                        >
                            <span x-text="redirectingToTemplates ? 'Opening templates...' : 'Choose a template'"></span>
                        </button>
                    </div>
                </div>

                <div x-show="importStatus === 'failed'" class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:justify-end">
                    <button
                        type="button"
                        @click="closeModal()"
                        class="dash-btn-secondary w-full justify-center px-4 py-2 sm:w-auto"
                    >
                        Close
                    </button>
                    <button
                        type="button"
                        @click="resetForRetry()"
                        class="inline-flex w-full items-center justify-center rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700 sm:w-auto"
                    >
                        Try Another PDF
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
