// Template gallery thumbnail renderer — dummy demo data + admin template settings only.

import { THUMBNAIL_CSS } from './preview-styles';
import { fontAwesomeStylesheetTag } from '../shared/fontawesome-document';
import iconStyles from '../../css/cv-document-icons.css?raw';
import { renderSectionHtml, renderSidebarPhotoHtml } from './preview-sections';
import { transformDocument } from './preview-transformer';
import { buildTemplateDemoDocument } from './preview-fallback';
import {
    assembleLayoutBody,
    fontFamily,
    inlineStyle,
    resolveLayoutConfig,
} from './layout-resolver';

function wrapThumbnailDocument({ layout, settings, bodyHtml }) {
    const font = settings?.font ?? 'figtree';
    const pageStyle = inlineStyle(settings);
    const sidebar = settings?.sidebar_position ?? 'none';
    const photo = settings?.photo_position ?? 'top';
    const scale = 0.30;

    return `<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Template preview</title>
    ${fontAwesomeStylesheetTag()}
    <style>
        :root { --thumb-scale: ${scale}; }
        ${THUMBNAIL_CSS.replace('__FONT_FAMILY__', fontFamily(font))}${iconStyles}
    </style>
</head>
<body class="cv-body cv-body--thumbnail">
    <div class="cv-thumb-wrap">
        <div class="cv-page cv-layout-${layout}" data-layout="${layout}" data-mode="thumbnail" data-sidebar="${sidebar}" data-photo="${photo}" style="${pageStyle}">
            ${bodyHtml}
        </div>
    </div>
</body>
</html>`;
}

export class TemplateThumbnailRenderer {
    constructor() {
        this.cache = new Map();
        this.demoDocument = buildTemplateDemoDocument();
    }

    build(templateKey, templateCatalog = [], sectionsConfig = {}) {
        const cacheKey = templateKey;

        if (this.cache.has(cacheKey)) {
            return this.cache.get(cacheKey);
        }

        const layoutConfig = resolveLayoutConfig(this.demoDocument, templateKey, templateCatalog);
        const preview = transformDocument(this.demoDocument, sectionsConfig, layoutConfig);
        const settings = layoutConfig.settings;
        const layout = layoutConfig.layout;

        const sectionHtml = Object.fromEntries(
            preview.sections.map((section) => [
                section.key,
                renderSectionHtml(section, settings),
            ]),
        );

        const headerSection = preview.sections.find((section) => ['header', 'personal_details'].includes(section.key));

        if (headerSection) {
            sectionHtml.__sidebar_photo = renderSidebarPhotoHtml(headerSection, settings);
        }

        const bodyHtml = assembleLayoutBody(preview.sections, sectionHtml, layoutConfig);
        const html = wrapThumbnailDocument({ layout, settings, bodyHtml });

        this.cache.set(cacheKey, html);

        return html;
    }

    invalidateAll() {
        this.cache.clear();
    }
}
