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

/**
 * Like richTextToPlainText, but keeps paragraph / line breaks and renders list
 * items as "- item" (or "1. item") lines so the structure survives a round trip
 * through the AI.
 */
export function richTextToStructuredText(html) {
    const prepared = prepareEditorHtml(html);

    if (! prepared) {
        return '';
    }

    const doc = new DOMParser().parseFromString(prepared, 'text/html');
    const lines = [];
    const clean = (node) => (node.textContent ?? '').replace(/\s+/g, ' ').trim();

    const walk = (parent) => {
        parent.childNodes.forEach((node) => {
            if (node.nodeType === 3) {
                const text = clean(node);

                if (text) {
                    lines.push(text);
                }

                return;
            }

            if (node.nodeType !== 1) {
                return;
            }

            const tag = node.tagName.toLowerCase();

            if (tag === 'ul' || tag === 'ol') {
                let count = 0;

                node.querySelectorAll(':scope > li').forEach((li) => {
                    count += 1;
                    lines.push(`${tag === 'ol' ? `${count}.` : '-'} ${clean(li)}`);
                });

                return;
            }

            if (tag === 'br') {
                return;
            }

            if (node.querySelector('ul, ol')) {
                walk(node);

                return;
            }

            const text = clean(node);

            if (text) {
                lines.push(text);
            }
        });
    };

    walk(doc.body);

    return lines.join('\n');
}