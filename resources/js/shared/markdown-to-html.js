const escapeHtml = (value) => value
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#39;');

function inlineMarkdown(escaped) {
    return escaped
        .replace(/\[([^\]]+)\]\((https?:\/\/[^\s)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>')
        .replace(/\*\*(?=\S)([\s\S]*?\S)\*\*/g, '<strong>$1</strong>')
        .replace(/(^|[^\w])__(?=\S)([\s\S]*?\S)__(?=[^\w]|$)/g, '$1<strong>$2</strong>')
        .replace(/(^|[^*\w])\*(?=[^\s*])([^*]*?[^\s*])\*(?=[^*\w]|$)/g, '$1<em>$2</em>')
        .replace(/(^|[^\w])_(?=[^\s_])([^_]*?[^\s_])_(?=[^\w]|$)/g, '$1<em>$2</em>');
}

/**
 * Converts the Markdown an AI reply may contain (bold, italic, links, bullet
 * and numbered lists, headings, line breaks) into editor-ready HTML. The input
 * is escaped first; callers still run the result through the HTML sanitizer.
 */
export function markdownToHtml(markdown) {
    const source = String(markdown ?? '').replace(/\r\n|\r/g, '\n').trim();

    if (! source) {
        return '';
    }

    const out = [];
    let listType = null;

    const closeList = () => {
        if (listType) {
            out.push(`</${listType}>`);
            listType = null;
        }
    };

    const openList = (type) => {
        if (listType !== type) {
            closeList();
            out.push(`<${type}>`);
            listType = type;
        }
    };

    source.split('\n').forEach((rawLine) => {
        const line = rawLine.trim();

        if (! line) {
            closeList();

            return;
        }

        const bullet = line.match(/^[-*\u2022\u25CF]\s+(.+)$/);
        const numbered = line.match(/^\d+[.)]\s+(.+)$/);
        const heading = line.match(/^#{1,6}\s+(.+)$/);

        if (bullet) {
            openList('ul');
            out.push(`<li>${inlineMarkdown(escapeHtml(bullet[1].trim()))}</li>`);
        } else if (numbered) {
            openList('ol');
            out.push(`<li>${inlineMarkdown(escapeHtml(numbered[1].trim()))}</li>`);
        } else if (heading) {
            closeList();
            out.push(`<p><strong>${inlineMarkdown(escapeHtml(heading[1].trim()))}</strong></p>`);
        } else {
            closeList();
            out.push(`<p>${inlineMarkdown(escapeHtml(line))}</p>`);
        }
    });

    closeList();

    return out.join('');
}