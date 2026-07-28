@php
    use App\Support\Cv\CvSectionIcons;

    $settings = $data['settings'] ?? [];
    $typography = $settings['typography'] ?? [];
    $spacing = $settings['spacing'] ?? [];

    $order = $data['sections']['order'] ?? [];
    $enabled = $data['sections']['enabled'] ?? [];

    $fontKey = $settings['font'] ?? 'figtree';
    $fontStacks = [
        'figtree' => "'Figtree', ui-sans-serif, system-ui, sans-serif",
        'inter' => "'Inter', ui-sans-serif, system-ui, sans-serif",
        'roboto' => "'Roboto', ui-sans-serif, system-ui, sans-serif",
        'lora' => "'Lora', Georgia, serif",
        'merriweather' => "'Merriweather', Georgia, serif",
        'open-sans' => "'Open Sans', ui-sans-serif, system-ui, sans-serif",
        'sora' => "'Sora', ui-sans-serif, system-ui, sans-serif",
        'dm-sans' => "'DM Sans', ui-sans-serif, system-ui, sans-serif",
    ];
    $googleFonts = [
        'figtree' => 'Figtree:wght@400;500;600;700',
        'inter' => 'Inter:wght@400;500;600;700',
        'roboto' => 'Roboto:wght@400;500;700',
        'lora' => 'Lora:wght@400;500;600;700',
        'merriweather' => 'Merriweather:wght@400;700',
        'open-sans' => 'Open+Sans:wght@400;500;600;700',
        'sora' => 'Sora:wght@400;500;600;700',
        'dm-sans' => 'DM+Sans:wght@400;500;700',
    ];

    $fontStack = $fontStacks[$fontKey] ?? $fontStacks['figtree'];
    $googleFamily = $googleFonts[$fontKey] ?? $googleFonts['figtree'];

    $primary = $settings['primary_color'] ?? '#172d40';
    $secondary = $settings['secondary_color'] ?? '#c1623d';
    $text = $settings['text_color'] ?? '#3a4a58';
    $background = $settings['background_color'] ?? '#ffffff';
    $headingStyle = $typography['heading_style'] ?? 'normal';
    $headerStyle = $settings['header_style'] ?? 'stacked';
    $layout = $data['layout'] ?? 'professional';

    $fontSize = $typography['font_size'] ?? '14px';
    $headingWeight = $typography['heading_weight'] ?? '700';
    $bodyWeight = $typography['font_weight'] ?? '400';
    $pagePadding = $spacing['page_padding'] ?? '2.5rem';
    $sectionGap = $spacing['section_gap'] ?? '1.5rem';

    $headingTransform = $headingStyle === 'uppercase' ? 'uppercase' : 'none';
    $headingFontStyle = $headingStyle === 'italic' ? 'italic' : 'normal';

    $sender = $data['sender'] ?? [];
    $recipient = $data['recipient'] ?? [];

    $hasRecipient = trim((string) ($recipient['name'] ?? '')) !== ''
        || trim((string) ($recipient['company'] ?? '')) !== ''
        || trim((string) ($recipient['address'] ?? '')) !== '';

    $isEnabled = fn (string $key): bool => in_array($key, $enabled, true);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $data['subject'] ?: 'Cover Letter' }} | {{ config('app.name') }}</title>
    @include('web.partials.fontawesome')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family={{ $googleFamily }}&display=swap" rel="stylesheet">
    <style>
        :root {
            --cl-primary: {{ $primary }};
            --cl-secondary: {{ $secondary }};
            --cl-text: {{ $text }};
            --cl-bg: {{ $background }};
            --cl-font-size: {{ $fontSize }};
            --cl-section-gap: {{ $sectionGap }};
        }

        * { box-sizing: border-box; }

        html, body { margin: 0; padding: 0; background: #f1f5f9; }

        body {
            font-family: {!! $fontStack !!};
            color: var(--cl-text);
            font-size: var(--cl-font-size);
            font-weight: {{ $bodyWeight }};
            line-height: 1.7;
            -webkit-font-smoothing: antialiased;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        .cl-page {
            position: relative;
            width: 100%;
            max-width: 794px;
            min-height: 1123px;
            margin: 0 auto;
            padding: {{ $pagePadding }};
            background: var(--cl-bg);
            box-shadow: 0 10px 40px rgba(15, 23, 42, 0.08);
        }

        .cl-section { margin-bottom: var(--cl-section-gap); }

        .cl-header { margin-bottom: var(--cl-section-gap); }

        .cl-header--bordered {
            border-bottom: {{ $settings['borders']['width'] ?? '2px' }} {{ $settings['borders']['style'] ?? 'solid' }} var(--cl-primary);
            padding-bottom: 0.85rem;
        }

        .cl-header--inline {
            display: flex;
            flex-wrap: wrap;
            align-items: baseline;
            justify-content: space-between;
            gap: 0.5rem 1.5rem;
        }

        .cl-name {
            font-size: calc(var(--cl-font-size) * 1.65);
            font-weight: {{ $headingWeight }};
            color: var(--cl-primary);
            text-transform: {{ $headingTransform }};
            font-style: {{ $headingFontStyle }};
            letter-spacing: 0.02em;
            line-height: 1.2;
        }

        .cl-role {
            margin-top: 0.25rem;
            font-size: calc(var(--cl-font-size) * 0.95);
            font-weight: 500;
            color: var(--cl-secondary);
            opacity: 0.95;
        }

        .cl-contact {
            font-size: calc(var(--cl-font-size) * 0.85);
            color: var(--cl-text);
            opacity: 0.85;
            line-height: 1.65;
        }

        .cl-contact-item { display: block; }

        .cl-header--inline .cl-contact { text-align: right; }

        .cl-date {
            font-size: calc(var(--cl-font-size) * 0.9);
            color: var(--cl-text);
            opacity: 0.85;
        }

        .cl-recipient { font-size: calc(var(--cl-font-size) * 0.9); line-height: 1.65; }
        .cl-recipient strong { color: var(--cl-primary); font-weight: {{ $headingWeight }}; }
        .cl-address { white-space: pre-line; }

        .cl-subject {
            font-weight: {{ $headingWeight }};
            color: var(--cl-primary);
            text-transform: {{ $headingTransform }};
            font-style: {{ $headingFontStyle }};
            font-size: calc(var(--cl-font-size) * 1.02);
        }

        .cl-greeting { font-weight: 600; }

        .cl-rich-text { white-space: normal; line-height: 1.75; }
        .cl-rich-text p { margin: 0 0 0.75rem; }
        .cl-rich-text p:last-child { margin-bottom: 0; }
        .cl-rich-text ul, .cl-rich-text ol { margin: 0 0 0.75rem; padding-left: 1.25rem; }
        .cl-rich-text li { margin: 0.2rem 0; }
        .cl-rich-text h1, .cl-rich-text h2, .cl-rich-text h3 { font-weight: {{ $headingWeight }}; color: var(--cl-primary); margin: 0.5rem 0 0.35rem; }
        .cl-rich-text strong, .cl-rich-text b { font-weight: 700; }
        .cl-rich-text em, .cl-rich-text i { font-style: italic; }
        .cl-rich-text u { text-decoration: underline; }
        .cl-rich-text a { color: var(--cl-primary); text-decoration: underline; word-break: break-word; }
        .cl-rich-text .ql-align-center { text-align: center; }
        .cl-rich-text .ql-align-right { text-align: right; }
        .cl-rich-text .ql-align-justify { text-align: justify; }

        .cl-closing { margin-top: var(--cl-section-gap); font-weight: 500; }
        .cl-signature {
            margin-top: 0.35rem;
            font-weight: {{ $headingWeight }};
            color: var(--cl-primary);
        }

        /* Layout variants */
        .cl-layout-professional .cl-subject {
            border-left: 3px solid var(--cl-secondary);
            padding-left: 0.65rem;
        }

        .cl-layout-modern .cl-name { color: var(--cl-primary); }
        .cl-layout-modern .cl-subject { color: var(--cl-secondary); }

        .cl-layout-executive .cl-header { text-align: center; }
        .cl-layout-executive .cl-header--inline { justify-content: center; text-align: center; }
        .cl-layout-executive .cl-header--inline .cl-contact { text-align: center; width: 100%; }
        .cl-layout-executive .cl-role { color: var(--cl-secondary); letter-spacing: 0.06em; text-transform: uppercase; font-size: calc(var(--cl-font-size) * 0.82); }
        .cl-layout-executive .cl-subject { letter-spacing: 0.04em; }

        .cl-layout-minimal .cl-name { font-weight: 600; font-size: calc(var(--cl-font-size) * 1.5); color: var(--cl-text); }
        .cl-layout-minimal .cl-subject { color: var(--cl-text); font-weight: 600; text-transform: none; font-style: normal; }
        .cl-layout-minimal .cl-rich-text { line-height: 1.85; }

        .cl-layout-elegant .cl-role { font-style: italic; color: var(--cl-secondary); }
        .cl-layout-elegant .cl-subject { color: var(--cl-secondary); border-bottom: 1px solid color-mix(in srgb, var(--cl-secondary) 25%, white); padding-bottom: 0.35rem; }

        .pdf-watermark,
        .cl-watermark {
            position: fixed;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            pointer-events: none;
            user-select: none;
            z-index: 2147483000;
            overflow: visible;
            background: transparent;
        }
        .pdf-watermark__img,
        .cl-watermark .cv-watermark-logo {
            display: block;
            width: {{ (int) config('cv-templates.watermark.logo_display_width_px', 900) }}px;
            max-width: none;
            height: auto;
            opacity: {{ config('cv-templates.watermark.logo_opacity', 0.20) }};
            transform: rotate({{ config('cv-templates.watermark.angle', -30) }}deg);
            transform-origin: center center;
            object-fit: contain;
            image-rendering: auto;
        }

        @media (max-width: 768px) {
            .cl-page {
                max-width: 100%;
                min-height: auto;
                padding: 1.35rem;
                box-shadow: none;
            }

            .cl-header--inline {
                flex-direction: column;
                align-items: flex-start;
            }

            .cl-header--inline .cl-contact { text-align: left; }

            .cl-layout-executive .cl-header,
            .cl-layout-executive .cl-header--inline .cl-contact { text-align: left; }
        }

        @media print {
            @page { size: A4; margin: 0; }
            html, body { background: #fff; margin: 0; padding: 0; }
            .cl-page {
                width: 100%;
                max-width: none;
                min-height: auto;
                margin: 0;
                padding: {{ $pagePadding }};
                box-shadow: none;
                page-break-after: auto;
            }
            .cl-section { page-break-inside: avoid; break-inside: avoid-page; }
            .pdf-watermark,
            .cl-watermark {
                position: fixed;
                z-index: 2147483000;
                overflow: visible;
            }
            * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
        }

        {!! \App\Support\FontAwesome\FontAwesomeAssets::iconStylesCss() !!}
    </style>
</head>
<body>
    @if ($watermark)
        <div class="pdf-watermark" aria-hidden="true">
            <img
                class="pdf-watermark__img cv-watermark-logo"
                src="{{ asset(config('cv-templates.watermark.logo', 'images/WATERMARK-LOGO.png')) }}"
                width="4500"
                height="827"
                alt=""
            >
        </div>
    @endif
    <div class="cl-page cl-layout-{{ $layout }}">
        @foreach ($order as $section)
            @continue(! $isEnabled($section))

            @switch($section)
                @case('header')
                    <div class="cl-header cl-header--{{ $headerStyle }}">
                        <div>
                            @if ($sender['full_name'] ?? '')
                                <div class="cl-name">{{ $sender['full_name'] }}</div>
                            @endif
                            @if (($headerStyle !== 'minimal') && ($sender['job_title'] ?? ''))
                                <div class="cl-role">{{ $sender['job_title'] }}</div>
                            @endif
                        </div>
                        @if ($headerStyle !== 'minimal')
                            <div class="cl-contact">
                                @php
                                    $contactFields = [
                                        'email' => $sender['email'] ?? '',
                                        'phone' => $sender['phone'] ?? '',
                                        'location' => $sender['location'] ?? '',
                                        'website' => $sender['website'] ?? '',
                                        'linkedin' => $sender['linkedin'] ?? '',
                                        'github' => $sender['github'] ?? '',
                                    ];
                                @endphp
                                @foreach ($contactFields as $field => $value)
                                    @if ($value)
                                        <span class="cl-contact-item">
                                            <i class="cl-contact-icon {{ CvSectionIcons::contactIcon($field) }}" aria-hidden="true"></i>{{ $value }}
                                        </span>
                                    @endif
                                @endforeach
                            </div>
                        @endif
                    </div>
                    @break

                @case('date')
                    @if ($data['date'] ?? '')
                        <div class="cl-section cl-date">{{ $data['date'] }}</div>
                    @endif
                    @break

                @case('recipient')
                    @if ($hasRecipient)
                        <div class="cl-section cl-recipient">
                            @if ($recipient['name'] ?? '')
                                <div><strong>{{ $recipient['name'] }}</strong></div>
                            @endif
                            @if ($recipient['company'] ?? '')
                                <div>{{ $recipient['company'] }}</div>
                            @endif
                            @if ($recipient['address'] ?? '')
                                <x-cv.render.rich-text :content="$recipient['address']" class="cl-address cl-rich-text" />
                            @endif
                        </div>
                    @endif
                    @break

                @case('subject')
                    @if ($data['subject'] ?? '')
                        <div class="cl-section cl-subject">{{ $data['subject'] }}</div>
                    @endif
                    @break

                @case('greeting')
                    @if ($data['greeting'] ?? '')
                        <div class="cl-section cl-greeting">{{ $data['greeting'] }}</div>
                    @endif
                    @break

                @case('introduction')
                    @if ($data['introduction'] ?? '')
                        <div class="cl-section cl-paragraph">
                            <x-cv.render.rich-text :content="$data['introduction']" class="cl-rich-text" />
                        </div>
                    @endif
                    @break

                @case('body')
                    @if ($data['body'] ?? '')
                        <div class="cl-section cl-body">
                            <x-cv.render.rich-text :content="$data['body']" class="cl-rich-text" />
                        </div>
                    @endif
                    @break

                @case('closing')
                    @if ($data['closing'] ?? '')
                        <div class="cl-closing">{{ $data['closing'] }}</div>
                    @endif
                    @break

                @case('signature')
                    @if ($data['signature'] ?? '')
                        <div class="cl-signature">{{ $data['signature'] }}</div>
                    @endif
                    @break
            @endswitch
        @endforeach
    </div>
</body>
</html>
