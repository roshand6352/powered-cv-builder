<?php

namespace App\Support\Cv;

/**
 * Realistic dummy CV data used to render accurate template previews on the
 * template selection page. This never touches user data or stored CVs.
 */
class CvSampleDocument
{
    /**
     * @return array<string, mixed>
     */
    public static function data(): array
    {
        return [
            'personal_details' => [
                'full_name' => 'John Smith',
                'job_title' => 'Senior Laravel Developer',
                'email' => 'john.smith@example.com',
                'phone' => '+1 (555) 010-2030',
                'location' => 'San Francisco, CA',
                'website' => 'johnsmith.dev',
                'linkedin' => 'linkedin.com/in/johnsmith',
                'summary' => 'Experienced Laravel Developer with 5+ years of experience building scalable web applications, REST APIs, and high-performance backends for growing product teams.',
                'photo_preview' => self::avatar(),
            ],
            'skills' => [
                ['name' => 'Laravel', 'level' => 'Expert'],
                ['name' => 'PHP', 'level' => 'Expert'],
                ['name' => 'MySQL', 'level' => 'Advanced'],
                ['name' => 'Vue.js', 'level' => 'Advanced'],
                ['name' => 'JavaScript', 'level' => 'Advanced'],
                ['name' => 'REST APIs', 'level' => 'Expert'],
            ],
            'experience' => [
                [
                    'position' => 'Senior Laravel Developer',
                    'company' => 'ABC Technologies',
                    'location' => 'San Francisco, CA',
                    'start_date' => '2022',
                    'end_date' => '',
                    'current' => true,
                    'description' => "Led development of a multi-tenant SaaS platform serving 50k+ users.\nArchitected REST APIs and improved response times by 40%.\nMentored a team of 4 developers and established code review standards.",
                ],
                [
                    'position' => 'Laravel Developer',
                    'company' => 'XYZ Solutions',
                    'location' => 'Remote',
                    'start_date' => '2020',
                    'end_date' => '2022',
                    'current' => false,
                    'description' => "Built and maintained customer-facing web applications using Laravel and Vue.js.\nIntegrated third-party payment gateways and reporting dashboards.",
                ],
            ],
            'education' => [
                [
                    'institution' => 'University of California',
                    'degree' => 'Bachelor of Computer Science',
                    'field' => 'Computer Science',
                    'start_date' => '2014',
                    'end_date' => '2018',
                    'description' => 'Graduated with honors. Focus on software engineering and databases.',
                ],
            ],
            'languages' => [
                ['name' => 'English', 'level' => 'Native'],
                ['name' => 'Spanish', 'level' => 'Intermediate'],
            ],
            'projects' => [
                [
                    'name' => 'OpenInvoice',
                    'description' => 'Open-source invoicing platform built with Laravel and Livewire.',
                    'link' => 'github.com/johnsmith/openinvoice',
                ],
            ],
            'certifications' => [
                [
                    'name' => 'Laravel Certified Developer',
                    'issuer' => 'Laravel',
                    'date' => '2023',
                ],
            ],
            'references' => [
                [
                    'name' => 'Jane Doe',
                    'position' => 'Engineering Manager, ABC Technologies',
                    'email' => 'jane.doe@example.com',
                    'phone' => '+1 (555) 909-1212',
                ],
            ],
        ];
    }

    /**
     * Lightweight inline placeholder avatar (keeps previews dependency-free).
     */
    public static function avatar(): string
    {
        $svg = <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" width="160" height="160" viewBox="0 0 160 160">
<rect width="160" height="160" fill="#e2e8f0"/>
<circle cx="80" cy="62" r="30" fill="#94a3b8"/>
<path d="M30 150c0-30 22-50 50-50s50 20 50 50z" fill="#94a3b8"/>
</svg>
SVG;

        return 'data:image/svg+xml;base64,'.base64_encode($svg);
    }
}
