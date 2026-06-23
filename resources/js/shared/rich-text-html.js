import { prepareEditorHtml, sanitizeRichText } from './rich-text-sanitize';

/**
 * Render sanitized rich text HTML for CV live preview (client-side).
 */
export function renderRichTextHtml(content, className = 'cv-rich-text') {
    const html = prepareEditorHtml(content);

    if (! html) {
        return '';
    }

    const safe = sanitizeRichText(html);

    return `<div class="${className}">${safe}</div>`;
}
