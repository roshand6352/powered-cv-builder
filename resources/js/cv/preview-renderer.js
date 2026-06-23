// LIVE PREVIEW FIX: instant client-side preview — no AJAX, no DB reads

import { previewSectionMatchesFocus } from './builder-sections';
import { deepPlainClone } from './builder-payload';
import { fontAwesomeStylesheetTag } from '../shared/fontawesome-document';
import iconStyles from '../../css/cv-document-icons.css?raw';
import { PREVIEW_CSS } from './preview-styles';
import { renderSectionHtml, renderSidebarPhotoHtml } from './preview-sections';
import { transformDocument } from './preview-transformer';
import {
    assembleLayoutBody,
    fontFamily,
    fontStylesheetUrl,
    inlineStyle,
    resolveLayoutConfig,
} from './layout-resolver';

function hashValue(value) {
    return JSON.stringify(value);
}

function wrapPreviewDocument({ title, layout, settings, bodyHtml }) {
    const font = settings?.font ?? 'figtree';
    const pageStyle = inlineStyle(settings);
    const sidebar = settings?.sidebar_position ?? 'none';
    const photo = settings?.photo_position ?? 'top';

    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>${title}</title>
    ${fontAwesomeStylesheetTag()}
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="${fontStylesheetUrl(font)}" rel="stylesheet">
    <style>${PREVIEW_CSS.replace('__FONT_FAMILY__', fontFamily(font))}${iconStyles}</style>
</head>
<body class="cv-body cv-body--preview cv-body--embedded">
    <div class="cv-page cv-layout-${layout}" data-layout="${layout}" data-mode="preview" data-sidebar="${sidebar}" data-photo="${photo}" style="${pageStyle}">
        ${bodyHtml}
    </div>
</body>
</html>`;
}

export class ClientPreviewRenderer {
    constructor() {
        this.sectionCache = new Map();
        this.lastTemplateKey = null;
        this.lastLayoutSignature = null;
    }

    invalidateAll() {
        this.sectionCache.clear();
        this.lastTemplateKey = null;
        this.lastLayoutSignature = null;
    }

    invalidateSections(keys = []) {
        keys.forEach((key) => {
            [...this.sectionCache.keys()].forEach((cacheKey) => {
                if (cacheKey.endsWith(`:${key}`)) {
                    this.sectionCache.delete(cacheKey);
                }
            });
        });
    }

    renderSection(section, settings, templateKey) {
        const hash = hashValue({
            section: deepPlainClone(section),
            settings,
            templateKey,
        });
        const cacheKey = `${templateKey}:${section.key}`;
        const cached = this.sectionCache.get(cacheKey);

        if (cached?.hash === hash) {
            return { html: cached.html, changed: false, key: section.key };
        }

        const html = renderSectionHtml(section, settings);
        this.sectionCache.set(cacheKey, { hash, html });

        return { html, changed: true, key: section.key };
    }

    build(document, sectionsConfig, templateKey, templateCatalog = [], options = {}) {
        const focusSection = options.focusSection ?? null;
        const layoutConfig = resolveLayoutConfig(document, templateKey, templateCatalog);
        const preview = transformDocument(document, sectionsConfig, layoutConfig);
        const settings = layoutConfig.settings;
        const layout = layoutConfig.layout;
        const layoutSignature = hashValue(layoutConfig);
        const templateChanged = this.lastTemplateKey !== templateKey || this.lastLayoutSignature !== layoutSignature;

        if (templateChanged) {
            this.invalidateAll();
            this.lastTemplateKey = templateKey;
            this.lastLayoutSignature = layoutSignature;
        }

        const renderedSections = preview.sections.map((section) => {
            if (focusSection && ! previewSectionMatchesFocus(section.key, focusSection)) {
                const cacheKey = `${templateKey}:${section.key}`;
                const cached = this.sectionCache.get(cacheKey);

                if (cached) {
                    return { html: cached.html, changed: false, key: section.key };
                }
            }

            return this.renderSection(section, settings, templateKey);
        });

        const sectionHtml = Object.fromEntries(renderedSections.map((result) => [result.key, result.html]));
        const headerSection = preview.sections.find((section) => ['header', 'personal_details'].includes(section.key));

        if (headerSection) {
            sectionHtml.__sidebar_photo = renderSidebarPhotoHtml(headerSection, settings);
        }

        const bodyHtml = assembleLayoutBody(preview.sections, sectionHtml, layoutConfig);
        const title = preview.personal_details?.full_name || 'CV Preview';
        const html = wrapPreviewDocument({ title, layout, settings, bodyHtml });

        return {
            html,
            bodyHtml,
            sectionHtml,
            changedSections: renderedSections.filter((result) => result.changed).map((result) => result.key),
            layoutChanged: templateChanged,
            layout,
            settings,
            layoutConfig,
        };
    }
}

// Backward-compatible exports for any legacy imports
export { resolveLayoutConfig, resolveTemplateDefinition } from './layout-resolver';
