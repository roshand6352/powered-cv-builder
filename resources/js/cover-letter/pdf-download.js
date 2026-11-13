import axios from 'axios';
import { canSharePdfFile, saveCvPdfFile } from '../cv/pdf-download';

export const PDF_FRIENDLY_ERROR = "We couldn't generate your PDF right now. Your cover letter is safe - please try again.";

/**
 * Read a friendly message out of an axios error whose response body is a Blob
 * (responseType: 'blob'). Never surfaces raw JSON or exception details.
 */
export async function friendlyPdfError(error) {
    const status = error?.response?.status;

    if (status === 403 || status === 404) {
        return "This cover letter isn't available to download from your account.";
    }

    if (status === 419) {
        return 'Your session has expired. Please refresh the page and try again.';
    }

    const data = error?.response?.data;
    let message = null;

    try {
        if (data instanceof Blob) {
            const parsed = JSON.parse(await data.text());
            message = parsed?.message ?? null;
        } else if (data && typeof data === 'object') {
            message = data.message ?? null;
        }
    } catch {
        message = null;
    }

    // Only trust the server's friendly copy; anything generic/technical gets ours.
    if (typeof message === 'string' && message.length > 0 && message.length < 240 && ! /exception|stack|\{|unable to generate pdf\.?$/i.test(message)) {
        return message;
    }

    return PDF_FRIENDLY_ERROR;
}

function filenameFromHeaders(headers, fallback) {
    const disposition = headers?.['content-disposition'] ?? '';
    const match = /filename\*?=(?:UTF-8'')?"?([^";]+)"?/i.exec(disposition);

    return match ? decodeURIComponent(match[1]) : fallback;
}

/**
 * POST to the existing cover letter PDF endpoint and save the returned file.
 * Throws an Error with a user-friendly message on failure.
 *
 * Touch devices that can share files resolve with `needsUserAction: true` so the
 * caller shows the share/download sheet (share() needs a fresh tap after the export).
 *
 * @returns {Promise<{ file: File, needsUserAction: boolean }>}
 */
export async function downloadCoverLetterPdf(url, fallbackName = 'cover-letter.pdf') {
    let response;

    try {
        response = await axios.post(url, {}, {
            responseType: 'blob',
            headers: { Accept: 'application/pdf, application/json' },
        });
    } catch (error) {
        throw new Error(await friendlyPdfError(error));
    }

    const blob = response.data instanceof Blob
        ? response.data
        : new Blob([response.data], { type: 'application/pdf' });

    const head = await blob.slice(0, 5).text();

    if (head !== '%PDF-') {
        throw new Error(PDF_FRIENDLY_ERROR);
    }

    const file = new File([blob], filenameFromHeaders(response.headers, fallbackName), { type: 'application/pdf' });

    if (canSharePdfFile(file)) {
        return { file, needsUserAction: true };
    }

    saveCvPdfFile(file);

    return { file, needsUserAction: false };
}