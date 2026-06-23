<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    <title>Choose a Cover Letter Template | {{ $cv->title }} | {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-cream font-sans antialiased text-body" style="font-family:'DM Sans',sans-serif">
    <div class="min-h-screen">
        {{-- Top bar --}}
        <header class="sticky top-0 z-40 border-b border-slate-200/80 bg-white/95 backdrop-blur">
            <div class="mx-auto flex h-16 max-w-7xl items-center justify-between gap-4 px-4 sm:px-6 lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <a
                        href="{{ route('dashboard.cover-letters') }}"
                        class="inline-flex shrink-0 items-center gap-2 rounded-lg px-2.5 py-1.5 text-sm font-medium text-muted transition hover:bg-cream hover:text-navy"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span class="hidden sm:inline">Cover Letters</span>
                    </a>
                    <div class="hidden h-6 w-px bg-slate-200 sm:block"></div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-navy">{{ $cv->title }}</p>
                        <p class="text-xs text-muted">Choose a cover letter template</p>
                    </div>
                </div>

                <div class="hidden items-center gap-2 rounded-full bg-brand-50 px-3 py-1.5 text-xs font-semibold text-brand-700 sm:flex">
                    <span class="flex h-5 w-5 items-center justify-center rounded-full bg-brand-600 text-[11px] text-white">{{ $templates->count() }}</span>
                    {{ Str::plural('template', $templates->count()) }}
                </div>
            </div>
        </header>

        <main class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
            {{-- Breadcrumb --}}
            <nav class="flex items-center gap-1.5 text-xs font-medium text-muted" aria-label="Breadcrumb">
                <a href="{{ route('dashboard') }}" class="transition hover:text-navy">Dashboard</a>
                <span class="text-slate-300">/</span>
                <a href="{{ route('dashboard.cover-letters') }}" class="transition hover:text-navy">Cover Letters</a>
                <span class="text-slate-300">/</span>
                <span class="text-navy">Choose template</span>
            </nav>

            {{-- Page header --}}
            <div class="mt-6 max-w-2xl">
                <h1 class="text-3xl font-bold tracking-tight text-navy sm:text-4xl" style="font-family:'Sora',sans-serif">
                    Select a cover letter template
                </h1>
                <p class="mt-3 text-base leading-relaxed text-muted">
                    Choose a professional cover letter design for your application. Your letter will be generated from
                    <span class="font-semibold text-navy">{{ $cv->title }}</span> — pick a style, then personalize and preview it live.
                </p>
            </div>

            @if ($templates->isEmpty())
                <div class="mt-10 flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                        </svg>
                    </span>
                    <h2 class="mt-5 text-xl font-semibold text-navy">No cover letter templates yet</h2>
                    <p class="mt-2 max-w-sm text-sm text-muted">
                        Cover letter templates are published from the admin panel. Please check back soon or ask an administrator to add one.
                    </p>
                </div>
            @else
                <div class="template-gallery-grid mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($templates as $template)
                        @php
                            $settings = $template['settings'] ?? [];
                            $typography = $settings['typography'] ?? [];
                            $font = $settings['font'] ?? 'figtree';
                            $primary = $settings['primary_color'] ?? $settings['accent'] ?? '#172d40';
                            $secondary = $settings['secondary_color'] ?? '#c1623d';
                            $background = $settings['background_color'] ?? '#ffffff';
                            $text = $settings['text_color'] ?? '#3a4a58';
                            $fontLabels = [
                                'figtree' => 'Figtree', 'inter' => 'Inter', 'roboto' => 'Roboto',
                                'lora' => 'Lora', 'merriweather' => 'Merriweather', 'open-sans' => 'Open Sans',
                                'sora' => 'Sora', 'dm-sans' => 'DM Sans',
                            ];
                        @endphp

                        <x-template-card
                            :name="$template['name']"
                            :description="$template['description'] ?? null"
                            :preview-src="route('cover-letter-templates.preview', $template['key'])"
                            :font-label="$fontLabels[$font] ?? ucfirst($font)"
                            :font-size="$typography['font_size'] ?? '14px'"
                            :primary="$primary"
                            :secondary="$secondary"
                            :background="$background"
                            :text="$text"
                            :template-key="$template['key']"
                            :action="route('cvs.cover-letters.store', $cv)"
                            cta="Use this template"
                        />
                    @endforeach
                </div>
            @endif
        </main>
    </div>

    @include('web.partials.template-preview-scaler')
</body>
</html>
