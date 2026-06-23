<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    <title>Choose a CV Template | {{ $cv->title }} | {{ config('app.name') }}</title>

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
                        href="{{ route('dashboard.cvs') }}"
                        class="inline-flex shrink-0 items-center gap-2 rounded-lg px-2.5 py-1.5 text-sm font-medium text-muted transition hover:bg-cream hover:text-navy"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        <span class="hidden sm:inline">Back to CVs</span>
                    </a>
                    <div class="hidden h-6 w-px bg-slate-200 sm:block"></div>
                    <div class="min-w-0">
                        <p class="truncate text-sm font-semibold text-navy">{{ $cv->title }}</p>
                        <p class="text-xs text-muted">Step 2 · Choose a template</p>
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
                <a href="{{ route('dashboard.cvs') }}" class="transition hover:text-navy">My CVs</a>
                <span class="text-slate-300">/</span>
                <span class="text-navy">Choose template</span>
            </nav>

            @if (session('status') === 'cv-created')
                <div class="mt-6 flex items-start gap-3 rounded-xl border border-brand-200 bg-brand-50 px-4 py-3 text-sm text-brand-800">
                    <svg class="mt-0.5 h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Your CV was created. Pick a template below to start building.
                </div>
            @endif

            {{-- Page header --}}
            <div class="mt-6 max-w-2xl">
                <h1 class="text-3xl font-bold tracking-tight text-navy sm:text-4xl" style="font-family:'Sora',sans-serif">
                    Select a CV template
                </h1>
                <p class="mt-3 text-base leading-relaxed text-muted">
                    Choose a professionally designed template for your resume. Every preview below is a live miniature
                    of the final document — compare typography, colors, and layout, then continue to the builder.
                </p>
            </div>

            @if ($templates->isEmpty())
                <div class="mt-10 flex flex-col items-center justify-center rounded-3xl border border-dashed border-slate-300 bg-white px-6 py-20 text-center">
                    <span class="flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                        <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.6" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </span>
                    <h2 class="mt-5 text-xl font-semibold text-navy">No templates available yet</h2>
                    <p class="mt-2 max-w-sm text-sm text-muted">
                        CV templates are published from the admin panel. Please check back soon or ask an administrator to add one.
                    </p>
                </div>
            @else
                <div class="template-gallery-grid mt-10 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    @foreach ($templates as $template)
                        @php
                            $settings = $template['layout_json']['settings'] ?? ($template['settings'] ?? []);
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
                            :preview-src="route('cv-templates.preview', $template['key'])"
                            :font-label="$fontLabels[$font] ?? ucfirst($font)"
                            :font-size="$typography['font_size'] ?? '14px'"
                            :primary="$primary"
                            :secondary="$secondary"
                            :background="$background"
                            :text="$text"
                            :template-key="$template['key']"
                            :action="route('cvs.templates.store', $cv)"
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
