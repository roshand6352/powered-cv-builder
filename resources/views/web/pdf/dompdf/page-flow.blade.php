{{-- DomPDF only: content-height sidebar colour beside the floated columns. --}}
@php($darkSidebar = \App\Support\Cv\CvColor::readableTextColor($vars['--cv-sidebar-bg']) === '#ffffff')
<style id="cv-pdf-dompdf-page-flow">
/* Stretch the columns wrapper to the taller float so absolute sidebar bg matches content. */
body.cv-pdf-export .cv-pdf-columns {
    position: relative;
}

body.cv-pdf-export .cv-pdf-columns::after {
    content: '';
    display: block;
    clear: both;
    height: 0;
}

body.cv-pdf-export .cv-pdf-sidebar-bg {
    position: absolute;
    top: 0;
    bottom: 0;
    width: 240px;
    height: auto;
    margin: 0;
    padding: 0;
    background: {{ $vars['--cv-sidebar-bg'] }};
    z-index: 0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
}

body.cv-pdf-export .cv-pdf-sidebar-bg--left {
    left: 0;
}

body.cv-pdf-export .cv-pdf-sidebar-bg--right {
    right: 0;
}

@if($darkSidebar)
/* Same high-contrast treatment the Corporate/Professional templates give their dark sidebar. */
body.cv-pdf-export .cv-pdf-columns .cv-aside,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-header-name,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-section-title,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-section-title-text,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-item-title,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-list-row,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-group-title {
    color: #f8fafc;
}

body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-header-name {
    color: #ffffff;
}

body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-header-contact,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-header-contact a,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-item-subtitle,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-item-meta,
body.cv-pdf-export .cv-pdf-columns .cv-aside .cv-item-text {
    color: #eef2f6;
}
@endif
</style>
