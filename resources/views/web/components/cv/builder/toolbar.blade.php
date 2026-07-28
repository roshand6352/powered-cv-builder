<header class="shrink-0 border-b border-slate-200 bg-white">
    <div class="flex min-h-16 flex-wrap items-center justify-between gap-x-3 gap-y-2 px-4 py-2 sm:px-6">
        <div class="flex min-w-0 items-center gap-3">
            <a
                href="{{ route('dashboard.cvs') }}"
                class="inline-flex shrink-0 items-center gap-2 rounded-lg px-2 py-1.5 text-sm font-medium text-slate-600 hover:bg-slate-100"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                </svg>
                <span class="hidden sm:inline">Back to CVs</span>
            </a>

            <div class="hidden h-6 w-px bg-slate-200 sm:block"></div>

            <input
                type="text"
                x-model="title"
                @input="markDirty()"
                class="min-w-0 flex-1 rounded-lg border-slate-300 text-sm font-semibold shadow-sm focus:border-brand-500 focus:ring-brand-500 sm:max-w-xs"
                placeholder="CV title"
            >
        </div>

        <div class="flex flex-wrap items-center justify-end gap-2 sm:gap-3">
            <span
                class="hidden text-xs font-medium sm:inline"
                :class="{
                    'text-slate-500': saveStatus === 'saved' || saveStatus === 'idle' || saveStatus === 'published',
                    'text-amber-600': saveStatus === 'pending' || saveStatus === 'saving',
                    'text-red-600': saveStatus === 'error',
                }"
                x-text="saveStatusLabel()"
            ></span>

            <span
                class="hidden rounded-full bg-violet-50 px-2.5 py-1 text-xs font-semibold text-violet-700 sm:inline"
                x-show="aiUsage.remaining !== null"
                x-text="`AI ${aiUsage.used}/${aiUsage.limit}`"
            ></span>

            <span
                class="rounded-full px-2.5 py-1 text-xs font-semibold capitalize"
                :class="status === 'published' ? 'bg-emerald-50 text-emerald-700' : 'bg-amber-50 text-amber-700'"
                x-text="cvStatusLabel()"
            ></span>

            <a
                :href="urls.templates"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-2 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:px-3"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75V19.5a2.25 2.25 0 002.25 2.25h10.5a2.25 2.25 0 002.25-2.25v-6.75m-13.5 0l6.75-6.75 6.75 6.75m-13.5 0h13.5" />
                </svg>
                <span class="hidden sm:inline">Change template</span>
            </a>

            <a
                :href="urls.livePreview"
                target="_blank"
                rel="noopener"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-2 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:px-3"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <span class="hidden sm:inline">Live Preview</span>
            </a>

            <button
                type="button"
                @click="exportPdf()"
                :disabled="pdfExportStatus === 'generating'"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                </svg>
                <span class="hidden sm:inline" x-show="pdfExportStatus !== 'generating'">Download PDF</span>
                <span class="hidden sm:inline" x-show="pdfExportStatus === 'generating'">Generating...</span>
            </button>

            <button
                type="button"
                @click="save(false)"
                class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-2 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 sm:px-3"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4.5 2.25M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="hidden sm:inline">Save Draft</span>
            </button>

            <button
                type="button"
                @click="save(true)"
                class="inline-flex items-center gap-2 rounded-lg bg-brand-600 px-2 py-2 text-sm font-semibold text-white hover:bg-brand-700 sm:px-3"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 8.25V6A2.25 2.25 0 0014.25 3.75h-9A2.25 2.25 0 003 6v9A2.25 2.25 0 005.25 17.25h2.25M9 14.25L21 2.25M21 2.25h-6.75M21 2.25v6.75" />
                </svg>
                <span class="hidden sm:inline">Publish</span>
            </button>
        </div>
    </div>
</header>
