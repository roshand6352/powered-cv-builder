// Mirrors App\Support\Cv\CvTemplateLayoutResolver

const SIDEBAR_SECTION_KEYS = ['header', 'personal_details', 'skills', 'languages'];

const FONT_STYLESHEETS = {
    figtree: 'figtree:400,500,600,700',
    inter: 'inter:400,500,600,700',
    roboto: 'roboto:400,500,700',
    lora: 'lora:400,500,600,700',
    merriweather: 'merriweather:400,700',
    'open-sans': 'open-sans:400,500,600,700',
    sora: 'sora:400,500,600,700,800',
    'dm-sans': 'dm-sans:400,500,600,700',
};

const FONT_FAMILIES = {
    figtree: 'Figtree, ui-sans-serif, system-ui, sans-serif',
    inter: 'Inter, ui-sans-serif, system-ui, sans-serif',
    roboto: 'Roboto, ui-sans-serif, system-ui, sans-serif',
    lora: 'Lora, ui-serif, Georgia, serif',
    merriweather: 'Merriweather, ui-serif, Georgia, serif',
    'open-sans': 'Open Sans, ui-sans-serif, system-ui, sans-serif',
    sora: 'Sora, ui-sans-serif, system-ui, sans-serif',
    'dm-sans': 'DM Sans, ui-sans-serif, system-ui, sans-serif',
};

const DEFAULT_SETTINGS = {
    sidebar_position: 'none',
    photo_position: 'top_left',
    font: 'figtree',
    primary_color: '#172d40',
    secondary_color: '#c1623d',
    background_color: '#ffffff',
    sidebar_background_color: '#f4f7f9',
    text_color: '#3a4a58',
    accent: '#172d40',
    typography: {
        font_size: '14px',
        font_weight: '400',
        heading_weight: '700',
        heading_style: 'normal',
    },
    spacing: {
        page_padding: '2.5rem',
        section_gap: '1.75rem',
        item_gap: '1.1rem',
    },
    borders: {
        style: 'solid',
        width: '2px',
        radius: '0px',
    },
    icons: {
        enabled: false,
    },
};

function hasCatalogDefinition(definition = {}) {
    return Boolean(definition.key ?? definition.slug ?? definition.id);
}

function normalizeLayout(layout = {}) {
    const rawSettings = layout.settings ?? {};
    const layoutName = layout.layout ?? 'classic';
    const settings = normalizeSettings(rawSettings);

    if (settings.sidebar_position === 'none' && layoutName === 'sidebar') {
        settings.sidebar_position = 'left';
    }

    return {
        layout: layoutName,
        settings,
        section_order: layout.section_order ?? [],
        enabled_sections: layout.enabled_sections ?? [],
    };
}

export function resolveTemplateDefinition(templateKey, templateCatalog = []) {
    return templateCatalog.find((item) => item.key === templateKey || item.slug === templateKey) ?? {};
}

export function resolveLayoutConfig(document, templateKey, templateCatalog = []) {
    const definition = resolveTemplateDefinition(templateKey, templateCatalog);
    const fromCatalog = hasCatalogDefinition(definition);

    const layoutJson = fromCatalog
        ? (definition.layout_json ?? {
            layout: definition.layout ?? templateKey,
            settings: definition.settings ?? {},
            section_order: definition.section_order ?? [],
            enabled_sections: definition.enabled_sections ?? [],
        })
        : {
            layout: document?.template?.key ?? templateKey,
            settings: document?.template?.settings ?? {},
            section_order: document?.meta?.section_order ?? [],
            enabled_sections: document?.meta?.enabled_sections ?? [],
        };

    const config = normalizeLayout(layoutJson);
    const sidebarPosition = config.settings.sidebar_position ?? 'none';

    return {
        layout: config.layout,
        settings: config.settings,
        section_order: config.section_order,
        enabled_sections: config.enabled_sections,
        sidebar_position: sidebarPosition,
        uses_sidebar: sidebarPosition !== 'none',
    };
}

export function resolveTemplateSettings(document, templateKey, templateCatalog = []) {
    return resolveLayoutConfig(document, templateKey, templateCatalog).settings;
}

export function resolveTemplateLayout(templateKey, templateCatalog = []) {
    return resolveLayoutConfig({}, templateKey, templateCatalog).layout;
}

