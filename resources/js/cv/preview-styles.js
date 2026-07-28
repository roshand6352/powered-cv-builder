export const PREVIEW_CSS = `
    :root {
        --cv-accent: #172d40;
        --cv-secondary: #c1623d;
        --cv-text: #3a4a58;
        --cv-muted: #64748b;
        --cv-border: #e2e8f0;
        --cv-accent-border: #d5dee8;
        --cv-tag-bg: #eef2f6;
        --cv-photo-border: #d5dee8;
        --cv-bg: #ffffff;
        --cv-sidebar-bg: #f4f7f9;
        --cv-font-size: 14px;
        --cv-font-weight: 400;
        --cv-heading-weight: 700;
        --cv-heading-style: normal;
        --cv-page-padding: 2.5rem;
        --cv-section-gap: 1.75rem;
        --cv-item-gap: 1.1rem;
        --cv-border-style: solid;
        --cv-border-width: 2px;
        --cv-border-radius: 0px;
    }
    * { box-sizing: border-box; }
    body.cv-body {
        margin: 0;
        background: #f1f5f9;
        font-family: __FONT_FAMILY__;
        font-size: var(--cv-font-size);
        font-weight: var(--cv-font-weight);
        color: var(--cv-text);
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
    .cv-body--embedded {
        background: #ffffff;
        padding: 0;
    }
    .cv-page { position: relative; background: var(--cv-bg); color: var(--cv-text); overflow: visible; }
    html, body.cv-body--preview { height: 100%; }
    .cv-body--preview { overflow: auto; min-height: 100%; height: 100%; }
    .cv-body--embedded .cv-page {
        width: 794px;
        max-width: none;
        min-width: 794px;
        margin: 0;
        min-height: 1123px;
        height: auto;
        box-shadow: none;
    }
    .cv-body--preview .cv-page { width: 794px; max-width: 794px; min-width: 794px; margin: 0 auto; min-height: 1123px; height: auto; box-shadow: 0 10px 40px rgba(15,23,42,.08); }
    .cv-content, .cv-grid-sidebar { padding: var(--cv-page-padding); }
    .cv-grid-sidebar { display: grid; grid-template-columns: 240px 1fr; gap: 2rem; padding: 0; min-height: 1123px; height: auto; }
    .cv-body--preview .cv-grid-sidebar { display: grid; grid-template-columns: 240px 1fr; min-height: 1123px; height: auto; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right { grid-template-columns: 1fr 240px; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right .cv-aside { order: 2; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right .cv-main { order: 1; }
    .cv-aside { background: var(--cv-sidebar-bg, color-mix(in srgb, var(--cv-accent) 8%, white)); padding: 2rem 1.5rem; }
    .cv-main { padding: var(--cv-page-padding) 2rem; background: var(--cv-bg); }
    .cv-section { margin-bottom: var(--cv-section-gap); }
    .cv-section:last-child { margin-bottom: 0; }
    .cv-section-title {
        font-size: calc(var(--cv-font-size) * 0.72);
        font-weight: var(--cv-heading-weight);
        font-style: var(--cv-heading-style);
        letter-spacing: .12em;
        text-transform: uppercase;
        color: var(--cv-accent);
        border-bottom: var(--cv-border-width) var(--cv-border-style) var(--cv-accent-border);
        border-radius: var(--cv-border-radius);
        padding-bottom: .45rem;
        margin: 0 0 1rem;
    }
    .cv-item { margin-bottom: var(--cv-item-gap); }
    .cv-item:last-child { margin-bottom: 0; }
    .cv-item-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: .35rem .75rem; margin-bottom: .2rem; }
    .cv-item-title { font-weight: var(--cv-heading-weight); font-size: calc(var(--cv-font-size) * 0.95); }
    .cv-item-subtitle { color: var(--cv-secondary); font-size: calc(var(--cv-font-size) * 0.86); font-weight: 600; }
    .cv-item-meta { color: var(--cv-muted); font-size: calc(var(--cv-font-size) * 0.78); white-space: nowrap; }
    .cv-item-text { color: var(--cv-text); font-size: calc(var(--cv-font-size) * 0.84); line-height: 1.55; white-space: pre-line; margin: .35rem 0 0; }
    .cv-header { margin-bottom: var(--cv-section-gap); }
    .cv-header-body { min-width: 0; }
    .cv-header-name { font-size: calc(var(--cv-font-size) * 2); font-weight: var(--cv-heading-weight); line-height: 1.1; margin: 0; }
    .cv-header-role { font-size: calc(var(--cv-font-size) * 1.05); color: var(--cv-secondary); font-weight: 600; margin: .35rem 0 0; }
    .cv-header-contact { display: flex; flex-wrap: wrap; gap: .5rem 1rem; margin-top: .85rem; font-size: calc(var(--cv-font-size) * 0.78); color: var(--cv-muted); }
    .cv-header-summary { margin-top: 1rem; font-size: calc(var(--cv-font-size) * 0.88); line-height: 1.65; color: var(--cv-text); white-space: pre-line; }
    .cv-header-photo { width: 96px; height: 96px; border-radius: 999px; object-fit: cover; border: 3px solid var(--cv-photo-border); flex-shrink: 0; }
    .cv-header-photo--sidebar { display: block; margin: 0 auto 1.25rem; }
    .cv-sidebar-photo { margin-bottom: 1rem; text-align: center; }
    .cv-header--photo-left,
    .cv-header--photo-right { display: flex; gap: 1.25rem; align-items: flex-start; }
    .cv-header--photo-right { flex-direction: row-reverse; }
    .cv-header--photo-left .cv-header-photo,
    .cv-header--photo-right .cv-header-photo { margin-bottom: 0; }
    .cv-header--photo-center { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .cv-header--photo-center .cv-header-photo { margin-bottom: 1rem; }
    .cv-header--photo-center .cv-header-contact { justify-content: center; }
    .cv-header--photo-hidden .cv-header-photo,
    .cv-header--photo-sidebar .cv-header-photo { display: none; }
    .cv-grid-sidebar.cv-sidebar--right { grid-template-columns: 1fr 240px; }
    .cv-grid-sidebar.cv-sidebar--right .cv-aside { order: 2; }
    .cv-grid-sidebar.cv-sidebar--right .cv-main { order: 1; }
    .cv-tags { display: flex; flex-wrap: wrap; gap: .45rem; }
    .cv-tag { display: inline-flex; align-items: center; border-radius: 999px; background: var(--cv-tag-bg); color: var(--cv-accent); padding: .25rem .7rem; font-size: calc(var(--cv-font-size) * 0.78); font-weight: 600; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

    /* Live preview & builder: accurate template colors from admin layout_json */
    .cv-body--preview .cv-page,
    .cv-body--embedded .cv-page {
        background-color: var(--cv-bg);
        color: var(--cv-text);
    }
    .cv-body--preview .cv-content,
    .cv-body--embedded .cv-content {
        background-color: var(--cv-bg);
    }
    .cv-body--preview .cv-main,
    .cv-body--embedded .cv-main {
        background-color: var(--cv-bg);
    }
    .cv-body--preview .cv-header-name,
    .cv-body--embedded .cv-header-name {
        color: var(--cv-text);
    }
    .cv-body--preview .cv-item-title,
    .cv-body--embedded .cv-item-title,
    .cv-body--preview .cv-list-row,
    .cv-body--embedded .cv-list-row,
    .cv-body--preview .cv-group-title,
    .cv-body--embedded .cv-group-title {
        color: var(--cv-text);
    }
    .cv-body--preview .cv-tag,
    .cv-body--embedded .cv-tag {
        background-color: var(--cv-tag-bg);
        color: var(--cv-tag-color, #ffffff);
    }
    .cv-body--preview .cv-layout-modern .cv-header-name,
    .cv-body--embedded .cv-layout-modern .cv-header-name {
        color: var(--cv-accent);
    }
    .cv-list-row { display: flex; justify-content: space-between; gap: 1rem; font-size: calc(var(--cv-font-size) * 0.84); padding: .25rem 0; }
    .cv-list-row strong { font-weight: 600; }
    .cv-group { margin-bottom: 1.25rem; }
    .cv-group-title { font-weight: var(--cv-heading-weight); font-size: calc(var(--cv-font-size) * 0.92); margin: 0 0 .65rem; }
    .cv-layout-classic .cv-section-title { color: var(--cv-text); border-bottom-color: var(--cv-text); }
    .cv-layout-classic .cv-header-role { color: var(--cv-muted); }
    .cv-layout-classic .cv-header { text-align: center; }
    .cv-layout-classic .cv-header-contact { justify-content: center; }
    .cv-layout-modern .cv-header-name { color: var(--cv-accent); }
    .cv-layout-modern .cv-section-title { border-bottom-color: var(--cv-accent); }
    .cv-layout-professional .cv-header { border-bottom: 2px solid var(--cv-accent); padding-bottom: 1.25rem; }
    .cv-layout-professional .cv-section-title { border-bottom: none; border-left: 3px solid var(--cv-secondary); padding-left: .65rem; }
    .cv-layout-professional .cv-item-subtitle { color: var(--cv-secondary); font-weight: 600; }
    .cv-layout-minimal .cv-section-title { border-bottom-color: var(--cv-border); color: var(--cv-text); letter-spacing: .18em; font-weight: 600; }
    .cv-layout-minimal .cv-header-name { font-weight: 600; font-size: calc(var(--cv-font-size) * 1.75); }
    .cv-layout-executive .cv-header { text-align: center; }
    .cv-layout-executive .cv-header-name { letter-spacing: .06em; text-transform: uppercase; font-size: calc(var(--cv-font-size) * 1.85); }
    .cv-layout-executive .cv-header-role { color: var(--cv-secondary); letter-spacing: .04em; text-transform: uppercase; }
    .cv-layout-executive .cv-section-title { border-bottom-width: 3px; letter-spacing: .1em; }
    .cv-layout-elegant .cv-header-role { font-style: italic; font-weight: 500; color: var(--cv-secondary); }
    .cv-layout-elegant .cv-section-title { border-bottom: 1px solid var(--cv-border); letter-spacing: .16em; font-weight: 600; }
    .cv-layout-corporate .cv-aside { background: var(--cv-sidebar-bg, var(--cv-accent)); color: #f8fafc; }
    .cv-layout-corporate .cv-aside .cv-section-title,
    .cv-layout-corporate .cv-aside .cv-item-title,
    .cv-layout-corporate .cv-aside .cv-list-row,
    .cv-layout-corporate .cv-aside .cv-group-title { color: #f8fafc; }
    .cv-layout-corporate .cv-aside .cv-section-title { border-bottom-color: rgba(255,255,255,.28); }
    .cv-layout-corporate .cv-aside .cv-item-subtitle,
    .cv-layout-corporate .cv-aside .cv-item-meta,
    .cv-layout-corporate .cv-aside .cv-item-text { color: rgba(248,250,252,.88); }
    .cv-layout-corporate .cv-aside .cv-tag { background: rgba(255,255,255,.14); color: #fff; }
    .cv-layout-corporate .cv-aside .cv-section-icon,
    .cv-layout-corporate .cv-aside .cv-contact-icon { color: var(--cv-secondary); }
    .cv-layout-creative .cv-header { border-left: 6px solid var(--cv-accent); padding-left: 1rem; }
    .cv-layout-creative .cv-tag { border-radius: var(--cv-border-radius, 4px); }
    .cv-layout-premium .cv-header { border-top: 4px solid var(--cv-secondary); padding-top: 1.35rem; }
    .cv-layout-premium .cv-header-role { color: var(--cv-secondary); font-weight: 600; }
    .cv-layout-premium .cv-section-title { color: var(--cv-secondary); border-bottom-color: var(--cv-secondary); }
    .cv-layout-smart .cv-section-experience .cv-item { border-left: 2px solid var(--cv-accent-border); padding-left: 1rem; margin-left: .35rem; position: relative; }
    .cv-layout-smart .cv-section-experience .cv-item::before { content: ''; position: absolute; left: -.47rem; top: .4rem; width: .55rem; height: .55rem; border-radius: 50%; background: var(--cv-accent); }
    .pdf-watermark {
        position: fixed;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        display: none;
        align-items: center;
        justify-content: center;
        pointer-events: none;
        user-select: none;
        z-index: 2147483000;
        overflow: visible;
        background: transparent;
    }
    .cv-body--preview .pdf-watermark,
    .cv-body--embedded .pdf-watermark {
        display: flex !important;
    }
    .pdf-watermark__img {
        display: block !important;
        width: var(--wm-width, 900px) !important;
        max-width: none !important;
        height: auto !important;
        opacity: var(--wm-opacity, 0.20) !important;
        transform: rotate(var(--wm-angle, -30deg)) !important;
        transform-origin: center center !important;
        object-fit: contain !important;
        image-rendering: auto !important;
    }
    @media (max-width: 768px) {
        .cv-body:not(.cv-body--preview) .cv-grid-sidebar { grid-template-columns: 1fr; }
        .cv-body:not(.cv-body--preview) .cv-aside { order: 2; }
        .cv-content, .cv-main, .cv-aside { padding: 1.25rem !important; }
        .cv-header-name { font-size: calc(var(--cv-font-size) * 1.55); }
    }
`;

