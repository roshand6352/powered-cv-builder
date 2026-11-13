@props([
    'cv',
    'builder',
    'linkedinImportDocument' => null,
    'linkedinImportApplied' => false,
])

@php
    $activePlan = app(\App\Services\Subscription\SubscriptionService::class)->resolveActivePlan(auth()->user());
    $showWatermark = app(\App\Services\Pdf\PdfWatermarkService::class)->shouldApply(auth()->user());
    $builderData = $builder->toArray();

    if (is_array($linkedinImportDocument)) {
        $builderData['document'] = app(\App\Services\Cv\LinkedInPdfImportService::class)
            ->mergeIntoDocument($builderData['document'] ?? [], $linkedinImportDocument);
    }

    $builderPayload = array_merge($builderData, [
        'has_ai_access' => ! $activePlan->isFree(),
        'linkedin_import_prefilled' => (bool) $linkedinImportApplied,
        'show_watermark' => $showWatermark,
        'watermark' => [
            'logo_url' => url(config('cv-templates.watermark.logo', 'images/WATERMARK-LOGO.png')),
            'opacity' => (float) config('cv-templates.watermark.logo_opacity', 0.20),
            'angle' => (float) config('cv-templates.watermark.angle', -30),
            'width_px' => (int) config('cv-templates.watermark.logo_display_width_px', 900),
        ],
        'urls' => [
            'autosave' => route('cvs.builder.autosave', $cv),
            'update' => route('cvs.builder.update', $cv),
            'preview' => route('cvs.render.live', $cv),
            'templates' => route('cvs.templates', $cv),
            'pdfExport' => route('pdf-exports.cv.store', $cv),
            'pdfDownload' => route('pdf-exports.cv.download', $cv),
            'pdfStatus' => route('pdf-exports.show', ['pdfExport' => '__ID__']),
            'aiUsage' => route('ai.usage'),
            'aiLog' => route('ai.logs.show', ['aiLog' => '__ID__']),
            'aiSummary' => route('ai.summary'),
            'aiExperience' => route('ai.experience.improve'),
            'aiSkills' => route('ai.skills.improve'),
            'aiGrammar' => route('ai.grammar-fix'),
            'aiCoverLetter' => route('ai.cover-letter'),
            'subscription' => route('dashboard.subscription'),
            'render' => route('cvs.render.show', ['cv' => $cv, 'template' => '__TEMPLATE__']),
            'livePreview' => route('cv.preview', $cv),
            'linkedinPdfImportStatus' => route('linkedin-pdf-import.status'),
            'linkedinPdfImportConsume' => route('linkedin-pdf-import.consume'),
        ],
    ]);
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    @include('web.partials.fontawesome')

    <title>{{ $cv->title }} | CV Builder | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cv-builder-page h-full overflow-hidden bg-cream font-sans antialiased text-body" style="font-family:'DM Sans',sans-serif">
    <div
        x-data="cvBuilder(@js($builderPayload))"
        class="flex h-full flex-col overflow-hidden"
    >
        <x-cv.builder.toolbar :cv="$cv" />

        @if (session('status') === 'template-selected')
            <div class="shrink-0 border-b border-brand-200 bg-brand-50 px-4 py-2.5 text-center text-sm text-brand-800 sm:px-6">
                @if ($linkedinImportApplied)
                    Template applied. Your LinkedIn import is ready in the form — review and edit, then Save when you're ready.
                @else
                    Template applied. Your live preview is ready — start filling in your details.
                @endif
            </div>
        @endif

        <div
            x-show="linkedinImportPrefilled"
            x-cloak
            class="shrink-0 border-b border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-800 sm:px-6"
        >
            LinkedIn data has been imported into this form locally. Review every field, then click Save Draft or Publish when you're ready.
        </div>

        {{-- Mobile / tablet: Edit CV / Preview CV switcher --}}
        <div class="shrink-0 border-b border-line bg-white px-4 py-2 sm:px-6 lg:hidden">
            <div class="grid grid-cols-2 gap-1 rounded-xl bg-slate-100 p-1" role="tablist" aria-label="CV builder view">
                <button
                    type="button"
                    role="tab"
                    id="cv-builder-tab-edit"
                    aria-controls="cv-builder-form-panel"
                    :aria-selected="mobileView === 'edit'"
                    @click="setMobileView('edit')"
                    class="inline-flex min-h-[2.5rem] items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                    :class="mobileView === 'edit' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125" />
                    </svg>
                    Edit CV
                </button>
                <button
                    type="button"
                    role="tab"
                    id="cv-builder-tab-preview"
                    aria-controls="cv-preview-panel"
                    :aria-selected="mobileView === 'preview'"
                    @click="setMobileView('preview')"
                    class="inline-flex min-h-[2.5rem] items-center justify-center gap-2 rounded-lg px-3 py-2 text-sm font-semibold transition focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500"
                    :class="mobileView === 'preview' ? 'bg-white text-brand-700 shadow-sm' : 'text-slate-600 hover:text-slate-900'"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Preview CV
                </button>
            </div>
        </div>

        <div class="flex min-h-0 flex-1 flex-col overflow-hidden lg:flex-row">
            {{-- Left: scrollable form panel (45%) --}}
            <div
                x-ref="formPanel"
                id="cv-builder-form-panel"
                class="min-h-0 flex-1 overflow-x-hidden overflow-y-auto overscroll-contain border-line bg-white lg:w-[45%] lg:flex-none lg:border-r"
                :class="{ 'max-lg:hidden': mobileView === 'preview' }"
            >
                <div class="p-4 sm:p-6">
                    <x-cv.builder.forms />
                </div>
            </div>

            {{-- Right: fixed live preview (55%) --}}
            <div
                class="flex min-h-0 flex-1 flex-col overflow-hidden bg-cream max-lg:hidden lg:h-auto lg:w-[55%]"
                :class="{ 'max-lg:hidden': mobileView === 'edit' }"
            >
                <div
                    x-ref="previewPanel"
                    id="cv-preview-panel"
                    class="flex h-full min-h-0 flex-col"
                >
                    <x-cv.builder.preview />
                </div>
            </div>
        </div>

        <x-cv.pdf-ready-sheet />
    </div>

    <x-linkedin-import-modal />
    <x-ai-upgrade-modal :default-url="route('dashboard.subscription')" />
</body>
</html>
