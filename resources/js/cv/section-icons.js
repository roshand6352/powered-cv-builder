export const SECTION_ICON_CLASSES = {
    header: 'fa-solid fa-id-card',
    about: 'fa-solid fa-user',
    experience: 'fa-solid fa-briefcase',
    education: 'fa-solid fa-graduation-cap',
    skills: 'fa-solid fa-star',
    projects: 'fa-solid fa-diagram-project',
    languages: 'fa-solid fa-language',
    certificates: 'fa-solid fa-award',
    certifications: 'fa-solid fa-award',
    references: 'fa-solid fa-user-group',
    custom_sections: 'fa-solid fa-folder-open',
    awards: 'fa-solid fa-trophy',
    interests: 'fa-solid fa-heart',
};

export const CONTACT_ICON_CLASSES = {
    email: 'fa-solid fa-envelope',
    phone: 'fa-solid fa-phone',
    location: 'fa-solid fa-location-dot',
    website: 'fa-solid fa-globe',
    linkedin: 'fa-brands fa-linkedin',
    github: 'fa-brands fa-github',
};

export function normalizeSectionKey(key) {
    switch (key) {
    case 'profile':
    case 'personal_details':
        return 'header';
    case 'summary':
        return 'about';
    case 'certifications':
        return 'certificates';
    default:
        return key;
    }
}

export function sectionIconClass(sectionKey) {
    return SECTION_ICON_CLASSES[normalizeSectionKey(sectionKey)] ?? 'fa-solid fa-circle';
}

export function iconsEnabled(settings = {}) {
    return settings?.icons?.enabled !== false;
}

export function sectionIconHtml(sectionKey, settings = {}) {
    if (! iconsEnabled(settings)) {
        return '';
    }

    const iconClass = sectionIconClass(sectionKey);

    return `<i class="cv-section-icon ${iconClass}" aria-hidden="true"></i>`;
}

export function contactIconHtml(field, settings = {}) {
    if (! iconsEnabled(settings)) {
        return '';
    }

    const iconClass = CONTACT_ICON_CLASSES[field] ?? 'fa-solid fa-circle';

    return `<i class="cv-contact-icon ${iconClass}" aria-hidden="true"></i> `;
}
