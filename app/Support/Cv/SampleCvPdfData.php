<?php

namespace App\Support\Cv;

class SampleCvPdfData
{
    /**
     * @return array<string, mixed>
     */
    public static function make(): array
    {
        return [
            'full_name' => 'Sarah Mbeki',
            'job_title' => 'Senior Software Engineer',
            'photo' => self::photoPath(),
            'contact' => [
                'email' => 'sarah.mbeki@email.com',
                'phone' => '+27 82 555 1234',
                'address' => 'Sandton, Johannesburg, Gauteng, South Africa',
                'website' => 'www.sarahmbeki.dev',
                'linkedin' => 'linkedin.com/in/sarahmbeki',
            ],
            'summary' => 'Results-driven software engineer with 8+ years of experience designing and delivering scalable web applications. '
                .'Specialises in Laravel, cloud architecture, and cross-functional team leadership. '
                .'Passionate about building ATS-friendly products that help professionals present their best selves.',
            'skills' => [
                'PHP / Laravel',
                'JavaScript / Vue.js',
                'MySQL / PostgreSQL',
                'REST APIs',
                'AWS / Docker',
                'CI/CD Pipelines',
                'Agile / Scrum',
                'Technical Leadership',
            ],
            'experience' => [
                [
                    'title' => 'Senior Software Engineer',
                    'company' => 'TechFlow Solutions',
                    'location' => 'Johannesburg, SA',
                    'start' => 'Jan 2021',
                    'end' => 'Present',
                    'bullets' => [
                        'Led a team of 6 engineers to rebuild the core SaaS platform, reducing page load times by 40%.',
                        'Architected microservices migration using Laravel and AWS, improving deployment frequency from monthly to weekly.',
                        'Mentored junior developers and established code review standards adopted company-wide.',
                    ],
                ],
                [
                    'title' => 'Software Engineer',
                    'company' => 'Digital Innovations Ltd',
                    'location' => 'Cape Town, SA',
                    'start' => 'Mar 2018',
                    'end' => 'Dec 2020',
                    'bullets' => [
                        'Developed RESTful APIs serving 50,000+ daily active users with 99.9% uptime.',
                        'Implemented automated testing suite increasing code coverage from 45% to 85%.',
                        'Collaborated with product and design teams to deliver 12 major feature releases.',
                    ],
                ],
                [
                    'title' => 'Junior Developer',
                    'company' => 'StartUp Hub',
                    'location' => 'Pretoria, SA',
                    'start' => 'Jun 2016',
                    'end' => 'Feb 2018',
                    'bullets' => [
                        'Built customer-facing dashboards using Vue.js and Laravel.',
                        'Integrated third-party payment gateways including PayFast and Stripe.',
                    ],
                ],
            ],
            'education' => [
                [
                    'degree' => 'BSc Computer Science',
                    'institution' => 'University of the Witwatersrand',
                    'location' => 'Johannesburg, SA',
                    'start' => '2012',
                    'end' => '2015',
                    'details' => 'Dean\'s List, Honours in Software Engineering',
                ],
                [
                    'degree' => 'National Diploma IT',
                    'institution' => 'Tshwane University of Technology',
                    'location' => 'Pretoria, SA',
                    'start' => '2010',
                    'end' => '2012',
                    'details' => 'Graduated with Distinction',
                ],
            ],
            'certifications' => [
                ['name' => 'AWS Certified Solutions Architect', 'issuer' => 'Amazon Web Services', 'year' => '2023'],
                ['name' => 'Professional Scrum Master I', 'issuer' => 'Scrum.org', 'year' => '2022'],
                ['name' => 'Laravel Certified Developer', 'issuer' => 'Laravel', 'year' => '2021'],
            ],
            'languages' => [
                ['name' => 'English', 'level' => 'Native'],
                ['name' => 'Afrikaans', 'level' => 'Fluent'],
                ['name' => 'isiZulu', 'level' => 'Conversational'],
            ],
            'projects' => [
                [
                    'name' => 'ApplyDocs ZA',
                    'url' => 'applydocs.co.za',
                    'description' => 'AI-powered CV and cover letter builder serving 10,000+ South African job seekers.',
                ],
                [
                    'name' => 'OpenSource API Toolkit',
                    'url' => 'github.com/sarahmbeki/api-toolkit',
                    'description' => 'Laravel package for standardised REST API responses; 500+ GitHub stars.',
                ],
            ],
            'references' => [
                [
                    'name' => 'David Nkosi',
                    'title' => 'Engineering Manager, TechFlow Solutions',
                    'contact' => 'david.nkosi@techflow.co.za | +27 11 555 9876',
                ],
                [
                    'name' => 'Lisa van der Berg',
                    'title' => 'CTO, Digital Innovations Ltd',
                    'contact' => 'lisa@digitalinnovations.co.za | +27 21 555 4321',
                ],
            ],
            'colors' => [
                'sidebar_bg' => '#1a365d',
                'sidebar_text' => '#ffffff',
                'sidebar_muted' => '#cbd5e1',
                'accent' => '#2b6cb0',
                'text' => '#1e293b',
                'muted' => '#64748b',
                'border' => '#e2e8f0',
            ],
        ];
    }

    public static function photoPath(): string
    {
        $path = public_path('images/sample-cv-photo.png');

        if (! is_file($path)) {
            $dir = dirname($path);
            if (! is_dir($dir)) {
                mkdir($dir, 0755, true);
            }

            $png = base64_decode(
                'iVBORw0KGgoAAAANSUhEUgAAAGQAAABkCAYAAABw4pVUAAAABHNCSVQICAgIfAhkiAAAAAlwSFlz'
                .'AAALEgAACxIB0t1+/AAAABZ0RVh0Q3JlYXRpb24gVGltZQAwNi8yNC8yNFLl0CkAAAAcdEVYdFNvZnR3YXJl'
                .'AEFkb2JlIEZpcmV3b3JrcyBDUzXx0p0AAAA6SURBVHic7cEBAQAAAIIg/69uSEABAAAAAAAAAAAAAAAA'
                .'AAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADgZwEPAAFqk0oAAAAASUVORK5CYII=',
                true,
            );

            if (is_string($png)) {
                file_put_contents($path, $png);
            }
        }

        return $path;
    }
}
