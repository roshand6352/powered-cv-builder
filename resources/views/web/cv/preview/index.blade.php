<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')
    @include('web.partials.fontawesome')

    <title>{{ $cv->title }} | Live Preview | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-100 font-sans antialiased text-slate-900" style="font-family:'DM Sans',sans-serif">
    <div
        x-data="cvLivePreview(@js($previewPayload))"
        class="flex min-h-screen flex-col"
    >
        <header class="sticky top-0 z-40 border-b border-slate-200 bg-white">
            <div class="mx-auto flex h-14 max-w-6xl items-center justify-between gap-4 px-4 sm:px-6">
                <div class="min-w-0">
                    <p class="truncate text-sm font-semibold text-slate-900" x-text="title"></p>
                    <p
                        class="text-xs"
                        :class="{
                            'text-slate-500': syncStatus === 'waiting' || syncStatus === 'polling',
                            'text-emerald-600': syncStatus === 'live',
                        }"
                        x-text="syncStatusLabel()"
                    ></p>
                </div>

                <div class="flex items-center gap-3">
                    <span
                        class="hidden text-xs text-slate-400 sm:inline"
                        x-show="lastUpdateAt"
                        x-text="lastUpdateLabel()"
                    ></span>

                    <button
                        type="button"
                        @click="exportPdf()"
                        :disabled="pdfExportStatus === 'generating' || ! previewFrameReady"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12M12 16.5V3" />
                        </svg>
                        <span x-show="pdfExportStatus !== 'generating'">Download PDF</span>
                        <span x-show="pdfExportStatus === 'generating'">Generating...</span>
                    </button>

                    <a
                        href="{{ route('cvs.builder', $cv) }}"
                        class="inline-flex items-center gap-2 rounded-lg border border-slate-300 px-3 py-2 text-sm font-semibold text-slate-700 hover:bg-slate-50"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z" />
                        </svg>
                        Edit CV
                    </a>
                </div>
            </div>
        </header>

        <main class="flex-1 p-4 sm:p-6">
            <div class="mx-auto max-w-4xl">
                <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-lg">
                    <div class="flex items-center justify-between border-b border-slate-100 bg-slate-50 px-4 py-3">
                        <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">Live Preview</span>
                        <span
                            class="inline-flex items-center gap-1.5 text-xs font-medium"
                            :class="syncStatus === 'live' ? 'text-emerald-600' : 'text-slate-400'"
                        >
                            <span
                                class="h-2 w-2 rounded-full"
                                :class="syncStatus === 'live' ? 'bg-emerald-500 animate-pulse' : 'bg-slate-300'"
                            ></span>
                            <span x-text="syncStatus === 'live' ? 'Realtime' : 'Standby'"></span>
                        </span>
                    </div>

                    <div class="bg-slate-100 p-3 sm:p-4">
                        <iframe
                            x-ref="previewFrame"
                            class="min-h-[720px] w-full rounded-lg border-0 bg-white shadow-sm"
                            title="CV live preview"
                        ></iframe>
                    </div>
                </div>

                <p class="mt-4 text-center text-xs text-red-600" x-show="pdfExportError" x-text="pdfExportError"></p>

                <p class="mt-4 text-center text-xs text-slate-500">
                    Keep this page open alongside the builder — changes appear instantly while you edit.
                </p>
            </div>
        </main>
    </div>
</body>
</html>
