<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')
    @include('web.partials.fontawesome')

    <title>{{ $coverLetter->title }} | Cover Letter Builder | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cover-letter-builder-page bg-cream font-sans antialiased text-body" style="font-family:'DM Sans',sans-serif">
    <div x-data="coverLetterBuilder(@js($builder))" class="flex h-screen flex-col">
        {{-- Toolbar --}}
        <header class="flex shrink-0 items-center justify-between gap-4 border-b border-line bg-white px-4 py-3 sm:px-6">
            <div class="flex min-w-0 items-center gap-3">
                <a href="{{ route('dashboard.cover-letters') }}" class="inline-flex shrink-0 items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-muted hover:bg-cream">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                    <span class="hidden sm:inline">Cover Letters</span>
                </a>
                <div class="hidden h-6 w-px bg-line sm:block"></div>
                <input
                    type="text"
                    x-model="title"
                    @input="onInput()"
                    class="min-w-0 max-w-xs truncate rounded-lg border border-transparent bg-transparent px-2 py-1 text-sm font-semibold text-navy hover:border-line focus:border-brand-400 focus:bg-white focus:outline-none"
                />
            </div>

            <div class="flex items-center gap-2">
                <span class="hidden text-xs text-muted sm:inline" x-text="saveStatus === 'saving' ? 'Saving…' : (saveStatus === 'saved' ? 'Saved' : (saveStatus === 'error' ? 'Save failed' : ''))"></span>

                <button
                    type="button"
                    @click="downloadPdf()"
                    :disabled="pdfStatus === 'loading'"
                    class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-sm font-medium text-navy transition hover:bg-cream disabled:opacity-60"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                    </svg>
                    <span x-text="pdfStatus === 'loading' ? 'Preparing…' : 'Download PDF'"></span>
                </button>

                <button
                    type="button"
                    @click="save()"
                    class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-brand-700"
                >
                    Save
                </button>
            </div>
        </header>

        <div class="flex min-h-0 flex-1 flex-col lg:flex-row">
            {{-- Form --}}
            <section class="min-h-0 w-full overflow-y-auto border-b border-line bg-white p-4 sm:p-6 lg:w-[440px] lg:border-b-0 lg:border-r">
                {{-- AI generation --}}
                <div class="rounded-2xl border border-brand-200 bg-brand-50/60 p-4">
                    <div class="flex items-center justify-between gap-2">
                        <h2 class="text-sm font-bold text-navy">Generate with AI</h2>
                        <span class="text-xs text-muted" x-text="`${aiUsage.remaining}/${aiUsage.limit} credits`"></span>
                    </div>
                    <p class="mt-1 text-xs text-muted">Uses your selected CV plus the company &amp; role below.</p>

                    <label class="mt-3 block text-xs font-semibold text-navy">Job description (optional)</label>
                    <textarea
                        x-model="jobDescription"
                        rows="3"
                        placeholder="Paste the job description to tailor the letter…"
                        class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none"
                    ></textarea>

                    <div class="mt-3 flex items-center gap-2">
                        <select x-model="tone" class="rounded-lg border border-line px-2 py-2 text-xs focus:border-brand-400 focus:outline-none">
                            <option value="professional">Professional</option>
                            <option value="enthusiastic">Enthusiastic</option>
                            <option value="formal">Formal</option>
                            <option value="friendly">Friendly</option>
                        </select>
                        <button
                            type="button"
                            @click="generateWithAi()"
                            :disabled="aiLoading"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-lg bg-navy px-3 py-2 text-sm font-semibold text-white transition hover:bg-navy/90 disabled:cursor-not-allowed disabled:opacity-60"
                        >
                            <svg x-show="!aiLoading" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                            </svg>
                            <svg x-show="aiLoading" class="h-4 w-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                            </svg>
                            <span x-text="aiLoading ? 'Writing…' : 'Generate'"></span>
                        </button>
                    </div>
                    <p x-show="aiError" x-cloak x-text="aiError" class="mt-2 text-xs text-red-600" role="alert"></p>
                </div>

                {{-- CV source --}}
                <template x-if="cv">
                    <div x-data="{ open: false }" class="mt-4 rounded-2xl border border-line bg-cream/40 p-4">
                        <button type="button" @click="open = !open" class="flex w-full items-center justify-between text-left">
                            <span class="text-sm font-bold text-navy">Linked CV: <span x-text="cv.cv_title"></span></span>
                            <svg class="h-4 w-4 text-muted transition" :class="open && 'rotate-180'" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div x-show="open" class="mt-3 space-y-2 text-xs text-muted">
                            <p><span class="font-semibold text-navy">Name:</span> <span x-text="cv.full_name || '—'"></span></p>
                            <p><span class="font-semibold text-navy">Title:</span> <span x-text="cv.professional_title || '—'"></span></p>
                            <p x-show="cv.summary"><span class="font-semibold text-navy">Summary:</span> <span x-text="cv.summary"></span></p>
                            <p x-show="cv.skills && cv.skills.length"><span class="font-semibold text-navy">Skills:</span> <span x-text="(cv.skills || []).join(', ')"></span></p>
                        </div>
                    </div>
                </template>

                {{-- Template --}}
                <div class="mt-6">
                    <label class="block text-xs font-semibold uppercase tracking-wide text-muted">Template</label>
                    <select
                        x-model="template"
                        @change="onTemplateChange()"
                        class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none"
                    >
                        <template x-for="tpl in templates" :key="tpl.key">
                            <option :value="tpl.key" x-text="tpl.name"></option>
                        </template>
                    </select>
                </div>

                {{-- Recipient --}}
                <h3 class="mt-6 text-xs font-semibold uppercase tracking-wide text-muted">Recipient</h3>
                <div class="mt-2 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-navy">Company name</label>
                        <input type="text" x-model="fields.company_name" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Job title</label>
                        <input type="text" x-model="fields.job_title" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Hiring manager</label>
                        <input type="text" x-model="fields.hiring_manager_name" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Company address</label>
                        <div
                            class="cv-rich-text-field mt-1"
                            data-cover-letter-field="company_address"
                            x-data="richTextField({
                                get: () => fields.company_address,
                                set: (value) => { fields.company_address = value; },
                                minHeight: '90px',
                            })"
                            @cover-letter-ai-applied.window="
                                $nextTick(() => {
                                    const key = $el.dataset.coverLetterField;
                                    if (key && typeof setEditorHtml === 'function') {
                                        setEditorHtml($event.detail[key] ?? '');
                                    }
                                })
                            "
                            @input="onInput()"
                        >
                            <div x-ref="editor" data-rich-text-mount></div>
                        </div>
                    </div>
                </div>

                {{-- Content --}}
                <h3 class="mt-6 text-xs font-semibold uppercase tracking-wide text-muted">Letter content</h3>
                <div class="mt-2 space-y-3">
                    <div>
                        <label class="block text-xs font-medium text-navy">Subject</label>
                        <input type="text" x-model="fields.subject" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Greeting</label>
                        <input type="text" x-model="fields.greeting" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Introduction</label>
                        <div
                            class="cv-rich-text-field mt-1"
                            data-cover-letter-field="introduction"
                            x-data="richTextField({
                                get: () => fields.introduction,
                                set: (value) => { fields.introduction = value; },
                                minHeight: '120px',
                            })"
                            @cover-letter-ai-applied.window="
                                $nextTick(() => {
                                    const key = $el.dataset.coverLetterField;
                                    if (key && typeof setEditorHtml === 'function') {
                                        setEditorHtml($event.detail[key] ?? '');
                                    }
                                })
                            "
                            @input="onInput()"
                        >
                            <div x-ref="editor" data-rich-text-mount></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Body</label>
                        <div
                            class="cv-rich-text-field mt-1"
                            data-cover-letter-field="body"
                            x-data="richTextField({
                                get: () => fields.body,
                                set: (value) => { fields.body = value; },
                                minHeight: '220px',
                            })"
                            @cover-letter-ai-applied.window="
                                $nextTick(() => {
                                    const key = $el.dataset.coverLetterField;
                                    if (key && typeof setEditorHtml === 'function') {
                                        setEditorHtml($event.detail[key] ?? '');
                                    }
                                })
                            "
                            @input="onInput()"
                        >
                            <div x-ref="editor" data-rich-text-mount></div>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Closing</label>
                        <input type="text" x-model="fields.closing_paragraph" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-navy">Signature name</label>
                        <input type="text" x-model="fields.signature_name" @input="onInput()" class="mt-1 w-full rounded-lg border border-line px-3 py-2 text-sm focus:border-brand-400 focus:outline-none">
                    </div>
                </div>

                <p x-show="pdfError" x-text="pdfError" class="mt-4 text-xs text-red-600"></p>
            </section>

            {{-- Live preview --}}
            <section x-ref="previewPanel" class="relative min-h-0 flex-1 overflow-auto bg-slate-200 p-4 sm:p-8 [overflow-anchor:none]">
                <div class="mx-auto w-[794px] max-w-full origin-top shadow-2xl">
                    <iframe
                        x-ref="preview"
                        title="Cover letter preview"
                        class="block h-[1123px] w-[794px] max-w-full border-0 bg-white"
                    ></iframe>
                </div>
            </section>
        </div>
    </div>

    <x-ai-upgrade-modal :default-url="route('dashboard.subscription')" />
</body>
</html>
