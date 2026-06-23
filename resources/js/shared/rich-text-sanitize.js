import DOMPurify from 'dompurify';

const ALLOWED_TAGS = [
    'p', 'br', 'strong', 'b', 'em', 'i', 'u', 's',
    'h1', 'h2', 'h3', 'h4',
    'ul', 'ol', 'li',
    'a', 'span', 'div',
];

const ALLOWED_ATTR = ['href', 'target', 'rel', 'class'];

export function looksLikeHtml(value) {
    return /<[a-z][\s\S]*>/i.test(String(value ?? ''));
}

export function plainTextToHtml(text) {
    const trimmed = String(text ?? '').trim();

    if (! trimmed) {
        return '';
    }

    return trimmed
        .split(/\r\n|\r|\n/)
        .map((line) => line.trim())
        .filter(Boolean)
        .map((line) => {
            const escaped = line
                .replace(/&/g, '&amp;')
                .replace(/</g, '&lt;')
                .replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;')
                .replace(/'/g, '&#39;');

            return `<p>${escaped}</p>`;
        })
        .join('');
}

export function prepareEditorHtml(value) {
    const raw = String(value ?? '').trim();

    if (! raw) {
        return '';
    }

    return looksLikeHtml(raw) ? sanitizeRichText(raw) : plainTextToHtml(raw);
}

export function sanitizeRichText(html) {
    const cleaned = DOMPurify.sanitize(String(html ?? ''), {
        ALLOWED_TAGS,
        ALLOWED_ATTR,
        ALLOW_DATA_ATTR: false,
    });

    return cleaned === '<br>' ? '' : cleaned.trim();
}

export function richTextToPlainText(html) {
    const prepared = prepareEditorHtml(html);

    if (! prepared) {
        return '';
    }

    const doc = new DOMParser().parseFromString(prepared, 'text/html');

    return (doc.body.textContent ?? '').replace(/\s+/g, ' ').trim();
}
