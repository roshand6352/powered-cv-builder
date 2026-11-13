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
/* Continuation pages get a top/bottom margin so content never starts at the paper edge; page 1 keeps the preview's own top. */
@page { size: 794px 1123px; margin: 40px 0 32px 0; }
@page :first { margin-top: 0; }

html, body.cv-body.cv-pdf-export,
body.cv-body.cv-body--preview.cv-body--embedded.cv-pdf-export {
    margin: 0 !important;
    padding: 0 !important;
    background: #ffffff !important;
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

body.cv-pdf-export .cv-item li,
body.cv-pdf-export .cv-item p {
    break-inside: avoid-page !important;
    orphans: 2;
    widows: 2;
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
