<style>
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
    body.cv-body { margin: 0; background: #f1f5f9; font-family: {{ $fontFamily ?? 'Figtree, ui-sans-serif, system-ui, sans-serif' }}; font-size: var(--cv-font-size); font-weight: var(--cv-font-weight); color: var(--cv-text); -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .cv-page { position: relative; background: var(--cv-bg); color: var(--cv-text); }
    .cv-body--preview .cv-page { overflow: hidden; width: 794px; max-width: 794px; min-width: 794px; margin: 0 auto; min-height: 1123px; box-shadow: 0 10px 40px rgba(15,23,42,.08); }
    .cv-body--preview .cv-grid-sidebar { display: grid; grid-template-columns: 240px 1fr; min-height: 1123px; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right { grid-template-columns: 1fr 240px; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right .cv-aside { order: 2; }
    .cv-body--preview .cv-grid-sidebar.cv-sidebar--right .cv-main { order: 1; }
    .cv-body--export,
    .cv-body--pdf { background: #ffffff; margin: 0; padding: 0; }
    .cv-body--export .cv-page,
    .cv-body--pdf .cv-page {
        width: 100%;
        max-width: none;
        min-height: auto;
        height: auto;
        margin: 0;
        overflow: visible;
        box-shadow: none;
        page-break-after: auto;
    }
    .cv-body--thumbnail .cv-page { width: 794px; min-height: 1123px; transform: scale({{ $modeConfig['scale'] ?? 0.35 }}); transform-origin: top left; }
    .cv-body--export .cv-item,
    .cv-body--pdf .cv-item { page-break-inside: auto; break-inside: auto; }
    .cv-body--export .cv-item-head,
    .cv-body--pdf .cv-item-head { page-break-after: avoid; break-after: avoid-page; }
    .cv-content, .cv-grid-sidebar { padding: var(--cv-page-padding); }
    .cv-grid-sidebar { display: grid; grid-template-columns: 240px 1fr; gap: 0; padding: 0; min-height: 1123px; }
    .cv-body--export .cv-grid-sidebar,
    .cv-body--pdf .cv-grid-sidebar { min-height: auto; }
    .cv-body--preview .cv-grid-sidebar { gap: 2rem; }
    .cv-aside { background: var(--cv-sidebar-bg, color-mix(in srgb, var(--cv-accent) 8%, white)); padding: 2rem 1.5rem; }
    .cv-main { padding: var(--cv-page-padding) 2rem; background: var(--cv-bg); }
    .cv-section { margin-bottom: var(--cv-section-gap); }
    .cv-section:last-child { margin-bottom: 0; }
    .cv-section-title { font-size: calc(var(--cv-font-size) * 0.72); font-weight: var(--cv-heading-weight); font-style: var(--cv-heading-style); letter-spacing: .12em; text-transform: uppercase; color: var(--cv-accent); border-bottom: var(--cv-border-width) var(--cv-border-style) var(--cv-accent-border); border-radius: var(--cv-border-radius); padding-bottom: .45rem; margin: 0 0 1rem; }
    .cv-item { margin-bottom: var(--cv-item-gap); }
    .cv-item:last-child { margin-bottom: 0; }
    .cv-item-head { display: flex; flex-wrap: wrap; justify-content: space-between; gap: .35rem .75rem; margin-bottom: .2rem; }
    .cv-item-title { font-weight: var(--cv-heading-weight); font-size: calc(var(--cv-font-size) * 0.95); }
    .cv-item-subtitle { color: var(--cv-secondary); font-size: calc(var(--cv-font-size) * 0.86); font-weight: 600; }
    .cv-item-meta { color: var(--cv-muted); font-size: calc(var(--cv-font-size) * 0.78); white-space: nowrap; }
    .cv-item-text { color: var(--cv-text); font-size: calc(var(--cv-font-size) * 0.84); line-height: 1.55; margin: .35rem 0 0; }
    .cv-rich-text { white-space: normal; }
    .cv-rich-text p { margin: .35rem 0 0; }
    .cv-rich-text p:first-child { margin-top: 0; }
    .cv-rich-text ul, .cv-rich-text ol { margin: .35rem 0 0; padding-left: 1.25rem; }
    .cv-rich-text li { margin: .15rem 0; }
    .cv-rich-text h1 { font-size: calc(var(--cv-font-size) * 1.15); font-weight: var(--cv-heading-weight); margin: .5rem 0 .25rem; line-height: 1.25; }
    .cv-rich-text h2 { font-size: calc(var(--cv-font-size) * 1.05); font-weight: var(--cv-heading-weight); margin: .45rem 0 .2rem; line-height: 1.3; }
    .cv-rich-text h3 { font-size: calc(var(--cv-font-size) * 0.98); font-weight: var(--cv-heading-weight); margin: .4rem 0 .2rem; line-height: 1.35; }
    .cv-rich-text h4 { font-size: calc(var(--cv-font-size) * 0.92); font-weight: var(--cv-heading-weight); margin: .35rem 0 .15rem; }
    .cv-rich-text strong, .cv-rich-text b { font-weight: 700; }
    .cv-rich-text em, .cv-rich-text i { font-style: italic; }
    .cv-rich-text u { text-decoration: underline; }
    .cv-rich-text a { color: var(--cv-accent); text-decoration: underline; word-break: break-word; }
    .cv-rich-text .ql-align-center { text-align: center; }
    .cv-rich-text .ql-align-right { text-align: right; }
    .cv-rich-text .ql-align-justify { text-align: justify; }
    .cv-header { margin-bottom: var(--cv-section-gap); }
    .cv-header-body { min-width: 0; }
    .cv-header-name { font-size: calc(var(--cv-font-size) * 2); font-weight: var(--cv-heading-weight); line-height: 1.1; margin: 0; }
    .cv-header-role { font-size: calc(var(--cv-font-size) * 1.05); color: var(--cv-secondary); font-weight: 600; margin: .35rem 0 0; }
    .cv-header-contact { display: flex; flex-wrap: wrap; gap: .5rem 1rem; margin-top: .85rem; font-size: calc(var(--cv-font-size) * 0.78); color: var(--cv-muted); }
    .cv-header-summary { margin-top: 1rem; font-size: calc(var(--cv-font-size) * 0.88); line-height: 1.65; color: var(--cv-text); }
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
    .cv-tag { display: inline-flex; align-items: center; border-radius: 999px; background: var(--cv-tag-bg); color: var(--cv-accent); padding: .25rem .7rem; font-size: .78rem; font-weight: 600; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

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
    .cv-body--embedded .cv-tag,
    .cv-body--export .cv-tag,
    .cv-body--pdf .cv-tag {
        background-color: var(--cv-tag-bg);
        color: var(--cv-tag-color, #ffffff);
    }
    .cv-body--export .cv-page,
    .cv-body--pdf .cv-page,
    .cv-body--export .cv-content,
    .cv-body--pdf .cv-content,
    .cv-body--export .cv-main,
    .cv-body--pdf .cv-main {
        background-color: var(--cv-bg);
        color: var(--cv-text);
    }
    .cv-body--export .cv-header-name,
    .cv-body--pdf .cv-header-name,
    .cv-body--export .cv-item-title,
    .cv-body--pdf .cv-item-title,
    .cv-body--export .cv-list-row,
    .cv-body--pdf .cv-list-row,
    .cv-body--export .cv-group-title,
    .cv-body--pdf .cv-group-title {
        color: var(--cv-text);
    }
    .cv-body--export .cv-layout-modern .cv-header-name,
    .cv-body--pdf .cv-layout-modern .cv-header-name {
        color: var(--cv-accent);
    }
    .cv-body--preview .cv-layout-modern .cv-header-name,
    .cv-body--embedded .cv-layout-modern .cv-header-name {
        color: var(--cv-accent);
    }
    .cv-list-row { display: flex; justify-content: space-between; gap: 1rem; font-size: .84rem; padding: .25rem 0; }
    .cv-list-row strong { font-weight: 600; }
    .cv-group { margin-bottom: 1.25rem; }
    .cv-group-title { font-weight: 700; font-size: .92rem; margin: 0 0 .65rem; }
    .cv-watermark { position: absolute; inset: 0; display: none; pointer-events: none; user-select: none; z-index: 9999; overflow: hidden; }
    .cv-watermark-inner { display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.65rem; text-align: center; padding: 1rem; }
    .cv-watermark-primary { font-size: 3.25rem; font-weight: 800; letter-spacing: 0.14em; text-transform: uppercase; color: rgba(71,85,105,{{ config('cv-templates.watermark.text_opacity', 0.20) }}); line-height: 1; white-space: nowrap; }
    .cv-watermark-secondary { font-size: 1.05rem; font-weight: 600; letter-spacing: 0.06em; text-transform: uppercase; color: rgba(71,85,105,{{ max(config('cv-templates.watermark.text_opacity', 0.20) - 0.03, 0.12) }}); line-height: 1.2; white-space: nowrap; }
    .cv-body--export .cv-watermark,
    .cv-body--pdf .cv-watermark {
        display: none;
    }

    /* Layout variants — 10 premium ATS templates */
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
    .cv-layout-minimal .cv-header-role { color: var(--cv-muted); font-weight: 500; }

    .cv-layout-executive .cv-header { text-align: center; }
    .cv-layout-executive .cv-header-name { letter-spacing: .06em; text-transform: uppercase; font-size: calc(var(--cv-font-size) * 1.85); }
    .cv-layout-executive .cv-header-role { color: var(--cv-secondary); letter-spacing: .04em; text-transform: uppercase; font-size: calc(var(--cv-font-size) * 0.9); }
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
    .cv-layout-corporate .cv-main .cv-section-title { color: var(--cv-accent); }

    .cv-layout-creative .cv-header { border-left: 6px solid var(--cv-accent); padding-left: 1rem; }
    .cv-layout-creative .cv-section-title { color: var(--cv-accent); }
    .cv-layout-creative .cv-tag { border-radius: var(--cv-border-radius, 4px); background: color-mix(in srgb, var(--cv-secondary) 18%, white); color: var(--cv-accent); }

    .cv-layout-premium .cv-header { border-top: 4px solid var(--cv-secondary); padding-top: 1.35rem; }
    .cv-layout-premium .cv-header-name { letter-spacing: .03em; }
    .cv-layout-premium .cv-header-role { color: var(--cv-secondary); font-weight: 600; }
    .cv-layout-premium .cv-section-title { color: var(--cv-secondary); border-bottom-color: var(--cv-secondary); }

    .cv-layout-smart .cv-section-experience .cv-item { border-left: 2px solid var(--cv-accent-border); padding-left: 1rem; margin-left: .35rem; position: relative; }
    .cv-layout-smart .cv-section-experience .cv-item::before { content: ''; position: absolute; left: -.47rem; top: .4rem; width: .55rem; height: .55rem; border-radius: 50%; background: var(--cv-accent); }
    .cv-layout-smart .cv-section-title { letter-spacing: .08em; }

    @media (max-width: 768px) {
        .cv-body:not(.cv-body--preview) .cv-grid-sidebar { grid-template-columns: 1fr; }
        .cv-body:not(.cv-body--preview) .cv-aside { order: 2; }
        .cv-content, .cv-main, .cv-aside { padding: 1.25rem !important; }
        .cv-header-name { font-size: 1.55rem; }
    }

    @media print {
        @page { size: A4; margin: 0; }
        body.cv-body { background: #fff; margin: 0; padding: 0; }
        .cv-body--export,
        .cv-body--pdf { width: 100% !important; max-width: none !important; margin: 0 !important; padding: 0 !important; }
        .cv-page { box-shadow: none; width: 100% !important; max-width: none !important; min-height: auto !important; height: auto !important; margin: 0 !important; overflow: visible !important; }
        .cv-body--export .cv-grid-sidebar,
        .cv-body--pdf .cv-grid-sidebar { width: 100% !important; }
        .cv-grid-sidebar { min-height: auto !important; }
        .cv-body--export .cv-item,
        .cv-body--pdf .cv-item { page-break-inside: auto; break-inside: auto; }
        .cv-body--export .cv-item-head,
        .cv-body--pdf .cv-item-head { page-break-after: avoid; break-after: avoid-page; }
        * { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; }
    }

    /* PDF export: match live preview grid layout (not scaled preview iframe) */
    .cv-body--export .cv-grid-sidebar,
    .cv-body--pdf .cv-grid-sidebar {
        display: grid;
        grid-template-columns: 30.23% 1fr;
        width: 100%;
        min-height: auto;
    }
    .cv-body--export .cv-grid-sidebar.cv-sidebar--right,
    .cv-body--pdf .cv-grid-sidebar.cv-sidebar--right {
        grid-template-columns: 1fr 30.23%;
    }
    .cv-body--export .cv-grid-sidebar.cv-sidebar--right .cv-aside,
    .cv-body--pdf .cv-grid-sidebar.cv-sidebar--right .cv-aside {
        order: 2;
    }
    .cv-body--export .cv-grid-sidebar.cv-sidebar--right .cv-main,
    .cv-body--pdf .cv-grid-sidebar.cv-sidebar--right .cv-main {
        order: 1;
    }
    .cv-body--export .cv-aside,
    .cv-body--pdf .cv-aside {
        background: var(--cv-sidebar-bg, {{ $accentSoft ?? '#f8fafc' }});
    }
    .cv-body--export .cv-main,
    .cv-body--pdf .cv-main {
        background: var(--cv-bg, #ffffff);
        min-width: 0;
    }
    .cv-body--export .cv-section-title,
    .cv-body--pdf .cv-section-title {
        border-bottom-color: var(--cv-accent-border);
    }
    .cv-body--export .cv-header-photo,
    .cv-body--pdf .cv-header-photo {
        border-color: var(--cv-photo-border);
    }
    .cv-body--export .cv-header--photo-left,
    .cv-body--export .cv-header--photo-right,
    .cv-body--export .cv-header--photo-center,
    .cv-body--pdf .cv-header--photo-left,
    .cv-body--pdf .cv-header--photo-right,
    .cv-body--pdf .cv-header--photo-center {
        display: block;
    }
    .cv-body--export .cv-header--photo-center .cv-header-photo,
    .cv-body--pdf .cv-header--photo-center .cv-header-photo {
        display: block;
        margin: 0 auto 1rem;
    }
    .cv-body--export .cv-header--photo-left .cv-header-photo,
    .cv-body--pdf .cv-header--photo-left .cv-header-photo {
        float: left;
        margin: 0 1.25rem 0 0;
    }
    .cv-body--export .cv-header--photo-right .cv-header-photo,
    .cv-body--pdf .cv-header--photo-right .cv-header-photo {
        float: right;
        margin: 0 0 0 1.25rem;
    }
    .cv-body--export .cv-header-body,
    .cv-body--pdf .cv-header-body {
        overflow: hidden;
    }
    .cv-body--export .cv-header-contact,
    .cv-body--pdf .cv-header-contact {
        display: block;
    }
    .cv-body--export .cv-header-contact span,
    .cv-body--pdf .cv-header-contact span {
        display: inline-block;
        margin: 0 1rem 0.35rem 0;
    }
    .cv-body--export .cv-item-head,
    .cv-body--pdf .cv-item-head {
        display: block;
        overflow: hidden;
    }
    .cv-body--export .cv-item-meta,
    .cv-body--pdf .cv-item-meta {
        float: right;
    }
    .cv-body--export .cv-tags,
    .cv-body--pdf .cv-tags {
        display: block;
    }
    .cv-body--export .cv-tag,
    .cv-body--pdf .cv-tag {
        display: inline-block;
        margin: 0 0.45rem 0.45rem 0;
    }
    .cv-body--export .cv-list-row,
    .cv-body--pdf .cv-list-row {
        display: block;
        overflow: hidden;
    }
    .cv-body--export .cv-list-row span:last-child,
    .cv-body--pdf .cv-list-row span:last-child {
        float: right;
    }
    {!! \App\Support\FontAwesome\FontAwesomeAssets::iconStylesCss() !!}
</style>
