import { prepareEditorHtml, richTextToPlainText, sanitizeRichText } from '../shared/rich-text-sanitize';

const PAGE_SECTION_ORDER = [
    '.cl-header',
    '.cl-date',
    '.cl-recipient',
    '.cl-subject',
    '.cl-greeting',
    '.cl-paragraph',
    '.cl-body',
    '.cl-closing',
    '.cl-signature',
];

function insertIntoPage(page, element, anchorSelector) {
    const idx = PAGE_SECTION_ORDER.indexOf(anchorSelector);

    if (idx === -1) {
        page.appendChild(element);

        return;
    }

    for (let i = idx + 1; i < PAGE_SECTION_ORDER.length; i += 1) {
        const next = page.querySelector(PAGE_SECTION_ORDER[i]);

        if (next) {
            page.insertBefore(element, next);

            return;
        }
    }

    const watermark = page.querySelector('.pdf-watermark, .cl-watermark');

    if (watermark) {
        page.insertBefore(element, watermark);
    } else {
        page.appendChild(element);
    }
}

function patchTextSection(doc, page, selector, value, className) {
    const text = `${value ?? ''}`.trim();
    let element = doc.querySelector(selector);

    if (! text) {
        element?.remove();

        return;
    }

    if (! element) {
        element = doc.createElement('div');
        element.className = className;
        insertIntoPage(page, element, selector);
    }

    element.textContent = text;
}

function patchRichSection(doc, page, wrapperSelector, className, value) {
    const html = prepareEditorHtml(value);
    const hasContent = richTextToPlainText(html) !== '';
    let wrapper = doc.querySelector(wrapperSelector);

    if (! hasContent) {
        wrapper?.remove();

        return;
    }

    if (! wrapper) {
        wrapper = doc.createElement('div');
        wrapper.className = className;
        insertIntoPage(page, wrapper, wrapperSelector);

        const rich = doc.createElement('div');
        rich.className = 'cl-rich-text';
        wrapper.appendChild(rich);
    }

    let rich = wrapper.querySelector('.cl-rich-text');

    if (! rich) {
        rich = doc.createElement('div');
        rich.className = 'cl-rich-text';
        wrapper.appendChild(rich);
    }

    rich.innerHTML = sanitizeRichText(html) || '<p><br></p>';
}

function patchRecipient(doc, page, fields) {
    const name = `${fields.hiring_manager_name ?? ''}`.trim();
    const company = `${fields.company_name ?? ''}`.trim();
    const addressHtml = prepareEditorHtml(fields.company_address ?? '');
    const hasAddress = richTextToPlainText(addressHtml) !== '';

    if (! name && ! company && ! hasAddress) {
        doc.querySelector('.cl-recipient')?.remove();

        return;
    }

    let block = doc.querySelector('.cl-recipient');

    if (! block) {
        block = doc.createElement('div');
        block.className = 'cl-section cl-recipient';
        insertIntoPage(page, block, '.cl-recipient');
    }

    block.innerHTML = '';

    if (name) {
        const line = doc.createElement('div');
        const strong = doc.createElement('strong');
        strong.textContent = name;
        line.appendChild(strong);
        block.appendChild(line);
    }

    if (company) {
        const line = doc.createElement('div');
        line.textContent = company;
        block.appendChild(line);
    }

    if (hasAddress) {
        const line = doc.createElement('div');
        line.className = 'cl-address cl-rich-text';
        line.innerHTML = sanitizeRichText(addressHtml);
        block.appendChild(line);
    }
}

/**
 * Instant client-side preview update — no server round trip.
 *
 * @param {Document} doc
 * @param {Record<string, string>} fields
 */
export function applyCoverLetterPreviewPatch(doc, fields) {
    const page = doc?.querySelector('.cl-page');

    if (! page) {
        return false;
    }

    patchRecipient(doc, page, fields);
    patchTextSection(doc, page, '.cl-subject', fields.subject, 'cl-section cl-subject');
    patchTextSection(doc, page, '.cl-greeting', fields.greeting, 'cl-section cl-greeting');
    patchRichSection(doc, page, '.cl-paragraph', 'cl-section cl-paragraph', fields.introduction);
    patchRichSection(doc, page, '.cl-body', 'cl-section cl-body', fields.body);
    patchTextSection(doc, page, '.cl-closing', fields.closing_paragraph, 'cl-closing');
    patchTextSection(doc, page, '.cl-signature', fields.signature_name, 'cl-signature');

    return true;
}
