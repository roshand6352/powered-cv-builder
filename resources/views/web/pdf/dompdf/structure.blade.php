{{--
    DomPDF structure layer — engine fallbacks only. Template typography, colours and spacing
    come from the captured preview CSS; this layer only replaces what DomPDF cannot render
    (grid, flex, gap, box-sizing) and sets page flow to match the Browsershot export.
--}}
<style id="cv-pdf-dompdf-styles">
@if($isCv ?? false)
/* Same page box and continuation-page margins as PdfPaginationStyles (Browsershot). */
@page {
    size: 794px 1123px;
    margin: 40px 0 32px 0;
}

/* Margins are not inherited from the base @page rule, so :first repeats them. */
@page :first {
    margin: 0 0 32px 0;
}

/* No margin on html: DomPDF maps html margins onto the @page box. */
html {
    padding: 0 !important;
}
@else
@page {
    size: 794px 1123px;
    margin: 0;
}

html {
    margin: 0 !important;
    padding: 0 !important;
}
@endif

body.cv-body.cv-pdf-export {
    margin: 0 !important;
    padding: 0 !important;
    width: 100% !important;
    height: auto !important;
    min-height: 0 !important;
    overflow: visible !important;
    background: {{ $vars['--cv-bg'] }} !important;
    font-family: '{{ $fontFamily }}', 'DejaVu Sans', sans-serif !important;
}

body.cv-pdf-export .cv-page {
    width: auto !important;
    max-width: none !important;
    min-width: 0 !important;
    min-height: 0 !important;
    height: auto !important;
    margin: 0 !important;
    box-shadow: none !important;
    overflow: visible !important;
}

/* Trailing padding must not spill a near-empty extra page. */
body.cv-pdf-export .cv-page,
body.cv-pdf-export .cv-content,
body.cv-pdf-export .cv-aside,
body.cv-pdf-export .cv-main {
    padding-bottom: 0 !important;
}

/* Two-column templates: floated sidebar (240px incl. 24px side padding — no box-sizing in DomPDF) + 32px gap. */
body.cv-pdf-export .cv-pdf-columns {
    display: block !important;
    padding: 0 !important;
    min-height: 0 !important;
    height: auto !important;
}

body.cv-pdf-export .cv-pdf-columns > .cv-aside {
    width: 192px !important;
    padding-left: 24px !important;
    padding-right: 24px !important;
    min-height: 0 !important;
    height: auto !important;
}

body.cv-pdf-export .cv-pdf-columns--left > .cv-aside {
    float: left !important;
}

body.cv-pdf-export .cv-pdf-columns--right > .cv-aside {
    float: right !important;
}

/* DomPDF offsets a non-floated block by the float width plus its margin, so both columns float. 458px = 794 - 240 - 32 gap - 2 x 32 padding. */
body.cv-pdf-export .cv-pdf-columns > .cv-main {
    width: 458px !important;
    padding-left: 32px !important;
    padding-right: 32px !important;
    min-height: 0 !important;
    height: auto !important;
}

body.cv-pdf-export .cv-pdf-columns--left > .cv-main {
    float: right !important;
}

body.cv-pdf-export .cv-pdf-columns--right > .cv-main {
    float: left !important;
}

/* Section titles: flex row → inline icon + text. */
body.cv-pdf-export .cv-section-title {
    display: block !important;
}

body.cv-pdf-export img.cv-section-icon {
    display: inline !important;
    width: 1em !important;
    height: 1em !important;
    max-width: none !important;
    margin: 0 7px 0 1px !important;
    vertical-align: middle !important;
}

body.cv-pdf-export img.cv-contact-icon {
    display: inline !important;
    width: 0.92em !important;
    height: 0.92em !important;
    max-width: none !important;
    margin: 0 5px 0 0 !important;
    vertical-align: middle !important;
}

/* Item head: flex space-between → one-row table (floats nested in a floated column break DomPDF line widths). */
body.cv-pdf-export .cv-item-head {
    display: table !important;
    width: 100% !important;
    page-break-inside: avoid;
    page-break-after: avoid;
}

body.cv-pdf-export .cv-item-head > div {
    display: table-cell !important;
    vertical-align: top !important;
}

body.cv-pdf-export .cv-item-head > .cv-item-meta {
    float: none !important;
    width: 1% !important;
    padding-left: 12px !important;
    text-align: right !important;
    white-space: nowrap !important;
}

body.cv-pdf-export .cv-item-title {
    display: block !important;
}

body.cv-pdf-export .cv-item-subtitle {
    clear: none !important;
}

/* Header contact row: flex-wrap + gap (.5rem 1rem) → inline blocks spaced only between items, so a full row still fits. */
body.cv-pdf-export .cv-header-contact {
    display: block !important;
    margin-bottom: -8px !important;
}

body.cv-pdf-export .cv-header-contact > span,
body.cv-pdf-export .cv-header-contact > a {
    display: inline-block !important;
    margin: 0 0 8px 16px !important;
}

body.cv-pdf-export .cv-header-contact > span:first-child,
body.cv-pdf-export .cv-header-contact > a:first-child {
    margin-left: 0 !important;
}

/* Header photo: flex row / column → float or centred block. */
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

/* Tags: flex-wrap + gap → inline blocks. */
body.cv-pdf-export .cv-tags {
    display: block !important;
}

body.cv-pdf-export .cv-tag {
    display: inline-block !important;
    margin: 0 7px 7px 0 !important;
    page-break-inside: avoid;
}

/* List rows: space-between → right float. */
body.cv-pdf-export .cv-list-row {
    display: block !important;
    overflow: hidden !important;
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-list-row > :last-child {
    float: right !important;
}

/* Rich text is real paragraphs — pre-line would add the markup's line breaks. */
body.cv-pdf-export .cv-rich-text {
    white-space: normal !important;
}

/*
 * Chrome collapses the first/last paragraph's default 1em margin through the rich-text wrapper
 * into the neighbouring margins; DomPDF adds them instead. The wrapper takes the collapsed value.
 */
body.cv-pdf-export .cv-rich-text > :first-child {
    margin-top: 0 !important;
}

body.cv-pdf-export .cv-rich-text > :last-child {
    margin-bottom: 0 !important;
}

body.cv-pdf-export .cv-item-text {
    margin-top: 1em !important;
}

/* Page flow: sections and items may split (no large empty gaps); only titles stay with their content. */
body.cv-pdf-export .cv-section,
body.cv-pdf-export .cv-item {
    page-break-inside: auto;
}

body.cv-pdf-export .cv-section-title {
    page-break-after: avoid;
}

body.cv-pdf-export .cv-item-text p,
body.cv-pdf-export .cv-item-text li {
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-header {
    page-break-inside: avoid;
}

body.cv-pdf-export .cv-section:last-child,
body.cv-pdf-export .cv-section:last-child .cv-item:last-child {
    margin-bottom: 0 !important;
}

body.cv-pdf-export .cv-section:empty {
    display: none !important;
}

body.cv-pdf-export img {
    max-width: 100% !important;
}

body.cv-pdf-export .cv-icon-spacer {
    display: inline-block !important;
    width: 1.15em !important;
    height: 1em !important;
}
</style>
