// Dummy content used ONLY for template gallery thumbnail previews — never for live preview.

const SAMPLE_AVATAR = `data:image/svg+xml,${encodeURIComponent(`<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 96 96" fill="none">
  <rect width="96" height="96" rx="48" fill="#e4e8ec"/>
  <circle cx="48" cy="38" r="18" fill="#94a3b8"/>
  <path d="M16 88c4-18 20-28 32-28s28 10 32 28" fill="#94a3b8"/>
</svg>`)}`;

export const TEMPLATE_DEMO_CONTENT = {
    personal_details: {
        full_name: 'John Smith',
        job_title: 'Software Developer',
        email: 'john@example.com',
        phone: '+27 123 456 789',
        location: 'Johannesburg',
        summary: 'Experienced software developer with expertise in Laravel, PHP, MySQL, APIs, and frontend technologies.',
        photo_preview: SAMPLE_AVATAR,
    },
    experience: [
        {
            id: 'demo-exp-1',
            company: 'ABC Company',
            position: 'Senior Developer',
            location: 'Johannesburg',
            start_date: '2022',
            end_date: '',
            current: true,
            description: 'Led backend development and API integrations.',
        },
        {
            id: 'demo-exp-2',
            company: 'XYZ Company',
            position: 'Developer',
            location: 'Cape Town',
            start_date: '2020',
            end_date: '2022',
            current: false,
            description: 'Built web applications with Laravel and Vue.',
        },
    ],
    education: [
        {
            id: 'demo-edu-1',
            institution: 'University of Example',
            degree: 'Bachelor of Computer Science',
            field: 'Computer Science',
            start_date: '2016',
            end_date: '2020',
            description: '',
        },
    ],
    skills: [
        { id: 'demo-skill-1', name: 'Laravel', level: 'Expert' },
        { id: 'demo-skill-2', name: 'PHP', level: 'Expert' },
        { id: 'demo-skill-3', name: 'MySQL', level: 'Advanced' },
        { id: 'demo-skill-4', name: 'JavaScript', level: 'Advanced' },
        { id: 'demo-skill-5', name: 'API Integration', level: 'Advanced' },
    ],
    languages: [
        { id: 'demo-lang-1', name: 'English', proficiency: 'Native' },
        { id: 'demo-lang-2', name: 'Afrikaans', proficiency: 'Professional' },
    ],
    projects: [
        {
            id: 'demo-proj-1',
            name: 'CV Builder Platform',
            url: 'example.com',
            start_date: '2023',
            end_date: '2024',
            description: 'Full-stack SaaS application.',
        },
    ],
    certifications: [
        {
            id: 'demo-cert-1',
            name: 'AWS Cloud Practitioner',
            issuer: 'Amazon',
            date: '2023',
        },
    ],
    references: [],
    custom_sections: [],
};

/**
 * Build a standalone demo document for template thumbnail rendering.
 * Section order/enabled come from the template layout config at render time.
 */
export function buildTemplateDemoDocument() {
    return {
        schema_version: 1,
        template: {},
        personal_details: { ...TEMPLATE_DEMO_CONTENT.personal_details },
        experience: TEMPLATE_DEMO_CONTENT.experience.map((item) => ({ ...item })),
        education: TEMPLATE_DEMO_CONTENT.education.map((item) => ({ ...item })),
        skills: TEMPLATE_DEMO_CONTENT.skills.map((item) => ({ ...item })),
        languages: TEMPLATE_DEMO_CONTENT.languages.map((item) => ({ ...item })),
        projects: TEMPLATE_DEMO_CONTENT.projects.map((item) => ({ ...item })),
        certifications: TEMPLATE_DEMO_CONTENT.certifications.map((item) => ({ ...item })),
        references: [],
        custom_sections: [],
        meta: {
            section_order: [],
            enabled_sections: [],
            template_bindings: {},
        },
    };
}
