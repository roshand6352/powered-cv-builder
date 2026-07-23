<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $preview['personal_details']['full_name'] ?? 'CV Preview' }} | {{ config('app.name') }}</title>
    @include('web.partials.favicon')

    @include('web.partials.fontawesome')

    @if ($fontUrl)
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="{{ $fontUrl }}" rel="stylesheet">
    @endif
    @php
        $accentColor = $settings['primary_color'] ?? $settings['accent'] ?? '#1e293b';
        $accentSoft = \App\Support\Cv\CvColor::mixWithWhite($accentColor, 0.08);
        $accentBorder = \App\Support\Cv\CvColor::mixWithWhite($accentColor, 0.25);
        $accentTagBg = \App\Support\Cv\CvColor::mixWithWhite($accentColor, 0.10);
        $accentPhotoBorder = \App\Support\Cv\CvColor::mixWithWhite($accentColor, 0.20);
    @endphp
    @include('web.cv.templates.partials.styles', [
        'layout' => $layout,
        'settings' => $settings,
        'mode' => $mode,
        'modeConfig' => $modeConfig,
        'fontFamily' => $fontFamily,
        'accentColor' => $accentColor,
        'accentSoft' => $accentSoft,
        'accentBorder' => $accentBorder,
        'accentTagBg' => $accentTagBg,
        'accentPhotoBorder' => $accentPhotoBorder,
    ])
</head>
<body class="cv-body cv-body--{{ $mode }}">
    <div
        class="cv-page cv-layout-{{ $layout }}"
        data-layout="{{ $layout }}"
        data-template="{{ $definition['key'] ?? $definition['slug'] ?? $layout }}"
        data-mode="{{ $mode }}"
        data-sidebar="{{ $layoutConfig['sidebar_position'] ?? 'none' }}"
        data-photo="{{ $settings['photo_position'] ?? 'top' }}"
        style="{{ $pageStyle }}"
    >
        @if ($watermark)
            <div class="cv-watermark" aria-hidden="true">
                <div class="cv-watermark-inner">
                    <span class="cv-watermark-primary">{{ $watermarkPrimaryText }}</span>
                    <span class="cv-watermark-secondary">{{ $watermarkSecondaryText }}</span>
                </div>
            </div>
        @endif

        @include('web.cv.templates.partials.json-renderer', [
            'preview' => $preview,
            'renderSections' => $renderSections ?? [],
            'layout' => $layout,
            'layoutConfig' => $layoutConfig,
            'sectionLayout' => $sectionLayout,
            'settings' => $settings,
            'jsonRenderer' => $jsonRenderer,
        ])
    </div>
</body>
</html>
