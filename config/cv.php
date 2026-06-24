<?php

use App\Enums\CvSectionType;
use App\Support\Cv\CvDocumentMigrator;

return [

    'default_title' => 'Untitled CV',

    'schema_version' => CvDocumentMigrator::CURRENT_SCHEMA,

    'sections' => [
        CvSectionType::PersonalDetails->value => [
            'label' => 'Personal Details',
            'icon' => 'user',
            'repeatable' => false,
            'preview_type' => 'object',
        ],
        CvSectionType::Experience->value => [
            'label' => 'Experience',
            'icon' => 'briefcase',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::Education->value => [
            'label' => 'Education',
            'icon' => 'academic-cap',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::Skills->value => [
            'label' => 'Skills',
            'icon' => 'sparkles',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::Languages->value => [
            'label' => 'Languages',
            'icon' => 'globe',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::Projects->value => [
            'label' => 'Projects',
            'icon' => 'folder',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::Certifications->value => [
            'label' => 'Certifications',
            'icon' => 'badge',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::References->value => [
            'label' => 'References',
            'icon' => 'users',
            'repeatable' => true,
            'preview_type' => 'list',
        ],
        CvSectionType::CustomSections->value => [
            'label' => 'Custom Sections',
            'icon' => 'plus',
            'repeatable' => true,
            'preview_type' => 'groups',
        ],
    ],

    'defaults' => [
        'meta' => [
            'section_order' => CvSectionType::orderedKeys(),
            'enabled_sections' => CvSectionType::orderedKeys(),
            'template_bindings' => [],
            'last_edited_section' => null,
            'locale' => 'en',
        ],
    ],

    'schema' => [
        'template' => [
            'id' => null,
            'slug' => null,
            'key' => 'default',
            'settings' => [
                'layout' => 'single-column',
                'font' => 'figtree',
                'accent' => 'brand',
            ],
        ],
        'personal_details' => [
            'full_name' => '',
            'job_title' => '',
            'email' => '',
            'phone' => '',
            'location' => '',
            'website' => '',
            'linkedin' => '',
            'summary' => '',
            'photo_preview' => '',
        ],
        'experience' => [
            'id' => null,
            'company' => '',
            'position' => '',
            'location' => '',
            'start_date' => '',
            'end_date' => '',
            'current' => false,
            'description' => '',
        ],
        'education' => [
            'id' => null,
            'institution' => '',
            'degree' => '',
            'field' => '',
            'start_date' => '',
            'end_date' => '',
            'in_progress' => false,
            'description' => '',
        ],
        'skills' => [
            'id' => null,
            'name' => '',
            'level' => '',
        ],
        'languages' => [
            'id' => null,
            'name' => '',
            'proficiency' => '',
        ],
        'projects' => [
            'id' => null,
            'name' => '',
            'url' => '',
            'start_date' => '',
            'end_date' => '',
            'in_progress' => false,
            'description' => '',
        ],
        'certifications' => [
            'id' => null,
            'name' => '',
            'issuer' => '',
            'date' => '',
            'url' => '',
        ],
        'references' => [
            'id' => null,
            'name' => '',
            'company' => '',
            'email' => '',
            'phone' => '',
            'relationship' => '',
        ],
        'custom_sections' => [
            'id' => null,
            'title' => '',
            'order' => 0,
            'items' => [],
        ],
        'custom_section_item' => [
            'id' => null,
            'title' => '',
            'content' => '',
        ],
    ],

    // Builder UI item templates (Alpine add-item defaults)
    'templates' => [
        'experience' => [
            'id' => null,
            'company' => '',
            'position' => '',
            'location' => '',
            'start_date' => '',
            'end_date' => '',
            'current' => false,
            'description' => '',
        ],
        'education' => [
            'id' => null,
            'institution' => '',
            'degree' => '',
            'field' => '',
            'start_date' => '',
            'end_date' => '',
            'in_progress' => false,
            'description' => '',
        ],
        'skills' => [
            'id' => null,
            'name' => '',
            'level' => '',
        ],
        'languages' => [
            'id' => null,
            'name' => '',
            'proficiency' => '',
        ],
        'projects' => [
            'id' => null,
            'name' => '',
            'url' => '',
            'start_date' => '',
            'end_date' => '',
            'in_progress' => false,
            'description' => '',
        ],
        'references' => [
            'id' => null,
            'name' => '',
            'company' => '',
            'email' => '',
            'phone' => '',
            'relationship' => '',
        ],
        'certifications' => [
            'id' => null,
            'name' => '',
            'issuer' => '',
            'date' => '',
            'url' => '',
        ],
        'custom_sections' => [
            'id' => null,
            'title' => '',
            'order' => 0,
            'items' => [],
        ],
        'custom_section_item' => [
            'id' => null,
            'title' => '',
            'content' => '',
        ],
    ],

    'autosave' => [
        'debounce_ms' => 1000,
    ],

    // LIVE PREVIEW FIX: separate fast debounce for live preview rendering
    'preview' => [
        'debounce_ms' => 200,
    ],

];
