// LIVE PREVIEW FIX: serialize reactive builder state for autosave / PDF export

import { mergeSettings } from './layout-resolver';

function unwrap(value) {
    if (value === null || value === undefined) {
        return value;
    }

    const Alpine = window.Alpine;

    if (Alpine?.raw) {
        try {
            return Alpine.raw(value);
        } catch {
            // Fall through to the value as-is.
        }
    }

    return value;
}

export function deepPlainClone(value) {
    const raw = unwrap(value);

    if (raw === null || typeof raw !== 'object') {
        return raw;
    }

    if (Array.isArray(raw)) {
        return raw.map((item) => deepPlainClone(item));
    }

    const output = {};

    for (const key of Object.keys(raw)) {
        output[key] = deepPlainClone(raw[key]);
    }

    return output;
}

const BUILDER_SECTION_KEYS = [
    'personal_details',
    'experience',
    'education',
    'skills',
    'languages',
    'projects',
    'certifications',
    'references',
    'custom_sections',
];

function toBuilderSectionKey(key) {
    if (! key) {
        return null;
    }

    switch (key) {
    case 'personal_info':
    case 'profile':
    case 'header':
        return 'personal_details';
    case 'about':
    case 'summary':
        return 'about';
    case 'certificates':
        return 'certifications';
    default:
        return BUILDER_SECTION_KEYS.includes(key) ? key : null;
    }
}

function migrateSectionKeys(sections = [], { fillDefaults = true } = {}) {
    const normalized = [];

    sections.forEach((key) => {
        const builderKey = toBuilderSectionKey(key);

        if (builderKey && ! normalized.includes(builderKey)) {
            normalized.push(builderKey);
        }
    });

    if (! normalized.length) {
        return fillDefaults ? [...BUILDER_SECTION_KEYS] : [];
    }

    if (fillDefaults) {
        BUILDER_SECTION_KEYS.forEach((key) => {
            if (! normalized.includes(key)) {
                normalized.push(key);
            }
        });
    }

    return normalized;
}

function normalizeBuilderMeta(meta = {}) {
    const next = { ...meta };

    if (Array.isArray(meta.section_order)) {
        next.section_order = migrateSectionKeys(meta.section_order);
    }

    if (Array.isArray(meta.enabled_sections)) {
        next.enabled_sections = migrateSectionKeys(meta.enabled_sections, { fillDefaults: false });

        if (Array.isArray(meta.enabled_sections)) {
            meta.enabled_sections.forEach((key) => {
                if (key === 'about' && ! next.enabled_sections.includes('about')) {
                    next.enabled_sections.push('about');
                }
            });
        }
    }

    if (meta.last_edited_section) {
        const lastEdited = toBuilderSectionKey(meta.last_edited_section);
        next.last_edited_section = lastEdited ?? meta.last_edited_section;
    }

    return next;
}

const REPEATER_KEYS = [
    'experience',
    'education',
    'skills',
    'languages',
    'projects',
    'certifications',
    'references',
    'custom_sections',
];

// LIVE PREVIEW FIX: force-read repeater arrays from Alpine reactive state
export function captureBuilderDocument(document) {
    const plain = deepPlainClone(document);

    for (const key of REPEATER_KEYS) {
        if (Array.isArray(document?.[key])) {
            plain[key] = document[key].map((item) => deepPlainClone(item));
        }
    }

    if (document?.personal_details) {
        plain.personal_details = deepPlainClone(document.personal_details);
    }

    if (document?.meta) {
        plain.meta = normalizeBuilderMeta(deepPlainClone(document.meta));
    }

    if (document?.template) {
        plain.template = deepPlainClone(document.template);
    }

    return plain;
}

export function serializeBuilderDocument(document, activeTemplate, templateCatalog = []) {
    const jsonData = captureBuilderDocument(document);
    const selected = templateCatalog.find((item) => item.key === activeTemplate);
    const layoutJson = selected?.layout_json ?? {};
    const templateSettings = mergeSettings(
        layoutJson.settings ?? selected?.settings ?? {},
        jsonData.template?.settings ?? {},
    );

    jsonData.template = {
        ...(jsonData.template ?? {}),
        id: selected?.id ?? jsonData.template?.id ?? null,
        key: activeTemplate,
        slug: activeTemplate,
        settings: deepPlainClone(templateSettings),
    };

    if (jsonData.personal_details) {
        delete jsonData.personal_details._photoObjectUrl;
    }

    return jsonData;
}

export function buildSyncedJsonPayload({ title, document, activeTemplate, templateCatalog }) {
    return {
        title,
        json_data: serializeBuilderDocument(document, activeTemplate, templateCatalog),
    };
}

export function buildPdfSyncPayload({ title, document, activeTemplate, templateCatalog }) {
    const synced = buildSyncedJsonPayload({ title, document, activeTemplate, templateCatalog });

    return {
        title: synced.title,
        json_data: synced.json_data,
        template: activeTemplate,
    };
}

export async function blobUrlToDataUrl(url) {
    if (! url || ! url.startsWith('blob:')) {
        return url;
    }

    const response = await fetch(url);
    const blob = await response.blob();

    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => resolve(reader.result);
        reader.onerror = reject;
        reader.readAsDataURL(blob);
    });
}

export async function finalizePdfExportPayload(pdfSyncPayload) {
    const payload = {
        title: pdfSyncPayload.title,
        json_data: deepPlainClone(pdfSyncPayload.json_data),
        template: pdfSyncPayload.template,
    };

    if (payload.json_data.personal_details?.photo_preview?.startsWith('blob:')) {
        payload.json_data.personal_details.photo_preview = await blobUrlToDataUrl(
            payload.json_data.personal_details.photo_preview,
        );
    }

    const photoPreview = payload.json_data.personal_details?.photo_preview;

    if (typeof photoPreview === 'string' && photoPreview.startsWith('data:') && photoPreview.length > 450000) {
        delete payload.json_data.personal_details.photo_preview;
    }

    return payload;
}

export function prepareAutosavePayload({ title, document, activeTemplate, templateCatalog }) {
    const jsonData = serializeBuilderDocument(document, activeTemplate, templateCatalog);

    if (jsonData.personal_details?.photo_preview?.startsWith('blob:')) {
        delete jsonData.personal_details.photo_preview;
    }

    const photoPreview = jsonData.personal_details?.photo_preview;

    if (typeof photoPreview === 'string' && photoPreview.startsWith('data:') && photoPreview.length > 450000) {
        delete jsonData.personal_details.photo_preview;
    }

    return {
        title,
        json_data: jsonData,
    };
}

export async function prepareExportPayload({
    title,
    document,
    activeTemplate,
    templateCatalog,
    previewHtml = null,
}) {
    const pdfSyncPayload = buildPdfSyncPayload({ title, document, activeTemplate, templateCatalog });
    const payload = await finalizePdfExportPayload(pdfSyncPayload);

    if (typeof previewHtml === 'string' && previewHtml.trim() !== '') {
        payload.preview_html = previewHtml;
    }

    return payload;
}
