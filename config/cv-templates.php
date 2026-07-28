<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Template Renderer
    |--------------------------------------------------------------------------
    */
    'renderer' => [
        'fallback' => 'classic',
        'wrapper' => 'web.cv.templates.wrapper',
        'json_renderer' => 'web.cv.templates.partials.json-renderer',
    ],

    'fontawesome' => [
        'version' => '6.5.2',
        'path' => 'vendor/fontawesome/css/all.min.css',
    ],

    'modes' => [
        'preview' => [
            'responsive' => true,
            'scale' => 1,
        ],
        'thumbnail' => [
            'responsive' => false,
            'scale' => 0.35,
            'width' => 794,
        ],
        'pdf' => [
            'responsive' => false,
            'scale' => 1,
            'paginate' => true,
        ],
        'export' => [
            'responsive' => false,
            'scale' => 1,
            'paginate' => true,
        ],
    ],

    'watermark' => [
        'primary_text' => 'FREE VERSION',
        'secondary_text' => 'Generated with Free Plan',
        'text_opacity' => 0.20,
        'logo_opacity' => 0.20,
        'angle' => -30,
        // Large enough to read diagonally; kept under the rotated A4 fit limit to avoid cropping.
        'logo_display_width_px' => 900,
        'use_logo' => true,
        'logo' => 'images/WATERMARK-LOGO.png',
        'plans_without_watermark' => ['pro', 'premium', 'business'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Premium ATS-friendly templates (10)
    |--------------------------------------------------------------------------
    */
    'templates' => [
        'professional' => [
            'name' => 'Professional',
            'description' => 'Structured two-column layout for business and operations roles.',
            'layout' => 'professional',
            'settings' => [
                'accent' => '#0f4c5c',
                'primary_color' => '#0f4c5c',
                'secondary_color' => '#1b9aaa',
                'text_color' => '#1e293b',
                'font' => 'inter',
                'sidebar_position' => 'left',
                'photo_position' => 'sidebar',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
                'spacing' => [
                    'page_padding' => '2.25rem',
                    'section_gap' => '1.5rem',
                ],
            ],
        ],
        'modern' => [
            'name' => 'Modern',
            'description' => 'Clean single-column design with bold indigo accents.',
            'layout' => 'modern',
            'settings' => [
                'accent' => '#4f46e5',
                'primary_color' => '#4f46e5',
                'secondary_color' => '#6366f1',
                'text_color' => '#334155',
                'font' => 'sora',
                'sidebar_position' => 'none',
                'photo_position' => 'top_left',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                ],
            ],
        ],
        'executive' => [
            'name' => 'Executive',
            'description' => 'Authoritative serif typography for senior leadership profiles.',
            'layout' => 'executive',
            'settings' => [
                'accent' => '#1e293b',
                'primary_color' => '#1e293b',
                'secondary_color' => '#b45309',
                'text_color' => '#334155',
                'font' => 'merriweather',
                'sidebar_position' => 'none',
                'photo_position' => 'center',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
            ],
        ],
        'minimal' => [
            'name' => 'Minimal',
            'description' => 'Whitespace-first layout with understated, ATS-safe typography.',
            'layout' => 'minimal',
            'settings' => [
                'accent' => '#475569',
                'primary_color' => '#475569',
                'secondary_color' => '#64748b',
                'text_color' => '#334155',
                'font' => 'dm-sans',
                'sidebar_position' => 'none',
                'photo_position' => 'hidden',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '600',
                ],
                'borders' => [
                    'style' => 'none',
                    'width' => '1px',
                ],
                'spacing' => [
                    'page_padding' => '3rem',
                    'section_gap' => '2rem',
                ],
            ],
        ],
        'elegant' => [
            'name' => 'Elegant',
            'description' => 'Refined serif accents with graceful spacing and soft dividers.',
            'layout' => 'elegant',
            'settings' => [
                'accent' => '#5b21b6',
                'primary_color' => '#5b21b6',
                'secondary_color' => '#7c3aed',
                'text_color' => '#3f3f46',
                'font' => 'lora',
                'sidebar_position' => 'none',
                'photo_position' => 'center',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '600',
                    'heading_style' => 'italic',
                ],
            ],
        ],
        'corporate' => [
            'name' => 'Corporate',
            'description' => 'Navy sidebar panel for contact and skills — ideal for corporate CVs.',
            'layout' => 'corporate',
            'settings' => [
                'accent' => '#0c2340',
                'primary_color' => '#0c2340',
                'secondary_color' => '#2563eb',
                'text_color' => '#1e293b',
                'background_color' => '#ffffff',
                'sidebar_background_color' => '#0c2340',
                'font' => 'roboto',
                'sidebar_position' => 'right',
                'photo_position' => 'sidebar',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
            ],
        ],
        'creative' => [
            'name' => 'Creative',
            'description' => 'Vibrant accent bar with sidebar skills for designers and marketers.',
            'layout' => 'creative',
            'settings' => [
                'accent' => '#db2777',
                'primary_color' => '#db2777',
                'secondary_color' => '#f59e0b',
                'text_color' => '#374151',
                'font' => 'sora',
                'sidebar_position' => 'left',
                'photo_position' => 'top_left',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                ],
                'borders' => [
                    'radius' => '4px',
                ],
            ],
        ],
        'premium' => [
            'name' => 'Premium',
            'description' => 'High-contrast gold accents with executive presentation.',
            'layout' => 'premium',
            'settings' => [
                'accent' => '#18181b',
                'primary_color' => '#18181b',
                'secondary_color' => '#ca8a04',
                'text_color' => '#27272a',
                'font' => 'merriweather',
                'sidebar_position' => 'none',
                'photo_position' => 'top_right',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
            ],
        ],
        'smart' => [
            'name' => 'Smart',
            'description' => 'Timeline experience layout with a compact skills sidebar.',
            'layout' => 'smart',
            'settings' => [
                'accent' => '#0369a1',
                'primary_color' => '#0369a1',
                'secondary_color' => '#0ea5e9',
                'text_color' => '#1e3a5f',
                'font' => 'inter',
                'sidebar_position' => 'left',
                'photo_position' => 'top_right',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                ],
            ],
        ],
        'classic' => [
            'name' => 'Classic',
            'description' => 'Traditional centered header with timeless single-column flow.',
            'layout' => 'classic',
            'settings' => [
                'accent' => '#172d40',
                'primary_color' => '#172d40',
                'secondary_color' => '#c1623d',
                'text_color' => '#3a4a58',
                'font' => 'figtree',
                'sidebar_position' => 'none',
                'photo_position' => 'center',
                'icons' => ['enabled' => true],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                ],
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | DomPDF-native CV templates (standalone blade renderers)
    |--------------------------------------------------------------------------
    */
    'dompdf_templates' => [
        'dompdf-professional-sidebar' => [
            'name' => 'Professional Sidebar',
            'description' => 'Modern corporate layout with dark sidebar — optimised for DomPDF export.',
            'layout' => 'corporate',
            'pdf_blade' => 'pdf.template-1',
            'settings' => [
                'accent' => '#0f2744',
                'primary_color' => '#0f2744',
                'secondary_color' => '#3b82f6',
                'text_color' => '#1e293b',
                'sidebar_background_color' => '#0f2744',
                'font' => 'inter',
                'sidebar_position' => 'left',
                'photo_position' => 'sidebar',
                'icons' => ['enabled' => false],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
            ],
        ],
        'dompdf-executive-clean' => [
            'name' => 'Executive Clean',
            'description' => 'Minimalist executive style with full-width header and two-column body.',
            'layout' => 'executive',
            'pdf_blade' => 'pdf.template-2',
            'settings' => [
                'accent' => '#1d4ed8',
                'primary_color' => '#1d4ed8',
                'secondary_color' => '#3b82f6',
                'text_color' => '#1a2332',
                'font' => 'inter',
                'sidebar_position' => 'none',
                'photo_position' => 'hidden',
                'icons' => ['enabled' => false],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
            ],
        ],
        'dompdf-premium-ats' => [
            'name' => 'Premium ATS',
            'description' => 'Single-column ATS-optimised layout with bordered sections and multi-page support.',
            'layout' => 'minimal',
            'pdf_blade' => 'pdf.template-3',
            'settings' => [
                'accent' => '#111827',
                'primary_color' => '#111827',
                'secondary_color' => '#374151',
                'text_color' => '#111827',
                'font' => 'inter',
                'sidebar_position' => 'none',
                'photo_position' => 'top_right',
                'icons' => ['enabled' => false],
                'typography' => [
                    'font_size' => '14px',
                    'heading_weight' => '700',
                    'heading_style' => 'uppercase',
                ],
                'borders' => [
                    'style' => 'solid',
                    'width' => '1px',
                ],
            ],
        ],
    ],

];
