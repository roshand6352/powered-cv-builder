// Client-side section HTML renderers (mirrors components/cv/render/*.blade.php)

import { normalizePhotoPosition, photoPositionClass } from './layout-resolver';
import { renderRichTextHtml } from '../shared/rich-text-html';
import { contactIconHtml, sectionIconHtml } from './section-icons';

export function escapeHtml(value) {
    return String(value ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

function trimJoin(parts, separator) {
    return parts.filter(Boolean).join(separator).trim();
}

function formatExperienceDates(item) {
    const start = item.start_date ?? '';
    const end = item.current ? 'Present' : (item.end_date ?? '');
    const hasRange = start && (end || item.current);

    return `${escapeHtml(start)}${hasRange ? ' – ' : ''}${escapeHtml(end)}`;
}

function formatEducationDates(item) {
    const start = item.start_date ?? '';
    const end = item.in_progress ? 'Present' : (item.end_date ?? '');
    const hasRange = start && (end || item.in_progress);

    return `${escapeHtml(start)}${hasRange ? ' – ' : ''}${escapeHtml(end)}`;
}

function formatProjectDates(item) {
    const start = item.start_date ?? '';
    const end = item.in_progress ? 'Present' : (item.end_date ?? '');
    const hasRange = start && (end || item.in_progress);

    return `${escapeHtml(start)}${hasRange ? ' – ' : ''}${escapeHtml(end)}`;
}

function sectionTitleHtml(section, settings = {}) {
    const iconHtml = sectionIconHtml(section.key, settings);

    return `<h2 class="cv-section-title">${iconHtml}${escapeHtml(section.label ?? '')}</h2>`;
}

export function renderSidebarPhotoHtml(section, settings = {}) {
    const photoPosition = normalizePhotoPosition(settings?.photo_position);
    const data = section?.data ?? {};
    const photoUrl = data.photo_preview ?? data.photo_url ?? '';

    if (photoPosition !== 'sidebar' || ! photoUrl) {
        return '';
    }

    return `<div class="cv-sidebar-photo"><img src="${escapeHtml(photoUrl)}" alt="" class="cv-header-photo cv-header-photo--sidebar"></div>`;
}

export function renderHeader(section, settings) {
    const data = section.data ?? {};
    const photoPosition = normalizePhotoPosition(settings?.photo_position);
    const photoClass = photoPositionClass(settings?.photo_position);
    const photoUrl = data.photo_preview ?? data.photo_url ?? '';
    const showPhoto = ! ['hidden', 'sidebar'].includes(photoPosition) && photoUrl;

    const contactFields = [
        ['email', data.email],
        ['phone', data.phone],
        ['location', data.location],
        ['website', data.website],
        ['linkedin', data.linkedin],
        ['github', data.github],
    ];

    const contact = contactFields
        .filter(([, value]) => value)
        .map(([field, value]) => `<span>${contactIconHtml(field, settings)}${escapeHtml(value)}</span>`)
        .join('');

    const photoHtml = showPhoto
        ? `<img src="${escapeHtml(photoUrl)}" alt="" class="cv-header-photo">`
        : '';

    return `<section class="cv-section cv-section-header" data-section="${escapeHtml(section.key)}">
    <header class="cv-header cv-header--photo-${escapeHtml(photoClass)}">
        ${photoHtml}
        <div class="cv-header-body">
            <h1 class="cv-header-name">${escapeHtml(data.full_name || 'Your Name')}</h1>
            ${data.job_title ? `<p class="cv-header-role">${escapeHtml(data.job_title)}</p>` : ''}
            ${contact ? `<div class="cv-header-contact">${contact}</div>` : ''}
        </div>
    </header>
</section>`;
}

export function renderAbout(section, settings = {}) {
    const summary = section.data?.summary ?? '';

    if (! summary) {
        return '';
    }

    return `<section class="cv-section cv-section-about" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${renderRichTextHtml(summary, 'cv-rich-text cv-header-summary')}
</section>`;
}

export function renderPersonalDetails(section, settings) {
    return renderHeader(section, settings);
}

function formatExperienceSubtitle(item) {
    const company = item.company ?? '';
    const location = item.location ?? '';

    if (company && location) {
        return `${company} · ${location}`;
    }

    return company || location;
}

export function renderExperience(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.company || item.position);

    const body = items.map((item) => `<article class="cv-item">
            <div class="cv-item-head">
                <div>
                    <div class="cv-item-title">${escapeHtml(item.position || 'Position')}</div>
                    <div class="cv-item-subtitle">${escapeHtml(formatExperienceSubtitle(item))}</div>
                </div>
                <div class="cv-item-meta">${formatExperienceDates(item)}</div>
            </div>
            ${item.description ? renderRichTextHtml(item.description, 'cv-rich-text cv-item-text') : ''}
        </article>`).join('');

    return `<section class="cv-section cv-section-experience" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${body || '<p class="cv-item-text">No experience added yet.</p>'}
</section>`;
}

export function renderEducation(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.institution || item.degree);

    const body = items.map((item) => {
        const degreeField = trimJoin([item.degree ?? '', item.field ?? ''], item.degree && item.field ? ', ' : '');

        return `<article class="cv-item">
            <div class="cv-item-head">
                <div>
                    <div class="cv-item-title">${escapeHtml(item.institution || item.degree || 'Institution')}</div>
                    <div class="cv-item-subtitle">${escapeHtml(degreeField)}</div>
                </div>
                <div class="cv-item-meta">${formatEducationDates(item)}</div>
            </div>
            ${item.description ? renderRichTextHtml(item.description, 'cv-rich-text cv-item-text') : ''}
        </article>`;
    }).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${body}
</section>`;
}

export function renderSkills(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.name);

    const body = items.map((item) => {
        const label = item.level ? `${item.name} (${item.level})` : item.name;

        return `<span class="cv-tag">${escapeHtml(label)}</span>`;
    }).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    <div class="cv-tags">${body}</div>
</section>`;
}

