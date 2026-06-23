<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Premium cover letter templates (5)
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'professional' => [
            'slug' => 'cover-letter-professional',
            'name' => 'Professional Cover Letter',
            'description' => 'Structured business letter with a bordered header and clear hierarchy.',
            'layout' => 'professional',
            'settings' => [
                'accent' => '#0f4c5c',
                'primary_color' => '#0f4c5c',
                'secondary_color' => '#1b9aaa',
                'text_color' => '#1e293b',
                'background_color' => '#ffffff',
                'font' => 'inter',
                'header_style' => 'bordered',
                'typography' => [
                    'font_size' => '14px',
                    'font_weight' => '400',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
                'spacing' => [
                    'page_padding' => '2.5rem',
                    'section_gap' => '1.35rem',
                ],
                'borders' => [
                    'style' => 'solid',
                    'width' => '2px',
                ],
            ],
        ],
        'modern' => [
            'slug' => 'cover-letter-modern',
            'name' => 'Modern Cover Letter',
            'description' => 'Clean inline header with indigo accents for contemporary applications.',
            'layout' => 'modern',
            'settings' => [
                'accent' => '#4f46e5',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#6366f1',
                'text_color' => '#334155',
                'background_color' => '#ffffff',
                'font' => 'sora',
                'header_style' => 'inline',
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                ],
            ],
        ],
        'executive' => [
            'slug' => 'cover-letter-executive',
            'name' => 'Executive Cover Letter',
            'description' => 'Authoritative serif layout for senior and leadership roles.',
            'layout' => 'executive',
            'settings' => [
                'accent' => '#1e293b',
                'primary_color' => '#1e293b',
                'secondary_color' => '#b45309',
                'text_color' => '#334155',
                'background_color' => '#ffffff',
                'font' => 'merriweather',
                'header_style' => 'stacked',
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
                'spacing' => [
                    'page_padding' => '2.75rem',
                    'section_gap' => '1.5rem',
                ],
                'borders' => [
                    'style' => 'solid',
                    'width' => '3px',
                ],
            ],
        ],
        'minimal' => [
            'slug' => 'cover-letter-minimal',
            'name' => 'Minimal Cover Letter',
            'description' => 'Whitespace-focused letter with understated typography.',
            'layout' => 'minimal',
            'settings' => [
                'accent' => '#475569',
                'primary_color' => '#475569',
                'secondary_color' => '#64748b',
                'text_color' => '#334155',
                'background_color' => '#ffffff',
                'font' => 'dm-sans',
                'header_style' => 'minimal',
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '600',
                ],
                'spacing' => [
                    'page_padding' => '3rem',
                    'section_gap' => '1.75rem',
                ],
                'borders' => [
                    'style' => 'none',
                    'width' => '1px',
                ],
            ],
        ],
        'elegant' => [
            'slug' => 'cover-letter-elegant',
            'name' => 'Elegant Cover Letter',
            'description' => 'Refined serif styling with graceful spacing and soft accents.',
            'layout' => 'elegant',
            'settings' => [
                'accent' => '#5b21b6',
                'primary_color' => '#5b21b6',
                'secondary_color' => '#7c3aed',
                'text_color' => '#3f3f46',
                'background_color' => '#ffffff',
                'font' => 'lora',
                'header_style' => 'stacked',
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '600',
                    'heading_style' => 'italic',
                ],
            ],
        ],
    ],

];
