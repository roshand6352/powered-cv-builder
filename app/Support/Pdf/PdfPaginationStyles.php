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
@page { size: 794px 1123px; margin: 0; }

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

body.cv-pdf-export .cv-item,
body.cv-pdf-export .cv-tags,
body.cv-pdf-export .cv-list-row {
    page-break-inside: avoid !important;
    break-inside: avoid-page !important;
}

body.cv-pdf-export .cv-section-title,
body.cv-pdf-export .cv-item-head {
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

* {
    -webkit-print-color-adjust: exact !important;
    print-color-adjust: exact !important;
}
CSS;
    }
}