export function splitSections(sections, config) {
    if (! config.uses_sidebar) {
        return {
            mode: 'single',
            aside: [],
            main: sections,
            sidebar_position: 'none',
        };
    }

    const sidebarKeys = SIDEBAR_SECTION_KEYS.filter((key) => sections.some((section) => section.key === key));
    const aside = sections.filter((section) => sidebarKeys.includes(section.key));
    const main = sections.filter((section) => ! sidebarKeys.includes(section.key));

    return {
        mode: 'sidebar',
        aside,
        main,
        sidebar_position: config.sidebar_position ?? 'left',
    };
}

export function assembleLayoutBody(sections, sectionHtml, config) {
    const split = splitSections(sections, config);
    const headerSection = sections.find((section) => ['header', 'personal_details'].includes(section.key));

    if (split.mode === 'single') {
        return `<div class="cv-content">${sections.map((section) => sectionHtml[section.key] ?? '').join('')}</div>`;
    }

    const asideHtml = split.aside.map((section) => sectionHtml[section.key] ?? '').join('');
    const mainHtml = split.main.map((section) => sectionHtml[section.key] ?? '').join('');
    const position = split.sidebar_position ?? 'left';
    const sidebarPhoto = headerSection
        ? (sectionHtml.__sidebar_photo ?? '')
        : '';

    if (position === 'right') {
        return `<div class="cv-grid-sidebar cv-sidebar--right">
            <main class="cv-main">${mainHtml}</main>
            <aside class="cv-aside">${sidebarPhoto}${asideHtml}</aside>
        </div>`;
    }

    return `<div class="cv-grid-sidebar cv-sidebar--left">
        <aside class="cv-aside">${sidebarPhoto}${asideHtml}</aside>
        <main class="cv-main">${mainHtml}</main>
    </div>`;
}

export function fontStylesheetUrl(font = 'figtree') {
    const family = FONT_STYLESHEETS[font] ?? FONT_STYLESHEETS.figtree;

    return `https://fonts.bunny.net/css?family=${encodeURIComponent(family)}&display=swap`;
}

export function fontFamily(font = 'figtree') {
    return FONT_FAMILIES[font] ?? FONT_FAMILIES.figtree;
}

export function photoPositionClass(photoPosition = 'top_left') {
    const normalized = normalizePhotoPosition(photoPosition);

    switch (normalized) {
    case 'top_right':
        return 'right';
    case 'center':
        return 'center';
    case 'sidebar':
        return 'sidebar';
    case 'hidden':
        return 'hidden';
    default:
        return 'left';
    }
}

export function normalizePhotoPosition(value) {
    const aliases = { top: 'top_left', left: 'top_left', right: 'top_right' };
    const normalized = aliases[value] ?? value;

    return ['top_left', 'top_right', 'center', 'sidebar', 'hidden'].includes(normalized)
        ? normalized
        : 'top_left';
}

export function normalizeSidebarPosition(value) {
    const normalized = String(value ?? 'none').trim().toLowerCase();

    return ['left', 'right', 'none'].includes(normalized) ? normalized : 'none';
}

function hexToRgb(hex) {
    let value = String(hex ?? '').trim().replace('#', '');

    if (value.length === 3) {
        value = value.split('').map((char) => char + char).join('');
    }

    if (! /^[0-9a-fA-F]{6}$/.test(value)) {
        return null;
    }

    return [
        parseInt(value.slice(0, 2), 16),
        parseInt(value.slice(2, 4), 16),
        parseInt(value.slice(4, 6), 16),
    ];
}

export function normalizeColor(value, fallback) {
    const trimmed = String(value ?? '').trim();

    if (! trimmed) {
        return fallback;
    }

    let hex = trimmed.startsWith('#') ? trimmed : `#${trimmed}`;
    const raw = hex.slice(1);

    if (raw.length === 3) {
        hex = `#${raw.split('').map((char) => char + char).join('')}`;
    }

    return /^#[0-9a-fA-F]{6}$/.test(hex) ? hex.toLowerCase() : fallback;
}

export function readableTextColor(hex) {
    const rgb = hexToRgb(hex);

    if (! rgb) {
        return '#000000';
    }

    const luminance = (0.299 * rgb[0] + 0.587 * rgb[1] + 0.114 * rgb[2]) / 255;

    return luminance > 0.55 ? '#000000' : '#ffffff';
}