export const THUMBNAIL_CSS = PREVIEW_CSS + `
    html, body.cv-body--thumbnail { margin: 0; padding: 0; width: 100%; height: 100%; overflow: hidden; background: #fff; }
    body.cv-body--thumbnail { font-family: __FONT_FAMILY__; display: flex; justify-content: center; align-items: flex-start; }
    .cv-body--thumbnail .pdf-watermark { display: none !important; }
    .cv-thumb-wrap { width: 100%; height: 100%; overflow: hidden; display: flex; justify-content: center; align-items: flex-start; }
    .cv-body--thumbnail .cv-page {
        width: 794px;
        min-height: 1123px;
        transform: scale(var(--thumb-scale, 0.30));
        transform-origin: top center;
        box-shadow: 0 4px 18px rgba(15, 23, 42, 0.12);
        margin: 0;
        max-width: none;
    }
    .cv-body--thumbnail .cv-header-photo { width: 80px; height: 80px; }
    .cv-body--thumbnail .cv-header-name { font-size: calc(var(--cv-font-size) * 1.5); }
    .cv-body--thumbnail .cv-header-role { font-size: calc(var(--cv-font-size) * 0.82); }
    .cv-body--thumbnail .cv-header-contact { font-size: calc(var(--cv-font-size) * 0.62); gap: 0.3rem 0.55rem; margin-top: 0.55rem; }
    .cv-body--thumbnail .cv-header-summary { font-size: calc(var(--cv-font-size) * 0.68); line-height: 1.45; margin-top: 0.55rem; }
    .cv-body--thumbnail .cv-section { margin-bottom: 0.9rem; }
    .cv-body--thumbnail .cv-section-title { font-size: calc(var(--cv-font-size) * 0.58); margin-bottom: 0.5rem; padding-bottom: 0.3rem; }
    .cv-body--thumbnail .cv-item { margin-bottom: 0.6rem; }
    .cv-body--thumbnail .cv-item-title { font-size: calc(var(--cv-font-size) * 0.65); }
    .cv-body--thumbnail .cv-item-subtitle { font-size: calc(var(--cv-font-size) * 0.58); }
    .cv-body--thumbnail .cv-item-meta { font-size: calc(var(--cv-font-size) * 0.54); }
    .cv-body--thumbnail .cv-item-text { font-size: calc(var(--cv-font-size) * 0.56); line-height: 1.4; }
    .cv-body--thumbnail .cv-tag { font-size: calc(var(--cv-font-size) * 0.54); padding: 0.14rem 0.4rem; }
    .cv-body--thumbnail .cv-content,
    .cv-body--thumbnail .cv-main { padding: 1.25rem; }
    .cv-body--thumbnail .cv-aside { padding: 1rem 0.75rem; }
    .cv-body--thumbnail .cv-grid-sidebar { min-height: 1123px; grid-template-columns: 180px 1fr; gap: 1rem; }
    .cv-body--thumbnail .cv-grid-sidebar.cv-sidebar--right { grid-template-columns: 1fr 180px; }
`;
