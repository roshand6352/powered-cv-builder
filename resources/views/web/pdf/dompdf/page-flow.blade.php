{{-- DomPDF only: sidebar colour column repeated on every page beside the floated sidebar. --}}
@php($darkSidebar = \App\Support\Cv\CvColor::readableTextColor($vars['--cv-sidebar-bg']) === '#ffffff')
<style id="cv-pdf-dompdf-page-flow">
body.cv-pdf-export .cv-pdf-sidebar-bg {
    position: fixed;
    top: 0;
    width: 240px;
    /* Must stay within the 1123px page box, otherwise DomPDF stops repeating it after page 1. */
    height: 1120px;
    background: {{ $vars['--cv-sidebar-bg'] }};
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
