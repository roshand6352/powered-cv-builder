// Capture the builder live-preview iframe document for PDF export (WYSIWYG).

import { blobUrlToDataUrl } from './builder-payload';

/**
 * Remove builder-only scale/transform while preserving preview page dimensions and layout.
 */
function resetPreviewPageLayout(doc) {
    const page = doc.querySelector('.cv-page');
    const body = doc.body;
    const html = doc.documentElement;
    const layoutNodes = [
        page,
        ...doc.querySelectorAll('.cv-grid-sidebar, .cv-content, .cv-aside, .cv-main'),
    ].filter(Boolean);

    layoutNodes.forEach((node) => {
        node.style.minHeight = 'auto';
        node.style.height = 'auto';
    });

    if (page) {
        page.style.transform = '';
        page.style.transformOrigin = '';
        page.style.margin = '0 auto';
        page.style.marginLeft = 'auto';
        page.style.marginRight = 'auto';
        page.style.marginBottom = '';
        page.style.boxShadow = '';
        page.style.minWidth = '794px';
        page.style.width = '794px';
        page.style.maxWidth = '794px';
    }

    if (body) {
        body.style.height = '';
        body.style.minHeight = '';
        body.style.overflow = '';
        body.style.overflowX = '';
        body.style.overflowY = '';
        body.style.background = '#ffffff';
    }

    if (html) {
        html.style.height = '';
        html.style.overflow = '';
    }
}

/**
 * Resolve root-relative asset URLs so Browsershot can load the same files as the preview iframe.
 */
function absolutizeAssetUrls(root) {
    const origin = window.location.origin;

    root.querySelectorAll('link[href^="/"]').forEach((element) => {
        element.setAttribute('href', `${origin}${element.getAttribute('href')}`);
    });

    root.querySelectorAll('img[src^="/"]').forEach((element) => {
        element.setAttribute('src', `${origin}${element.getAttribute('src')}`);
    });
}

async function inlineBlobImages(doc) {
    const images = [...doc.querySelectorAll('img[src^="blob:"]')];

    await Promise.all(images.map(async (img) => {
        try {
            img.src = await blobUrlToDataUrl(img.src);
        } catch {
            // Keep original src if inlining fails.
        }
    }));
}

/**
 * @param {HTMLIFrameElement|null} previewFrame
 * @returns {Promise<string|null>}
 */
export async function capturePreviewHtmlForExport(previewFrame) {
    const doc = previewFrame?.contentDocument;

    if (! doc?.documentElement || ! doc.body?.querySelector('.cv-page')) {
        return null;
    }

    await inlineBlobImages(doc);
    resetPreviewPageLayout(doc);

    await new Promise((resolve) => {
        requestAnimationFrame(() => requestAnimationFrame(resolve));
    });

    const root = doc.documentElement.cloneNode(true);
    const clonePage = root.querySelector('.cv-page');
    const cloneLayoutNodes = [
        clonePage,
        ...root.querySelectorAll('.cv-grid-sidebar, .cv-content, .cv-aside, .cv-main'),
    ].filter(Boolean);

    cloneLayoutNodes.forEach((node) => {
        node.style.minHeight = 'auto';
        node.style.height = 'auto';
    });

    if (clonePage) {
        clonePage.style.transform = '';
        clonePage.style.transformOrigin = '';
        clonePage.style.margin = '0 auto';
        clonePage.style.marginLeft = 'auto';
        clonePage.style.marginRight = 'auto';
        clonePage.style.marginBottom = '';
        clonePage.style.boxShadow = '';
        clonePage.style.minWidth = '794px';
        clonePage.style.width = '794px';
        clonePage.style.maxWidth = '794px';
    }

    const cloneBody = root.querySelector('body');
    const cloneHtml = root.querySelector('html');

    if (cloneHtml) {
        cloneHtml.style.height = '';
        cloneHtml.style.overflow = '';
    }

    if (cloneBody) {
        cloneBody.style.height = '';
        cloneBody.style.minHeight = '';
        cloneBody.style.overflow = '';
        cloneBody.style.background = '#ffffff';
    }

    absolutizeAssetUrls(root);

    const hasSidebarGrid = Boolean(root.querySelector('.cv-grid-sidebar'));
    const hasContent = Boolean(root.querySelector('.cv-page .cv-section, .cv-page .cv-content'));

    if (! hasContent) {
        return null;
    }

    if (! hasSidebarGrid && ! root.querySelector('.cv-content')) {
        return null;
    }

    return '<!DOCTYPE html>\n'.concat(root.outerHTML);
}
