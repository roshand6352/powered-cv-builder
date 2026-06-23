{{-- Resolved CSS variables from the captured live preview (template-driven, not hardcoded). --}}
<style id="cv-pdf-theme-styles">
@if($fontFaces !== '')
{!! $fontFaces !!}
@endif

:root {
    --cv-accent: {{ $vars['--cv-accent'] }};
    --cv-secondary: {{ $vars['--cv-secondary'] }};
    --cv-text: {{ $vars['--cv-text'] }};
    --cv-muted: {{ $vars['--cv-muted'] }};
    --cv-border: {{ $vars['--cv-border'] }};
    --cv-accent-border: {{ $vars['--cv-accent-border'] }};
    --cv-tag-bg: {{ $vars['--cv-tag-bg'] }};
    --cv-tag-color: {{ $vars['--cv-tag-color'] ?? $vars['--cv-text'] }};
    --cv-photo-border: {{ $vars['--cv-photo-border'] }};
    --cv-bg: {{ $vars['--cv-bg'] }};
    --cv-sidebar-bg: {{ $vars['--cv-sidebar-bg'] }};
    --cv-font-size: {{ $vars['--cv-font-size'] }};
    --cv-font-weight: {{ $vars['--cv-font-weight'] }};
    --cv-heading-weight: {{ $vars['--cv-heading-weight'] }};
    --cv-heading-style: {{ $vars['--cv-heading-style'] }};
    --cv-page-padding: {{ $vars['--cv-page-padding'] }};
    --cv-section-gap: {{ $vars['--cv-section-gap'] }};
    --cv-item-gap: {{ $vars['--cv-item-gap'] }};
    --cv-border-style: {{ $vars['--cv-border-style'] }};
    --cv-border-width: {{ $vars['--cv-border-width'] }};
    --cv-border-radius: {{ $vars['--cv-border-radius'] }};
}

body.cv-body.cv-pdf-export,
body.cv-body.cv-body--preview.cv-body--embedded.cv-pdf-export {
    font-family: '{{ $fontFamily }}', 'DejaVu Sans', sans-serif;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
</style>
