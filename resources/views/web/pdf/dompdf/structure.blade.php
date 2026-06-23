{{-- DomPDF structure layer — dynamic from captured template variables (not hardcoded per template). --}}
<style id="cv-pdf-dompdf-styles">
@page {
    size: 794px 1123px;
    margin: 0;
}

html, body.cv-body.cv-pdf-export {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    background: {{ $vars['--cv-bg'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
    font-family: '{{ $fontFamily }}', 'DejaVu Sans', sans-serif !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

body.cv-pdf-export .cv-page {
    width: 100% !important;
    margin: 0 !important;
    padding: 0 !important;
    background: {{ $vars['--cv-bg'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
    box-shadow: none !important;
    page-break-after: always;
}

body.cv-pdf-export .cv-page:last-child {
    page-break-after: auto;
}

body.cv-pdf-export .cv-content,
body.cv-pdf-export .cv-main,
body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-main {
    background: {{ $vars['--cv-bg'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
}

body.cv-pdf-export .cv-aside,
body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-aside {
    background: {{ $vars['--cv-sidebar-bg'] }} !important;
    vertical-align: top !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf {
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    table-layout: fixed !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-aside {
    width: 240px !important;
    padding: 32px 24px !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-main {
    padding: {{ $vars['--cv-page-padding'] }} 32px !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-pdf-gap {
    width: 32px !important;
    padding: 0 !important;
}

body.cv-pdf-export .cv-content {
    padding: {{ $vars['--cv-page-padding'] }} !important;
}

body.cv-pdf-export .cv-section {
    margin-bottom: {{ $vars['--cv-section-gap'] }} !important;
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-section:last-child {
    margin-bottom: 0 !important;
}

body.cv-pdf-export .cv-item {
    margin-bottom: {{ $vars['--cv-item-gap'] }} !important;
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-item:last-child {
    margin-bottom: 0 !important;
}

body.cv-pdf-export .cv-item-head {
    display: block !important;
    overflow: hidden !important;
}

body.cv-pdf-export .cv-item-title {
    display: inline !important;
    font-size: {{ $type['itemTitle'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
}

body.cv-pdf-export .cv-item-meta {
    display: inline !important;
    float: right !important;
    font-size: {{ $type['itemMeta'] }} !important;
    color: {{ $vars['--cv-muted'] }} !important;
}

body.cv-pdf-export .cv-item-subtitle {
    font-size: {{ $type['itemSubtitle'] }} !important;
    color: {{ $vars['--cv-secondary'] }} !important;
    clear: both !important;
}

body.cv-pdf-export .cv-item-text {
    font-size: {{ $type['itemText'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
    line-height: 1.55 !important;
}

body.cv-pdf-export .cv-section-title {
    display: block !important;
    font-size: {{ $type['sectionTitle'] }} !important;
    font-weight: {{ $vars['--cv-heading-weight'] }} !important;
    color: {{ $vars['--cv-accent'] }} !important;
    border-bottom: {{ $vars['--cv-border-width'] }} {{ $vars['--cv-border-style'] }} {{ $vars['--cv-accent-border'] }} !important;
    margin: 0 0 16px !important;
    padding-bottom: 7px !important;
}

body.cv-pdf-export .cv-header {
    margin-bottom: {{ $vars['--cv-section-gap'] }} !important;
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-header-name {
    font-size: {{ $type['headerName'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
    margin: 0 !important;
}

body.cv-pdf-export .cv-header-role {
    font-size: {{ $type['headerRole'] }} !important;
    color: {{ $vars['--cv-secondary'] }} !important;
}

body.cv-pdf-export .cv-header-summary {
    font-size: {{ $type['headerSummary'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
}

body.cv-pdf-export .cv-header-contact {
    display: block !important;
    font-size: {{ $type['headerContact'] }} !important;
    color: {{ $vars['--cv-muted'] }} !important;
}

body.cv-pdf-export .cv-header-contact > span,
body.cv-pdf-export .cv-header-contact > a {
    display: inline-block !important;
    margin: 0 16px 8px 0 !important;
}

body.cv-pdf-export .cv-header--photo-left .cv-header-photo {
    float: left !important;
    margin: 0 20px 12px 0 !important;
}

body.cv-pdf-export .cv-header--photo-right .cv-header-photo {
    float: right !important;
    margin: 0 0 12px 20px !important;
}

body.cv-pdf-export .cv-header--photo-center {
    text-align: center !important;
}

body.cv-pdf-export .cv-header--photo-center .cv-header-photo {
    display: block !important;
    margin: 0 auto 16px !important;
}

body.cv-pdf-export .cv-header-body {
    overflow: hidden !important;
}

body.cv-pdf-export .cv-header-photo,
body.cv-pdf-export .cv-header-photo--sidebar,
body.cv-pdf-export .cv-sidebar-photo img {
    width: 96px !important;
    height: 96px !important;
    max-width: 96px !important;
    border-radius: 999px !important;
    border: 3px solid {{ $vars['--cv-photo-border'] }} !important;
}

body.cv-pdf-export .cv-tag {
    display: inline-block !important;
    background: {{ $vars['--cv-tag-bg'] }} !important;
    color: {{ $vars['--cv-tag-color'] ?? $vars['--cv-text'] }} !important;
    border-radius: 999px !important;
    padding: 4px 11px !important;
    margin: 0 7px 7px 0 !important;
    font-size: {{ $type['tag'] }} !important;
}

body.cv-pdf-export .cv-list-row {
    display: block !important;
    overflow: hidden !important;
    font-size: {{ $type['listRow'] }} !important;
    padding: 4px 0 !important;
}

body.cv-pdf-export .cv-list-row span:last-child {
    float: right !important;
}

body.cv-pdf-export .cv-group {
    margin-bottom: 20px !important;
}

body.cv-pdf-export .cv-group-title {
    font-size: {{ $type['groupTitle'] }} !important;
    color: {{ $vars['--cv-text'] }} !important;
}

/* Skill / progress bars — block-based fallback for unsupported flex layouts. */
body.cv-pdf-export .cv-progress-bar,
body.cv-pdf-export .cv-skill-bar,
body.cv-pdf-export [class*="progress-bar"] {
    display: block !important;
    width: 100% !important;
    height: 8px !important;
    background: {{ $vars['--cv-border'] }} !important;
    border-radius: 4px !important;
    overflow: hidden !important;
    margin: 6px 0 !important;
}

body.cv-pdf-export .cv-progress-fill,
body.cv-pdf-export .cv-skill-fill,
body.cv-pdf-export [class*="progress-fill"] {
    display: block !important;
    height: 100% !important;
    background: {{ $vars['--cv-secondary'] }} !important;
}

/* Timeline-style experience items — border fallback (no ::before). */
body.cv-pdf-export .cv-section-experience .cv-item {
    border-left: 2px solid {{ $vars['--cv-accent-border'] }} !important;
    padding-left: 16px !important;
    margin-left: 6px !important;
}

body.cv-pdf-export img {
    max-width: 100% !important;
    height: auto !important;
}

body.cv-pdf-export .cv-icon-spacer {
    display: inline-block !important;
    width: 1.15em !important;
    height: 1em !important;
}
</style>