export function mixWithWhite(hex, ratio) {
    const rgb = hexToRgb(hex);

    if (! rgb) {
        return '#f8fafc';
    }

    const amount = Math.max(0, Math.min(1, ratio));
    const mix = (channel) => Math.round(channel + ((255 - channel) * amount));

    return `#${mix(rgb[0]).toString(16).padStart(2, '0')}${mix(rgb[1]).toString(16).padStart(2, '0')}${mix(rgb[2]).toString(16).padStart(2, '0')}`;
}

export function normalizeSettings(settings = {}) {
    const merged = mergeSettings(DEFAULT_SETTINGS, settings);
    const primary = merged.primary_color ?? merged.accent ?? DEFAULT_SETTINGS.primary_color;

    merged.primary_color = primary;
    merged.accent = primary;
    merged.photo_position = normalizePhotoPosition(merged.photo_position);
    merged.sidebar_position = normalizeSidebarPosition(merged.sidebar_position);
    merged.background_color = normalizeColor(merged.background_color, DEFAULT_SETTINGS.background_color);
    merged.sidebar_background_color = normalizeColor(
        merged.sidebar_background_color,
        mixWithWhite(primary, 0.08),
    );
    merged.text_color = normalizeColor(merged.text_color, DEFAULT_SETTINGS.text_color);
    merged.secondary_color = normalizeColor(merged.secondary_color, DEFAULT_SETTINGS.secondary_color);

    return merged;
}

export function mergeSettings(base = {}, override = {}) {
    const merged = {
        ...base,
        ...override,
        typography: { ...(base.typography ?? {}), ...(override.typography ?? {}) },
        spacing: { ...(base.spacing ?? {}), ...(override.spacing ?? {}) },
        borders: { ...(base.borders ?? {}), ...(override.borders ?? {}) },
        icons: { ...(base.icons ?? {}), ...(override.icons ?? {}) },
    };
    merged.accent = merged.primary_color ?? merged.accent ?? base.accent;
    merged.primary_color = merged.accent;

    return merged;
}

export function cssVariables(settings = {}) {
    const normalized = normalizeSettings(settings);
    const typography = normalized.typography ?? {};
    const spacing = normalized.spacing ?? {};
    const borders = normalized.borders ?? {};
    const primary = normalized.primary_color ?? normalized.accent ?? '#172d40';
    const secondary = normalized.secondary_color ?? '#c1623d';
    const background = normalized.background_color ?? '#ffffff';
    const sidebarBackground = normalized.sidebar_background_color ?? mixWithWhite(primary, 0.08);
    const text = normalized.text_color ?? '#3a4a58';

    return {
        '--cv-accent': primary,
        '--cv-secondary': secondary,
        '--cv-bg': background,
        '--cv-sidebar-bg': sidebarBackground,
        '--cv-text': text,
        '--cv-muted': mixWithWhite(text, 0.35),
        '--cv-border': mixWithWhite(primary, 0.75),
        '--cv-accent-border': mixWithWhite(primary, 0.25),
        '--cv-tag-bg': primary,
        '--cv-tag-color': readableTextColor(primary),
        '--cv-photo-border': mixWithWhite(primary, 0.2),
        '--cv-font-size': typography.font_size ?? '14px',
        '--cv-font-weight': typography.font_weight ?? '400',
        '--cv-heading-weight': typography.heading_weight ?? '700',
        '--cv-heading-style': typography.heading_style ?? 'normal',
        '--cv-page-padding': spacing.page_padding ?? '2.5rem',
        '--cv-section-gap': spacing.section_gap ?? '1.75rem',
        '--cv-item-gap': spacing.item_gap ?? '1.1rem',
        '--cv-border-style': borders.style ?? 'solid',
        '--cv-border-width': borders.width ?? '2px',
        '--cv-border-radius': borders.radius ?? '0px',
    };
}

export function inlineStyle(settings = {}) {
    return Object.entries(cssVariables(settings))
        .map(([key, value]) => `${key}: ${value}`)
        .join('; ');
}

export function applyTemplateMeta(document, templateKey, templateCatalog = []) {
    const definition = resolveTemplateDefinition(templateKey, templateCatalog);

    if (! hasCatalogDefinition(definition)) {
        return document;
    }

    const config = resolveLayoutConfig(document, templateKey, templateCatalog);

    if (! document.template) {
        document.template = {};
    }

    if (! document.meta) {
        document.meta = {};
    }

    document.template.key = templateKey;
    document.template.slug = templateKey;
    document.template.id = definition.id ?? document.template.id ?? null;
    document.template.settings = config.settings;

    return document;
}
