import axios from 'axios';
import { prepareExportPayload } from './builder-payload';
import { capturePreviewHtmlForExport } from './preview-export';

const FALLBACK_PDF_FILENAME = 'My-CV.pdf';
const OBJECT_URL_REVOKE_DELAY_MS = 60000;

/**
 * Download a CV PDF via the builder export endpoint (WYSIWYG from live preview HTML).
 *
 * Desktop (and devices without file sharing) download straight away. Touch devices
 * that can share files resolve with `needsUserAction: true` so the caller can show the
 * share/download sheet — `navigator.share()` needs a fresh tap after the slow export.
 *
 * @param {object} options
 * @param {string} options.pdfDownloadUrl
 * @param {HTMLIFrameElement|null} options.previewFrame
 * @param {string} options.title
 * @param {object} options.cvDocument
 * @param {string} options.activeTemplate
 * @param {Array} [options.templateCatalog]
 * @param {string|null} [options.previewHtmlFallback]
 * @param {() => Promise<void>|void} [options.beforeExport]
 * @returns {Promise<{ file: File, needsUserAction: boolean }>}
 */
export async function downloadCvPdf({
    pdfDownloadUrl,
    previewFrame,
    title,
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

    const filename = buildCvPdfFilename(cvDocument?.personal_details?.full_name);
    const file = new File([buffer], filename, { type: 'application/pdf' });

    if (canSharePdfFile(file)) {
        return { file, needsUserAction: true };
    }

    saveCvPdfFile(file);

    return { file, needsUserAction: false };
}

/**
 * "Shane Louw" → "Shane-Louw-CV.pdf"; empty name → "My-CV.pdf".
 * Mirrors CvPdfExportService::filename() on the server.
 *
 * @param {unknown} fullName
 * @returns {string}
 */
export function buildCvPdfFilename(fullName) {
    const ascii = String(fullName ?? '')
        .normalize('NFKD')
        .replace(/[\u0300-\u036f]/g, '');

    const base = ascii
        .split(/[^A-Za-z0-9]+/)
        .filter((word) => word !== '' && word.toLowerCase() !== 'cv')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join('-')
        .slice(0, 80)
        .replace(/-+$/, '');

    return base !== '' ? `${base}-CV.pdf` : FALLBACK_PDF_FILENAME;
}

/**
 * Mobile / tablet only: desktop keeps the direct download.
 *
 * @param {File} file
 * @returns {boolean}
 */
export function canSharePdfFile(file) {
    if (typeof navigator === 'undefined' || typeof navigator.share !== 'function' || typeof navigator.canShare !== 'function') {
        return false;
    }

    if (! window.matchMedia?.('(pointer: coarse)').matches) {
        return false;
    }

    try {
        return navigator.canShare({ files: [file] });
    } catch {
        return false;
    }
}

/**
 * Share only the PDF file itself (no url/text) so no internal blob: link is attached.
 *
 * @param {File} file
 * @returns {Promise<void>}
 */
export async function shareCvPdfFile(file) {
    try {
        await navigator.share({ files: [file] });
    } catch (error) {
        if (error?.name === 'AbortError') {
            return;
        }

        saveCvPdfFile(file);
    }
}

/**
 * @param {File} file
 */
export function saveCvPdfFile(file) {
    const url = window.URL.createObjectURL(file);
    const link = window.document.createElement('a');
    link.href = url;
    link.download = file.name;
    link.rel = 'noopener';
    link.style.display = 'none';
    window.document.body.appendChild(link);
    link.click();
    link.remove();

    // Revoking synchronously can cancel the save on mobile browsers.
    window.setTimeout(() => window.URL.revokeObjectURL(url), OBJECT_URL_REVOKE_DELAY_MS);
}

/**
 * Alpine state + actions for the "PDF ready" share sheet (spread into a component).
 */
export function cvPdfReadySheet() {
    // Kept outside Alpine state: a reactive Proxy around a File breaks share() and createObjectURL().
    let readyFile = null;

    return {
        pdfReadyName: '',

        showPdfReady(file) {
            readyFile = file;
            this.pdfReadyName = file.name;
        },

        sharePdfReadyFile() {
            const file = readyFile;

            if (! file) {
                return;
            }

            shareCvPdfFile(file).finally(() => this.closePdfReady());
        },

        downloadPdfReadyFile() {
            const file = readyFile;

            if (! file) {
                return;
            }

            saveCvPdfFile(file);
            this.closePdfReady();
        },

        closePdfReady() {
            readyFile = null;
            this.pdfReadyName = '';
        },
    };
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
