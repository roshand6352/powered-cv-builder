import { normalizePhotoPosition, resolveLayoutConfig } from './layout-resolver';

export const BUILDER_FORM_SECTIONS = [
    'personal_details',
    'experience',
    'education',
    'skills',
    'projects',
    'languages',
    'certifications',
    'references',
    'custom_sections',
];

const TEMPLATE_TO_BUILDER = {
    header: 'personal_details',
    personal_details: 'personal_details',
    personal_info: 'personal_details',
    profile: 'personal_details',
    about: 'about',
    summary: 'about',
    experience: 'experience',
    education: 'education',
    skills: 'skills',
    projects: 'projects',
    languages: 'languages',
    certificates: 'certifications',
    certifications: 'certifications',
    references: 'references',
    custom_sections: 'custom_sections',
};

const PERSONAL_INFO_TEMPLATE_KEYS = ['header', 'personal_details', 'personal_info', 'profile'];
const ABOUT_TEMPLATE_KEYS = ['about', 'summary'];

export function templateKeyToBuilderSection(key) {
    if (! key) {
        return null;
    }

    if (TEMPLATE_TO_BUILDER[key]) {
        return TEMPLATE_TO_BUILDER[key];
    }

    return BUILDER_FORM_SECTIONS.includes(key) ? key : null;
}

export function builderSectionsFromTemplate(templateKeys = []) {
    const sections = [];

    templateKeys.forEach((key) => {
        const mapped = templateKeyToBuilderSection(key);

        if (mapped && ! sections.includes(mapped)) {
            sections.push(mapped);
        }
    });

    return sections;
}

export function builderSectionOrderFromTemplate(templateOrder = [], templateEnabled = []) {
    const order = [];

    templateOrder.forEach((key) => {
        const mapped = templateKeyToBuilderSection(key);

        if (mapped && ! order.includes(mapped)) {
            order.push(mapped);
        }
    });

    builderSectionsFromTemplate(templateEnabled).forEach((key) => {
        if (! order.includes(key)) {
            order.push(key);
        }
    });

    return order.length ? order : [...BUILDER_FORM_SECTIONS];
}

export function isAboutEnabledInTemplate(templateEnabled = []) {
    return templateEnabled.some((key) => ABOUT_TEMPLATE_KEYS.includes(key));
}

export function isPersonalInfoEnabledInTemplate(templateEnabled = []) {
    return templateEnabled.some((key) => PERSONAL_INFO_TEMPLATE_KEYS.includes(key));
}

export function isBuilderSectionInTemplate(builderKey, templateEnabled = []) {
    if (builderKey === 'personal_details') {
        return isPersonalInfoEnabledInTemplate(templateEnabled);
    }

    const templateKeys = templateEnabled.filter((key) => templateKeyToBuilderSection(key) === builderKey);

    return templateKeys.length > 0;
}

export function syncBuilderSectionsFromTemplate(document, templateKey, templateCatalog = [], options = {}) {
    const { force = false } = options;
    const config = resolveLayoutConfig(document, templateKey, templateCatalog);
    const templateEnabled = config.enabled_sections ?? [];
    const templateOrder = config.section_order ?? [];

    if (! templateEnabled.length) {
        return document;
    }

    document.meta = document.meta ?? {};

    const templateBuilderEnabled = builderSectionsFromTemplate(templateEnabled);
    const templateBuilderOrder = builderSectionOrderFromTemplate(templateOrder, templateEnabled);
    const savedEnabled = Array.isArray(document.meta.enabled_sections) ? document.meta.enabled_sections : [];

    document.meta.section_order = templateBuilderOrder;

    if (force || savedEnabled.length === 0) {
        document.meta.enabled_sections = templateBuilderEnabled;

        return document;
    }

    const allowed = new Set(templateBuilderEnabled);
    const preserved = savedEnabled.filter((key) => allowed.has(key));

    document.meta.enabled_sections = preserved.length
        ? preserved
        : templateBuilderEnabled;

    return document;
}

export function isPreviewSectionEnabled(renderKey, document, layoutConfig = {}) {
    const templateEnabled = layoutConfig.enabled_sections ?? [];
    const documentEnabled = document?.meta?.enabled_sections ?? [];

    if (templateEnabled.length && ! templateEnabled.includes(renderKey)) {
        return false;
    }

    switch (renderKey) {
    case 'header':
        return isPersonalInfoEnabledInTemplate(templateEnabled)
            && documentEnabled.includes('personal_details');
    case 'about':
        return isAboutEnabledInTemplate(templateEnabled) && documentEnabled.includes('about');
    default: {
        const builderKey = templateKeyToBuilderSection(renderKey);

        if (! builderKey) {
            return false;
        }

        return documentEnabled.includes(builderKey);
    }
    }
}

export function isProfilePhotoEnabledInTemplate(templateKey, templateCatalog = [], document = {}) {
    const settings = resolveLayoutConfig(document, templateKey, templateCatalog).settings ?? {};

    return normalizePhotoPosition(settings.photo_position) !== 'hidden';
}

export function profilePhotoPositionLabel(templateKey, templateCatalog = [], document = {}) {
    const settings = resolveLayoutConfig(document, templateKey, templateCatalog).settings ?? {};
    const position = normalizePhotoPosition(settings.photo_position);

    const labels = {
        top_left: 'Top left of the header',
        top_right: 'Top right of the header',
        center: 'Centered above your name',
        sidebar: 'Sidebar column',
        hidden: 'Hidden',
    };

    return labels[position] ?? 'Header';
}

export function templateEnabledSections(templateKey, templateCatalog = [], document = {}) {
    return resolveLayoutConfig(document, templateKey, templateCatalog).enabled_sections ?? [];
}

/** Builder form keys → preview/render keys used in the iframe and PDF pipeline. */
export function previewKeysForBuilderSection(builderKey) {
    if (! builderKey) {
        return [];
    }

    if (builderKey === 'personal_details') {
        return ['header', 'about'];
    }

    if (builderKey === 'about') {
        return ['about'];
    }

    if (builderKey === 'certifications') {
        return ['certificates'];
    }

    return [builderKey];
}

export function previewSectionMatchesFocus(previewSectionKey, builderFocusKey) {
    if (! builderFocusKey) {
        return true;
    }

    return previewKeysForBuilderSection(builderFocusKey).includes(previewSectionKey);
}
