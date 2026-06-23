{{-- Engine compatibility only — no per-template design rules (those come from live preview CSS). --}}
<style id="cv-pdf-layout-styles">
/*
 * Generic PDF engine layer: flex/grid fallbacks and page flow.
 * Template colors, typography, and layout variants remain in the captured preview styles.
 */

body.cv-pdf-export .cv-section-title {
    display: block !important;
    width: 100% !important;
}

body.cv-pdf-export .cv-section-title .cv-section-icon,
body.cv-pdf-export .cv-section-title .cv-pdf-icon.cv-section-icon {
    display: inline-block !important;
    vertical-align: middle !important;
}

body.cv-pdf-export .cv-section-title .cv-section-title-text {
    display: inline !important;
    vertical-align: middle !important;
}

body.cv-pdf-export .cv-item-head {
    display: block !important;
    overflow: hidden !important;
}

body.cv-pdf-export .cv-item-title {
    display: inline !important;
}

body.cv-pdf-export .cv-item-meta {
    display: inline !important;
    float: right !important;
    white-space: nowrap !important;
}

body.cv-pdf-export .cv-item-subtitle {
    clear: both !important;
}

body.cv-pdf-export .cv-header-contact {
    display: block !important;
}

body.cv-pdf-export .cv-header-contact > span,
body.cv-pdf-export .cv-header-contact > a {
    display: inline-block !important;
    vertical-align: middle !important;
}

body.cv-pdf-export .cv-header--photo-left .cv-header-photo {
    float: left !important;
}

body.cv-pdf-export .cv-header--photo-right .cv-header-photo {
    float: right !important;
}

body.cv-pdf-export .cv-header--photo-center .cv-header-photo {
    display: block !important;
    margin-left: auto !important;
    margin-right: auto !important;
}

body.cv-pdf-export .cv-header-body {
    overflow: hidden !important;
}

body.cv-pdf-export .cv-tags {
    display: block !important;
}

body.cv-pdf-export .cv-tag {
    display: inline-block !important;
}

body.cv-pdf-export .cv-list-row {
    display: block !important;
    overflow: hidden !important;
}

body.cv-pdf-export .cv-list-row span:last-child {
    float: right !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf {
    display: table !important;
    width: 100% !important;
    border-collapse: separate !important;
    border-spacing: 0 !important;
    table-layout: fixed !important;
}

body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-aside,
body.cv-pdf-export table.cv-grid-sidebar--pdf td.cv-main {
    vertical-align: top !important;
}

body.cv-pdf-export img {
    max-width: 100% !important;
    height: auto !important;
}
</style>
