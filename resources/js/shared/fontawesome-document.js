/**
 * Local Font Awesome stylesheet for CV/cover letter document iframes and exports.
 */
export function resolveFontAwesomeStylesheetUrl() {
    const meta = document.querySelector('meta[name="document-fontawesome-url"]');

    return meta?.content || '/vendor/fontawesome/css/all.min.css';
}

export function fontAwesomeStylesheetTag() {
    const href = resolveFontAwesomeStylesheetUrl();

    return `<link id="document-fontawesome" rel="stylesheet" href="${href}" crossorigin="anonymous" referrerpolicy="no-referrer" />`;
}
