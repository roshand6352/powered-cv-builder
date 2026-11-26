<?php

namespace App\Support\Pdf;

/**
 * Shared PDF pagination rules for Browsershot and DomPDF — keeps page counts aligned.
 */
class PdfPaginationStyles
{
    public static function css(): string
    {
        return <<<'CSS'
/* Shared PDF pagination — avoid only small blocks, not entire sections (prevents extra pages). */
/* Zero @page margins so page/sidebar backgrounds paint edge-to-edge with no white colour gaps. */
@page { size: 794px 1123px; margin: 0; }

html, body.cv-body.cv-pdf-export,
body.cv-body.cv-body--preview.cv-body--embedded.cv-pdf-export {
    margin: 0 !important;
    padding: 0 !important;
    background: var(--cv-bg, #ffffff) !important;
    overflow: visible !important;
}

body.cv-pdf-export .cv-page,
body.cv-pdf-export.cv-body--preview .cv-page,
body.cv-pdf-export.cv-body--embedded .cv-page {
    width: 794px !important;
    max-width: 794px !important;
    min-width: 794px !important;
    margin: 0 auto !important;
    transform: none !important;
    box-shadow: none !important;
    position: relative !important;
    overflow: visible !important;
    background: var(--cv-bg, #ffffff) !important;
}

/*
 * Content-height sidebar colour strip (absolute inside the grid/columns).
 * Continues across page breaks with the content, but stops where content ends —
 * does not paint empty leftover space on the last page (unlike position:fixed).
 */
body.cv-pdf-export .cv-grid-sidebar,
body.cv-pdf-export .cv-pdf-columns {
    position: relative !important;
}

body.cv-pdf-export .cv-pdf-sidebar-bg {
    position: absolute !important;
    top: 0 !important;
    bottom: 0 !important;
    width: 240px !important;
    height: auto !important;
    min-height: 0 !important;
    max-height: none !important;
    margin: 0 !important;
    padding: 0 !important;
    border: 0 !important;
    background: var(--cv-sidebar-bg, #f4f7f9) !important;
    z-index: 0 !important;
    pointer-events: none !important;
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}

body.cv-pdf-export .cv-pdf-sidebar-bg--left {
    left: 0 !important;
    right: auto !important;
}

body.cv-pdf-export .cv-pdf-sidebar-bg--right {
    right: 0 !important;
    left: auto !important;
}

body.cv-pdf-export .cv-page,
body.cv-pdf-export .cv-content,
body.cv-pdf-export .cv-main,
body.cv-pdf-export .cv-aside {
    position: relative !important;
    z-index: 1 !important;
}

body.cv-pdf-export .cv-grid-sidebar,
body.cv-pdf-export .cv-pdf-columns {
    z-index: auto !important;
}

body.cv-pdf-export .cv-page,
body.cv-pdf-export .cv-content,
body.cv-pdf-export .cv-grid-sidebar,
body.cv-pdf-export .cv-aside,
body.cv-pdf-export .cv-main,
body.cv-pdf-export table.cv-grid-sidebar--pdf {
    min-height: auto !important;
    height: auto !important;
    overflow: visible !important;
}

/* Long items may split between bullets (avoids large empty gaps at page bottoms); small blocks stay together. */
body.cv-pdf-export .cv-item {
    page-break-inside: auto !important;
    break-inside: auto !important;
}

/* Prefer keeping short bullets together, but never force a page-tall paragraph onto one page. */
body.cv-pdf-export .cv-item li {
    break-inside: avoid-page !important;
    orphans: 2;
    widows: 2;
}

/* Smart timeline vs bullets: keep discs inside the padded content column. */
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-item {
    padding-left: 2.75rem !important;
    margin-left: .65rem !important;
}
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-item::before {
    left: -.42rem !important;
}
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-rich-text ul,
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-rich-text ol,
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-item-text ul,
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-item-text ol {
    list-style: disc !important;
    list-style-type: disc !important;
    list-style-position: inside !important;
    padding-left: .5rem !important;
    margin-left: 0 !important;
}
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-rich-text li,
body.cv-pdf-export .cv-layout-smart .cv-section-experience .cv-item-text li {
    list-style: disc !important;
    list-style-type: disc !important;
    list-style-position: inside !important;
    padding-left: 0 !important;
}
body.cv-pdf-export .cv-layout-smart .cv-aside .cv-header-name {
    color: #ffffff !important;
    font-size: calc(var(--cv-font-size, 14px) * 1.15) !important;
    line-height: 1.3 !important;
    overflow-wrap: anywhere !important;
    word-break: break-word !important;
}

body.cv-pdf-export .cv-item p,
body.cv-pdf-export .cv-rich-text p,
body.cv-pdf-export .cv-header-summary p {
    break-inside: auto !important;
    page-break-inside: auto !important;
    orphans: 2;
    widows: 2;
}

body.cv-pdf-export .cv-main,
body.cv-pdf-export .cv-aside,
body.cv-pdf-export .cv-content,
body.cv-pdf-export .cv-rich-text,
body.cv-pdf-export .cv-item-text,
body.cv-pdf-export .cv-header-summary {
    word-wrap: break-word !important;
    overflow-wrap: break-word !important;
    max-width: 100% !important;
}

body.cv-pdf-export .cv-tags,
body.cv-pdf-export .cv-list-row {
    page-break-inside: avoid !important;
    break-inside: avoid-page !important;
}

body.cv-pdf-export .cv-section-title,
body.cv-pdf-export .cv-item-head,
body.cv-pdf-export .cv-item-title:not(:last-child),
body.cv-pdf-export .cv-item-subtitle:not(:last-child) {
    page-break-after: avoid !important;
    break-after: avoid-page !important;
}

body.cv-pdf-export .cv-section,
body.cv-pdf-export .cv-header,
body.cv-pdf-export .cv-group {
    page-break-inside: auto !important;
    break-inside: auto !important;
}

body.cv-pdf-export .cv-section-title {
    display: block !important;
}

body.cv-pdf-export img:not(.cv-watermark-logo):not(.pdf-watermark__img) {
    max-width: 100% !important;
    height: auto !important;
}

/* Trailing padding/margins must not spill a near-empty extra page (page bottom margin already gives the spacing). */
body.cv-pdf-export .cv-page,
body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-aside,
body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-main,
body.cv-pdf-export .cv-grid-sidebar > .cv-aside,
body.cv-pdf-export .cv-grid-sidebar > .cv-main,
body.cv-pdf-export .cv-content {
    padding-bottom: 0 !important;
}

/* Empty placeholder sections (e.g. no custom sections) must not push a blank trailing page. */
body.cv-pdf-export .cv-section:empty {
    display: none !important;
}

body.cv-pdf-export .cv-section:last-of-type,
body.cv-pdf-export .cv-section:last-of-type .cv-item:last-child {
    margin-bottom: 0 !important;
}

* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
CSS;
    }
}
