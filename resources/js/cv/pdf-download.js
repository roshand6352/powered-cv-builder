import axios from 'axios';
import { prepareExportPayload } from './builder-payload';
import { capturePreviewHtmlForExport } from './preview-export';

/**
 * Download a CV PDF via the builder export endpoint (WYSIWYG from live preview HTML).
 *
 * @param {object} options
 * @param {string} options.pdfDownloadUrl
 * @param {HTMLIFrameElement|null} options.previewFrame
 * @param {string} options.title
 * @param {string} [options.slug]
 * @param {object} options.cvDocument
 * @param {string} options.activeTemplate
 * @param {Array} [options.templateCatalog]
 * @param {string|null} [options.previewHtmlFallback]
 * @param {() => Promise<void>|void} [options.beforeExport]
 */
export async function downloadCvPdf({
    pdfDownloadUrl,
    previewFrame,
    title,
    slug = '',
    cvDocument,
    activeTemplate,
    templateCatalog = [],
    previewHtmlFallback = null,
    beforeExport = null,
}) {
    if (! pdfDownloadUrl) {
        throw new Error('PDF download is not available.');
    }

    if (beforeExport) {
        await beforeExport();
    }

    await new Promise((resolve) => {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
    });

    let previewHtml = await capturePreviewHtmlForExport(previewFrame);

    if (! previewHtml && previewHtmlFallback) {
        previewHtml = previewHtmlFallback;
    }

    if (! previewHtml) {
        throw new Error('Live preview is not ready. Wait for the preview to load, then try again.');
    }

    const payload = await prepareExportPayload({
        title,
        document: cvDocument,
        activeTemplate,
        templateCatalog,
        previewHtml,
    });

    const response = await axios.post(pdfDownloadUrl, payload, {
        responseType: 'blob',
        timeout: 180000,
        headers: {
            Accept: 'application/pdf',
        },
    });

    if (response.data.type && response.data.type.includes('json')) {
        const text = await response.data.text();
        const parsed = JSON.parse(text);
        throw new Error(parsed.message ?? 'PDF export failed.');
    }

    const buffer = await response.data.arrayBuffer();
    const header = new TextDecoder('latin1').decode(new Uint8Array(buffer.slice(0, 5)));

    if (! header.startsWith('%PDF-')) {
        throw new Error('Downloaded file is not a valid PDF. Please try again.');
    }

    const blob = new Blob([buffer], { type: 'application/pdf' });
    const url = window.URL.createObjectURL(blob);
    const link = window.document.createElement('a');
    link.href = url;
    link.download = `${slug || title || 'cv'}.pdf`;
    window.document.body.appendChild(link);
    link.click();
    link.remove();
    window.URL.revokeObjectURL(url);
}

/**
 * @param {unknown} error
 * @returns {string}
 */
export function resolveCvPdfDownloadError(error) {
    if (error?.response?.data instanceof Blob) {
        return error.response.data.text().then((text) => {
            try {
                const payload = JSON.parse(text);

                return payload.message ?? 'PDF export failed. Please try again.';
            } catch {
                return 'PDF export failed. Please try again.';
            }
        });
    }

    return Promise.resolve(
        error?.response?.data?.message
            ?? error?.response?.data?.error
            ?? error?.message
            ?? 'PDF export failed. Please try again.',
    );
}
