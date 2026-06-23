// Surgical preview DOM updates — avoid full iframe reloads while typing.

import { escapeHtml } from './preview-sections';
import { contactIconHtml } from './section-icons';
import { normalizePhotoPosition, photoPositionClass } from './layout-resolver';
import { renderRichTextHtml } from '../shared/rich-text-html';

function buildContactInnerHtml(data, settings) {
    const contactFields = [
        ['email', data.email],
        ['phone', data.phone],
        ['location', data.location],
        ['website', data.website],
        ['linkedin', data.linkedin],
        ['github', data.github],
    ];

    return contactFields
        .filter(([, value]) => value)
        .map(([field, value]) => `<span>${contactIconHtml(field, settings)}${escapeHtml(value)}</span>`)
        .join('');
}

function patchHeaderFromPersonalDetails(sectionEl, personalDetails, settings = {}) {
    const data = personalDetails ?? {};
    const photoPosition = normalizePhotoPosition(settings?.photo_position);
    const photoUrl = data.photo_preview ?? data.photo_url ?? '';
    const showPhoto = ! ['hidden', 'sidebar'].includes(photoPosition) && photoUrl;

    const header = sectionEl.querySelector('.cv-header') ?? sectionEl;
    const nameEl = header.querySelector('.cv-header-name');
    const nextName = data.full_name || 'Your Name';

    if (nameEl && nameEl.textContent !== nextName) {
        nameEl.textContent = nextName;
    }

    let roleEl = header.querySelector('.cv-header-role');

    if (data.job_title) {
        if (! roleEl) {
            roleEl = sectionEl.ownerDocument.createElement('p');
            roleEl.className = 'cv-header-role';
            nameEl?.insertAdjacentElement('afterend', roleEl);
        }

        if (roleEl.textContent !== data.job_title) {
            roleEl.textContent = data.job_title;
        }
    } else {
        roleEl?.remove();
    }

    const contactHtml = buildContactInnerHtml(data, settings);
    let contactEl = header.querySelector('.cv-header-contact');

    if (contactHtml) {
        if (! contactEl) {
            contactEl = sectionEl.ownerDocument.createElement('div');
            contactEl.className = 'cv-header-contact';
            header.querySelector('.cv-header-body')?.appendChild(contactEl);
        }

        if (contactEl.innerHTML !== contactHtml) {
            contactEl.innerHTML = contactHtml;
        }
    } else {
        contactEl?.remove();
    }

    let photoEl = header.querySelector('.cv-header-photo:not(.cv-header-photo--sidebar)');

    if (showPhoto) {
        const photoClass = photoPositionClass(settings?.photo_position);

        if (! photoEl) {
            photoEl = sectionEl.ownerDocument.createElement('img');
            photoEl.className = 'cv-header-photo';
            photoEl.alt = '';
            header.insertBefore(photoEl, header.firstChild);
        }

        header.className = `cv-header cv-header--photo-${photoClass}`;

        if (photoEl.getAttribute('src') !== photoUrl) {
            photoEl.setAttribute('src', photoUrl);
        }
    } else {
        photoEl?.remove();
    }

    return true;
}

function patchAboutFromSummary(sectionEl, summary) {
    const rich = sectionEl.querySelector('.cv-rich-text');

    if (! rich) {
        return false;
    }

    const wrapper = sectionEl.ownerDocument.createElement('div');
    wrapper.innerHTML = renderRichTextHtml(summary, 'cv-rich-text cv-header-summary').trim();
    const replacement = wrapper.firstElementChild;

    if (! replacement) {
        return false;
    }

    if (rich.innerHTML !== replacement.innerHTML) {
        rich.innerHTML = replacement.innerHTML;
    }

    return true;
}

function replaceSectionIfChanged(existing, html) {
    const trimmed = html.trim();

    if (! trimmed) {
        existing.remove();

        return true;
    }

    const wrapper = existing.ownerDocument.createElement('div');
    wrapper.innerHTML = trimmed;
    const replacement = wrapper.firstElementChild;

    if (! replacement) {
        return false;
    }

    if (existing.outerHTML === replacement.outerHTML) {
        return true;
    }

    existing.replaceWith(replacement);

    return true;
}

/**
 * Patch only the preview sections that changed. Returns ok:false when structure
 * must be rebuilt (missing nodes, new sections, etc.).
 */
export function applyPreviewPatches(doc, {
    sectionHtml = {},
    changedSections = [],
    personalDetails = null,
    settings = {},
} = {}) {
    if (! doc) {
        return { ok: false, reason: 'missing-document' };
    }

    const keys = changedSections.filter((key) => key !== '__sidebar_photo');

    for (const key of keys) {
        const html = sectionHtml[key] ?? '';
        const existing = doc.querySelector(`[data-section="${key}"]`);
        const trimmed = html.trim();

        if (! trimmed) {
            existing?.remove();
            continue;
        }

        if (! existing) {
            return { ok: false, reason: 'missing-section', key };
        }

        if (key === 'header' && personalDetails) {
            patchHeaderFromPersonalDetails(existing, personalDetails, settings);
            continue;
        }

        if (key === 'about' && personalDetails?.summary !== undefined) {
            if (patchAboutFromSummary(existing, personalDetails.summary)) {
                continue;
            }
        }

        if (! replaceSectionIfChanged(existing, trimmed)) {
            return { ok: false, reason: 'replace-failed', key };
        }
    }

    return { ok: true };
}