export function renderLanguages(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.name);

    const body = items.map((item) => `<div class="cv-list-row">
        <strong>${escapeHtml(item.name)}</strong>
        <span>${escapeHtml(item.proficiency ?? '')}</span>
    </div>`).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${body}
</section>`;
}

export function renderProjects(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.name);

    const body = items.map((item) => `<article class="cv-item">
        <div class="cv-item-head">
            <div class="cv-item-title">${escapeHtml(item.name)}</div>
            <div class="cv-item-meta">${formatProjectDates(item)}</div>
        </div>
        ${item.url ? `<div class="cv-item-subtitle">${escapeHtml(item.url)}</div>` : ''}
        ${item.description ? renderRichTextHtml(item.description, 'cv-rich-text cv-item-text') : ''}
    </article>`).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${body}
</section>`;
}

export function renderCertifications(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.name);

    const body = items.map((item) => {
        const issuer = item.issuer ?? '';
        const date = item.date ?? '';
        const subtitle = issuer && date ? `${issuer} · ${date}` : (issuer || date);

        return `<article class="cv-item">
            <div class="cv-item-title">${escapeHtml(item.name)}</div>
            <div class="cv-item-subtitle">${escapeHtml(subtitle)}</div>
        </article>`;
    }).join('');

    return `<section class="cv-section cv-section-certificates" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    ${body}
</section>`;
}

export function renderReferences(section, settings = {}) {
    const items = (section.items ?? []).filter((item) => item.name);

    const body = items.map((item) => `<article class="cv-item" style="margin:0;border:1px solid var(--cv-border);border-radius:.75rem;padding:.85rem;">
        <div class="cv-item-title">${escapeHtml(item.name)}</div>
        ${item.company ? `<div class="cv-item-subtitle">${escapeHtml(item.company)}</div>` : ''}
        ${item.relationship ? `<div class="cv-item-text">${escapeHtml(item.relationship)}</div>` : ''}
        ${item.email ? `<div class="cv-item-text">${escapeHtml(item.email)}</div>` : ''}
    </article>`).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">
    ${sectionTitleHtml(section, settings)}
    <div class="cv-tags" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;">
        ${body}
    </div>
</section>`;
}

export function renderCustomSections(section, settings = {}) {
    const groups = (section.items ?? []).filter((group) => group.title || (group.items ?? []).length);

    const body = groups.map((group) => {
        const items = (group.items ?? []).filter((item) => item.title || item.content);

        if (! items.length && ! group.title) {
            return '';
        }

        const itemsHtml = items.map((item) => `<article class="cv-item">
            ${item.title ? `<div class="cv-item-title">${escapeHtml(item.title)}</div>` : ''}
            ${item.content ? renderRichTextHtml(item.content, 'cv-rich-text cv-item-text') : ''}
        </article>`).join('');

        return `<div class="cv-group">
            ${group.title ? `<h2 class="cv-group-title">${escapeHtml(group.title)}</h2>` : ''}
            ${itemsHtml}
        </div>`;
    }).join('');

    return `<section class="cv-section" data-section="${escapeHtml(section.key)}">${body}</section>`;
}

const SECTION_RENDERERS = {
    header: renderHeader,
    personal_details: renderHeader,
    about: renderAbout,
    experience: renderExperience,
    education: renderEducation,
    skills: renderSkills,
    languages: renderLanguages,
    projects: renderProjects,
    certificates: renderCertifications,
    certifications: renderCertifications,
    references: renderReferences,
    custom_sections: renderCustomSections,
};

export function renderSectionHtml(section, settings = {}) {
    const renderer = SECTION_RENDERERS[section.key];

    if (! renderer) {
        return '';
    }

    if (section.key === 'header' || section.key === 'personal_details') {
        return renderer(section, settings);
    }

    return renderer(section, settings);
}
