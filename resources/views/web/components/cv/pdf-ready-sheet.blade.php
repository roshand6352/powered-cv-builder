@props(['id' => 'cv-pdf-ready-title'])

<div
    x-show="pdfReadyName"
    x-cloak
    x-transition.opacity
    class="fixed inset-0 z-50 flex items-end justify-center bg-slate-900/50 p-4 text-left sm:items-center"
    role="dialog"
    aria-modal="true"
    aria-labelledby="{{ $id }}"
    @keydown.escape.window="closePdfReady()"
>
    <div class="w-full max-w-sm rounded-2xl bg-white p-5 shadow-xl" @click.outside="closePdfReady()">
        <div class="flex items-start gap-3">
            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.75" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5A3.375 3.375 0 0010.125 2.25H6.75A2.25 2.25 0 004.5 4.5v15A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V14.25z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 2.625V6.75A1.125 1.125 0 0014.625 7.875H18.75" />
                </svg>
            </div>
            <div class="min-w-0 flex-1">
                <h2 id="{{ $id }}" class="text-base font-semibold text-slate-900">Your PDF is ready</h2>
                <p class="mt-1 truncate text-sm text-slate-600" x-text="pdfReadyName"></p>
            </div>
        </div>

        <div class="mt-5 grid gap-2">
            <button
                type="button"
                @click="sharePdfReadyFile()"
                class="inline-flex min-h-[2.75rem] items-center justify-center gap-2 rounded-lg bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 100 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186l9.566-5.314m-9.566 7.5l9.566 5.314m0 0a2.25 2.25 0 103.935 2.186 2.25 2.25 0 00-3.935-2.186zm0-12.814a2.25 2.25 0 103.933-2.185 2.25 2.25 0 00-3.933 2.185z" />
                </svg>
                Share PDF
            </button>
            <button
                type="button"
                @click="downloadPdfReadyFile()"
                class="inline-flex min-h-[2.75rem] items-center justify-center gap-2 rounded-lg border border-slate-300 px-4 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-50"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                </svg>
                Download PDF
            </button>
            <button
                type="button"
                @click="closePdfReady()"
                class="inline-flex min-h-[2.75rem] items-center justify-center rounded-lg px-4 py-2 text-sm font-medium text-slate-500 hover:text-slate-700"
            >
                Close
            </button>
        </div>
    </div>
</div>
