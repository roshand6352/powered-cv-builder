// Mirrors App\Services\Cv\CvPreviewTransformer + App\Support\Cv\CvSectionRenderPipeline

import { deepPlainClone } from './builder-payload';
import {
    isPreviewSectionEnabled,
    previewKeysForBuilderSection,
} from './builder-sections';

const SECTION_LABELS = {
    header: 'Header',
    about: 'About',
    experience: 'Experience',
    education: 'Education',
    skills: 'Skills',
    projects: 'Projects',
    languages: 'Languages',
    certificates: 'Certificates',
    certifications: 'Certificates',
    personal_details: 'Header',
    references: 'References',
    custom_sections: 'Custom Sections',
};

const RENDER_SECTION_KEYS = [
    'header',
    'about',
    'experience',
    'education',
    'skills',
    'projects',
    'languages',
    'certificates',
    'references',
    'custom_sections',
];

function listSection(key, definition, items, binding) {
    return {
        key,
        label: definition?.label ?? SECTION_LABELS[key] ?? key,
        type: 'list',
        repeatable: true,
        items: items ?? [],
        binding: binding ?? {},
    };
}

function transformBuilderSection(key, document, definition) {
    const binding = document?.meta?.template_bindings?.[key] ?? {};

    switch (key) {
    case 'personal_details':
        return {
            key,
            label: definition?.label ?? 'Personal Details',
            type: 'object',
            repeatable: false,
            data: document.personal_details ?? {},
            binding,
        };
    case 'experience':
        return listSection(key, definition, document.experience, binding);
    case 'education':
        return listSection(key, definition, document.education, binding);
    case 'skills':
        return listSection(key, definition, document.skills, binding);
    case 'languages':
        return listSection(key, definition, document.languages, binding);
    case 'projects':
        return listSection(key, definition, document.projects, binding);
    case 'certifications':
        return listSection(key, definition, deepPlainClone(document.certifications ?? []), binding);
    case 'references':
        return listSection(key, definition, document.references, binding);
    case 'custom_sections':
        return {
            key,
            label: definition?.label ?? 'Custom Sections',
            type: 'groups',
            repeatable: true,
            items: deepPlainClone(document.custom_sections ?? []),
            binding,
        };
    default:
        return {
            key,
            label: definition?.label ?? key,
            type: 'unknown',
            repeatable: false,
            items: [],
            binding,
        };
    }
}

function headerSection(personal) {
    const data = { ...(personal ?? {}) };
    delete data.summary;

    return {
        key: 'header',
        label: SECTION_LABELS.header,
        type: 'object',
        repeatable: false,
        data,
        binding: {},
    };
}

function aboutSection(personal) {
    const summary = personal?.summary ?? '';

    if (! summary) {
        return null;
    }

    return {
        key: 'about',
        label: SECTION_LABELS.about,
        type: 'object',
        repeatable: false,
        data: { summary },
        binding: {},
    };
}

function aliasListSection(key, label, section) {
    if (! section) {
        return null;
    }

    return {
        ...section,
        key,
        label,
    };
}

function resolveSectionPayload(key, indexed, personal) {
    switch (key) {
    case 'header':
    case 'personal_details':
        return headerSection(personal);
    case 'about':
        return aboutSection(personal);
    case 'certificates':
    case 'certifications':
        return aliasListSection('certificates', SECTION_LABELS.certificates, indexed.certifications);
    default:
        return indexed[key] ?? null;
    }
}

export function resolveSectionOrder(document, layoutConfig = {}) {
    const layoutOrder = layoutConfig.section_order ?? [];
    const documentOrder = (document?.meta?.section_order ?? [])
        .flatMap((key) => previewKeysForBuilderSection(key));

    const order = layoutOrder.length ? layoutOrder : documentOrder;
    const keys = order.filter((key) => isPreviewSectionEnabled(key, document, layoutConfig));

    if (keys.length) {
        return keys;
    }

    return order.length
        ? []
        : RENDER_SECTION_KEYS.filter((key) => isPreviewSectionEnabled(key, document, layoutConfig));
}

function buildIndexedSections(document, sectionsConfig) {
    const builderKeys = [
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

    return Object.fromEntries(
        builderKeys.map((key) => [
            key,
            transformBuilderSection(key, document, sectionsConfig[key] ?? {}),
        ]),
    );
}

export function orderedEnabledSections(document) {
    const order = document?.meta?.section_order ?? [];
    const enabled = document?.meta?.enabled_sections ?? [];

    return order.filter((key) => enabled.includes(key));
}

export function transformDocument(document, sectionsConfig = {}, layoutConfig = {}) {
    const indexed = buildIndexedSections(document, sectionsConfig);
    const personal = document.personal_details ?? {};
    const keys = resolveSectionOrder(document, layoutConfig);

    const sections = keys
        .map((key) => resolveSectionPayload(key, indexed, personal))
        .filter(Boolean);

    return {
        schema_version: document?.schema_version,
        template: document?.template ?? {},
        personal_details: personal,
        sections,
        meta: document?.meta ?? {},
    };
}
