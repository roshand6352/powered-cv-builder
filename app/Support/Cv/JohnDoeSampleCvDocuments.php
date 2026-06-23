<?php

namespace App\Support\Cv;

/**
 * Five realistic sample CV profiles for John Doe, each mapped to a distinct template.
 *
 * @return list<array{slug: string, title: string, content: array<string, mixed>}>
 */
class JohnDoeSampleCvDocuments
{
    /**
     * @return list<array{slug: string, title: string, content: array<string, mixed>}>
     */
    public static function profiles(): array
    {
        return [
            self::fullStackDeveloper(),
            self::digitalMarketingManager(),
            self::itDirector(),
            self::backendEngineer(),
            self::solutionsArchitect(),
        ];
    }

    /**
     * @return array{slug: string, title: string, content: array<string, mixed>}
     */
    protected static function fullStackDeveloper(): array
    {
        return [
            'slug' => 'professional',
            'title' => 'Senior Full Stack Developer',
            'content' => [
                'personal_details' => self::personalDetails(
                    jobTitle: 'Senior Full Stack Developer',
                    summary: 'Results-driven full stack developer with 8+ years building scalable web applications for fintech and SaaS companies across South Africa and Europe. Expert in Laravel, Vue.js, and cloud-native architectures. Known for translating business requirements into maintainable code, mentoring junior developers, and shipping features that measurably improve conversion and retention.',
                ),
                'experience' => [
                    [
                        'company' => 'PayFlow Africa',
                        'position' => 'Senior Full Stack Developer',
                        'location' => 'Cape Town, South Africa',
                        'start_date' => '2021-03',
                        'end_date' => '',
                        'current' => true,
                        'description' => "Lead engineer on a payments platform processing R2.5B+ annually across 12 African markets.\nDesigned and implemented a multi-tenant API gateway reducing partner onboarding time from 6 weeks to 10 days.\nIntroduced automated testing and CI/CD pipelines, cutting production incidents by 45% year-over-year.\nMentored a team of 5 developers and established architecture review practices.",
                    ],
                    [
                        'company' => 'CloudBridge Solutions',
                        'position' => 'Full Stack Developer',
                        'location' => 'Johannesburg, South Africa (Remote)',
                        'start_date' => '2018-06',
                        'end_date' => '2021-02',
                        'current' => false,
                        'description' => "Built customer-facing dashboards and internal admin tools using Laravel, Vue 3, and PostgreSQL.\nIntegrated Stripe, PayFast, and Ozow payment providers with idempotent webhook handling.\nOptimised database queries and caching strategy, improving average page load time by 38%.",
                    ],
                    [
                        'company' => 'WebCraft Studio',
                        'position' => 'Junior PHP Developer',
                        'location' => 'Cape Town, South Africa',
                        'start_date' => '2016-01',
                        'end_date' => '2018-05',
                        'current' => false,
                        'description' => "Developed bespoke WordPress and Laravel sites for SME clients in retail and hospitality.\nCollaborated with designers to deliver responsive, accessible front-ends.\nMaintained legacy codebases and documented deployment procedures for client handovers.",
                    ],
                ],
                'education' => [
                    [
                        'institution' => 'University of Cape Town',
                        'degree' => 'BSc Computer Science',
                        'field' => 'Computer Science',
                        'start_date' => '2012',
                        'end_date' => '2015',
                        'description' => 'Graduated with distinction. Dissertation on distributed caching strategies for high-traffic web applications. Member of the UCT Developer Society.',
                    ],
                    [
                        'institution' => 'HyperionDev',
                        'degree' => 'Certificate in Full Stack Web Development',
                        'field' => 'Software Engineering',
                        'start_date' => '2015',
                        'end_date' => '2016',
                        'description' => 'Intensive bootcamp covering PHP, JavaScript, databases, and agile delivery practices.',
                    ],
                ],
                'skills' => [
                    ['name' => 'Laravel', 'level' => 'Expert'],
                    ['name' => 'PHP', 'level' => 'Expert'],
                    ['name' => 'Vue.js', 'level' => 'Advanced'],
                    ['name' => 'TypeScript', 'level' => 'Advanced'],
                    ['name' => 'PostgreSQL', 'level' => 'Advanced'],
                    ['name' => 'Redis', 'level' => 'Advanced'],
                    ['name' => 'Docker', 'level' => 'Advanced'],
                    ['name' => 'AWS', 'level' => 'Intermediate'],
                    ['name' => 'REST APIs', 'level' => 'Expert'],
                    ['name' => 'Git', 'level' => 'Expert'],
                ],
                'languages' => [
                    ['name' => 'English', 'proficiency' => 'Native'],
                    ['name' => 'Afrikaans', 'proficiency' => 'Fluent'],
                    ['name' => 'isiXhosa', 'proficiency' => 'Conversational'],
                ],
                'projects' => [
                    [
                        'name' => 'InvoiceForge',
                        'url' => 'github.com/johndoe/invoice-forge',
                        'start_date' => '2023-01',
                        'end_date' => '2023-06',
                        'description' => 'Open-source invoicing toolkit for South African freelancers with SARS-compliant PDF exports, recurring billing, and multi-currency support. Adopted by 1,200+ developers on GitHub.',
                    ],
                    [
                        'name' => 'Cape Town Tech Meetup Platform',
                        'url' => 'capetowntech.dev',
                        'start_date' => '2022-03',
                        'end_date' => '',
                        'description' => 'Community event management platform built with Laravel Livewire. Handles RSVPs, speaker submissions, and sponsor dashboards for monthly meetups with 300+ attendees.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'AWS Certified Developer – Associate',
                        'issuer' => 'Amazon Web Services',
                        'date' => '2023-09',
                        'url' => 'aws.amazon.com/certification',
                    ],
                    [
                        'name' => 'Laravel Certified Developer',
                        'issuer' => 'Laravel',
                        'date' => '2022-04',
                        'url' => 'laravel.com/certification',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{slug: string, title: string, content: array<string, mixed>}
     */
    protected static function digitalMarketingManager(): array
    {
        return [
            'slug' => 'modern',
            'title' => 'Digital Marketing Manager',
            'content' => [
                'personal_details' => self::personalDetails(
                    jobTitle: 'Digital Marketing Manager',
                    summary: 'Strategic digital marketer with 7 years of experience growing B2B and B2C brands across Southern Africa. Proven track record in performance marketing, content strategy, and marketing automation. Combines data-driven decision making with creative storytelling to deliver consistent pipeline growth and brand awareness.',
                ),
                'experience' => [
                    [
                        'company' => 'GrowthHive Agency',
                        'position' => 'Digital Marketing Manager',
                        'location' => 'Cape Town, South Africa',
                        'start_date' => '2020-08',
                        'end_date' => '',
                        'current' => true,
                        'description' => "Manage a portfolio of 8 client accounts with combined annual ad spend exceeding R12M.\nIncreased qualified leads by 62% for a fintech client through Google Ads, LinkedIn, and landing page optimisation.\nBuilt reporting dashboards in Looker Studio tracking ROAS, CAC, and funnel conversion across channels.\nLead a team of 3 specialists covering SEO, paid media, and email marketing.",
                    ],
                    [
                        'company' => 'RetailNation',
                        'position' => 'Digital Marketing Specialist',
                        'location' => 'Durban, South Africa',
                        'start_date' => '2017-04',
                        'end_date' => '2020-07',
                        'current' => false,
                        'description' => "Ran paid social campaigns across Facebook, Instagram, and TikTok for a national e-commerce retailer.\nGrew email subscriber list from 45K to 180K through lead magnets and lifecycle automation.\nCoordinated seasonal campaigns that contributed to a 28% year-on-year revenue increase during peak trading.",
                    ],
                    [
                        'company' => 'BrightSpark Media',
                        'position' => 'Marketing Coordinator',
                        'location' => 'Pretoria, South Africa',
                        'start_date' => '2015-02',
                        'end_date' => '2017-03',
                        'current' => false,
                        'description' => "Supported content production, social scheduling, and influencer outreach for lifestyle brands.\nManaged WordPress blogs and basic on-page SEO for 15 client websites.\nAssisted with event marketing and trade show collateral for product launches.",
                    ],
                ],
                'education' => [
                    [
                        'institution' => 'University of the Witwatersrand',
                        'degree' => 'BCom Marketing Management',
                        'field' => 'Marketing',
                        'start_date' => '2011',
                        'end_date' => '2014',
                        'description' => 'Dean\'s List recipient. Specialised in consumer behaviour, brand management, and digital strategy.',
                    ],
                    [
                        'institution' => 'Google Digital Garage',
                        'degree' => 'Fundamentals of Digital Marketing',
                        'field' => 'Digital Marketing',
                        'start_date' => '2019',
                        'end_date' => '2019',
                        'description' => 'Comprehensive certification covering SEO, SEM, analytics, and content marketing fundamentals.',
                    ],
                ],
                'skills' => [
                    ['name' => 'Google Ads', 'level' => 'Expert'],
                    ['name' => 'Meta Ads', 'level' => 'Expert'],
                    ['name' => 'SEO', 'level' => 'Advanced'],
                    ['name' => 'Google Analytics 4', 'level' => 'Advanced'],
                    ['name' => 'HubSpot', 'level' => 'Advanced'],
                    ['name' => 'Mailchimp', 'level' => 'Advanced'],
                    ['name' => 'Content Strategy', 'level' => 'Expert'],
                    ['name' => 'A/B Testing', 'level' => 'Advanced'],
                    ['name' => 'Canva', 'level' => 'Advanced'],
                    ['name' => 'Copywriting', 'level' => 'Advanced'],
                ],
                'languages' => [
                    ['name' => 'English', 'proficiency' => 'Native'],
                    ['name' => 'Afrikaans', 'proficiency' => 'Fluent'],
                    ['name' => 'Zulu', 'proficiency' => 'Basic'],
                ],
                'projects' => [
                    [
                        'name' => 'SA Startup Marketing Playbook',
                        'url' => 'growthhive.co.za/playbook',
                        'start_date' => '2023-05',
                        'end_date' => '2024-01',
                        'description' => 'Authored a free 80-page guide for early-stage South African startups covering channel selection, budget allocation, and POPIA-compliant lead capture. Downloaded 4,500+ times.',
                    ],
                    [
                        'name' => 'EcoCart Rebrand Campaign',
                        'url' => 'ecocart.co.za',
                        'start_date' => '2022-01',
                        'end_date' => '2022-09',
                        'description' => 'Led end-to-end digital rebrand for a sustainable packaging startup: new positioning, website relaunch, and launch campaign achieving 3.2x ROAS in the first quarter.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'Google Ads Search Certification',
                        'issuer' => 'Google',
                        'date' => '2024-02',
                        'url' => 'skillshop.withgoogle.com',
                    ],
                    [
                        'name' => 'HubSpot Inbound Marketing Certification',
                        'issuer' => 'HubSpot Academy',
                        'date' => '2021-11',
                        'url' => 'academy.hubspot.com',
                    ],
                    [
                        'name' => 'Meta Certified Digital Marketing Associate',
                        'issuer' => 'Meta',
                        'date' => '2023-07',
                        'url' => 'facebook.com/business/learn',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{slug: string, title: string, content: array<string, mixed>}
     */
    protected static function itDirector(): array
    {
        return [
            'slug' => 'executive',
            'title' => 'IT Director',
            'content' => [
                'personal_details' => self::personalDetails(
                    jobTitle: 'IT Director',
                    summary: 'Seasoned technology leader with 14 years guiding IT strategy, digital transformation, and enterprise operations for mid-to-large organisations. Skilled at aligning technology investments with business outcomes, building high-performing teams, and managing multi-million rand budgets. Trusted advisor to C-suite executives on cybersecurity, cloud migration, and vendor governance.',
                ),
                'experience' => [
                    [
                        'company' => 'African Logistics Group',
                        'position' => 'IT Director',
                        'location' => 'Johannesburg, South Africa',
                        'start_date' => '2019-01',
                        'end_date' => '',
                        'current' => true,
                        'description' => "Direct IT strategy for a 2,400-employee logistics company operating in 6 countries.\nLed cloud migration of ERP and warehouse systems to Azure, saving R8.2M annually in infrastructure costs.\nEstablished a cybersecurity programme achieving ISO 27001 readiness and zero major breaches in 4 years.\nManage annual IT budget of R45M and a department of 32 staff across infrastructure, development, and support.",
                    ],
                    [
                        'company' => 'MediCare Holdings',
                        'position' => 'Head of IT Operations',
                        'location' => 'Cape Town, South Africa',
                        'start_date' => '2014-06',
                        'end_date' => '2018-12',
                        'current' => false,
                        'description' => "Oversaw data centre operations, service desk, and network infrastructure for 18 clinics.\nImplemented ITIL-based service management, improving ticket resolution SLA from 72% to 94%.\nNegotiated vendor contracts reducing software licensing costs by 22% without service degradation.",
                    ],
                    [
                        'company' => 'StandardTech Consulting',
                        'position' => 'Senior IT Project Manager',
                        'location' => 'Sandton, South Africa',
                        'start_date' => '2010-03',
                        'end_date' => '2014-05',
                        'current' => false,
                        'description' => "Delivered ERP and CRM implementations for banking and insurance clients using Prince2 methodology.\nManaged cross-functional teams of up to 25 consultants and client stakeholders.\nConsistently delivered projects within 5% of approved budget across a R120M portfolio.",
                    ],
                ],
                'education' => [
                    [
                        'institution' => 'University of Stellenbosch',
                        'degree' => 'MBA',
                        'field' => 'Business Administration',
                        'start_date' => '2016',
                        'end_date' => '2018',
                        'description' => 'Part-time programme with focus on strategic management, finance, and organisational leadership. Graduated cum laude.',
                    ],
                    [
                        'institution' => 'University of Pretoria',
                        'degree' => 'BSc Information Technology',
                        'field' => 'Information Technology',
                        'start_date' => '2006',
                        'end_date' => '2009',
                        'description' => 'Major in systems analysis and network engineering. Student representative for the IT faculty.',
                    ],
                ],
                'skills' => [
                    ['name' => 'IT Strategy', 'level' => 'Expert'],
                    ['name' => 'Digital Transformation', 'level' => 'Expert'],
                    ['name' => 'Budget Management', 'level' => 'Expert'],
                    ['name' => 'Cybersecurity Governance', 'level' => 'Advanced'],
                    ['name' => 'Azure / Cloud Architecture', 'level' => 'Advanced'],
                    ['name' => 'Vendor Management', 'level' => 'Expert'],
                    ['name' => 'ITIL', 'level' => 'Advanced'],
                    ['name' => 'Prince2', 'level' => 'Advanced'],
                    ['name' => 'Stakeholder Management', 'level' => 'Expert'],
                    ['name' => 'Team Leadership', 'level' => 'Expert'],
                ],
                'languages' => [
                    ['name' => 'English', 'proficiency' => 'Native'],
                    ['name' => 'Afrikaans', 'proficiency' => 'Fluent'],
                ],
                'projects' => [
                    [
                        'name' => 'Enterprise Cloud Migration Programme',
                        'url' => '',
                        'start_date' => '2020-06',
                        'end_date' => '2022-12',
                        'description' => 'Programme director for phased migration of 140 workloads to Azure. Delivered 99.97% uptime during transition, decommissioned two on-prem data centres, and trained 60 staff on cloud operations.',
                    ],
                    [
                        'name' => 'IT Service Modernisation Initiative',
                        'url' => '',
                        'start_date' => '2021-01',
                        'end_date' => '2023-06',
                        'description' => 'Replaced legacy service desk tooling with ServiceNow, introduced self-service portals and automated provisioning. Reduced L1 ticket volume by 35% and improved employee satisfaction scores by 28 points.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'ITIL 4 Managing Professional',
                        'issuer' => 'AXELOS',
                        'date' => '2022-03',
                        'url' => 'axelos.com',
                    ],
                    [
                        'name' => 'Prince2 Practitioner',
                        'issuer' => 'PeopleCert',
                        'date' => '2015-09',
                        'url' => 'peoplecert.org',
                    ],
                    [
                        'name' => 'Microsoft Certified: Azure Administrator Associate',
                        'issuer' => 'Microsoft',
                        'date' => '2021-08',
                        'url' => 'learn.microsoft.com',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{slug: string, title: string, content: array<string, mixed>}
     */
    protected static function backendEngineer(): array
    {
        return [
            'slug' => 'minimal',
            'title' => 'Backend Software Engineer',
            'content' => [
                'personal_details' => self::personalDetails(
                    jobTitle: 'Backend Software Engineer',
                    summary: 'Backend engineer focused on building reliable, high-throughput APIs and data pipelines. Strong background in PHP, Go, and event-driven architectures. Passionate about clean code, observability, and designing systems that scale gracefully under load.',
                ),
                'experience' => [
                    [
                        'company' => 'StreamPay',
                        'position' => 'Senior Backend Engineer',
                        'location' => 'Cape Town, South Africa (Hybrid)',
                        'start_date' => '2022-01',
                        'end_date' => '',
                        'current' => true,
                        'description' => "Own core billing and subscription microservices handling 500K+ daily API requests.\nMigrated monolithic payment module to Go services with Kafka event streaming, reducing p99 latency from 820ms to 190ms.\nImplemented OpenTelemetry tracing and structured logging across 12 services.\nConduct code reviews and pair programming sessions with mid-level engineers.",
                    ],
                    [
                        'company' => 'DataSync Labs',
                        'position' => 'Backend Developer',
                        'location' => 'Remote',
                        'start_date' => '2019-05',
                        'end_date' => '2021-12',
                        'current' => false,
                        'description' => "Built ETL pipelines ingesting data from 40+ third-party sources into a central warehouse.\nDeveloped REST and GraphQL APIs in Laravel for internal analytics dashboards.\nIntroduced database migration tooling and zero-downtime deployment practices.",
                    ],
                    [
                        'company' => 'InnoSoft',
                        'position' => 'PHP Developer',
                        'location' => 'Port Elizabeth, South Africa',
                        'start_date' => '2017-07',
                        'end_date' => '2019-04',
                        'current' => false,
                        'description' => "Maintained legacy Symfony applications and gradually refactored critical modules to Laravel.\nWrote integration tests and documentation for client API integrations.\nSupported on-call rotation for production incidents.",
                    ],
                ],
                'education' => [
                    [
                        'institution' => 'Nelson Mandela University',
                        'degree' => 'BSc Information Systems',
                        'field' => 'Information Systems',
                        'start_date' => '2013',
                        'end_date' => '2016',
                        'description' => 'Focus on software engineering, databases, and systems design. Final-year project: real-time inventory sync service for retail chains.',
                    ],
                ],
                'skills' => [
                    ['name' => 'Go', 'level' => 'Advanced'],
                    ['name' => 'PHP / Laravel', 'level' => 'Expert'],
                    ['name' => 'PostgreSQL', 'level' => 'Expert'],
                    ['name' => 'Redis', 'level' => 'Advanced'],
                    ['name' => 'Kafka', 'level' => 'Advanced'],
                    ['name' => 'Docker / Kubernetes', 'level' => 'Advanced'],
                    ['name' => 'GraphQL', 'level' => 'Intermediate'],
                    ['name' => 'System Design', 'level' => 'Advanced'],
                    ['name' => 'CI/CD', 'level' => 'Advanced'],
                    ['name' => 'Linux', 'level' => 'Advanced'],
                ],
                'languages' => [
                    ['name' => 'English', 'proficiency' => 'Native'],
                    ['name' => 'Afrikaans', 'proficiency' => 'Fluent'],
                ],
                'projects' => [
                    [
                        'name' => 'QueueGuard',
                        'url' => 'github.com/johndoe/queue-guard',
                        'start_date' => '2023-08',
                        'end_date' => '',
                        'description' => 'Laravel package for monitoring failed queue jobs with Slack alerts, retry policies, and dead-letter dashboards. Published on Packagist with 800+ monthly downloads.',
                    ],
                    [
                        'name' => 'Weather API Aggregator',
                        'url' => 'github.com/johndoe/weather-aggregator',
                        'start_date' => '2021-02',
                        'end_date' => '2021-08',
                        'description' => 'Go service that normalises forecasts from multiple providers with caching and circuit breakers. Handles 10K requests/minute in load tests.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'Certified Kubernetes Application Developer (CKAD)',
                        'issuer' => 'Cloud Native Computing Foundation',
                        'date' => '2023-11',
                        'url' => 'cncf.io/certification',
                    ],
                    [
                        'name' => 'MongoDB Certified Developer',
                        'issuer' => 'MongoDB University',
                        'date' => '2020-06',
                        'url' => 'university.mongodb.com',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array{slug: string, title: string, content: array<string, mixed>}
     */
    protected static function solutionsArchitect(): array
    {
        return [
            'slug' => 'elegant',
            'title' => 'Solutions Architect',
            'content' => [
                'personal_details' => self::personalDetails(
                    jobTitle: 'Solutions Architect',
                    summary: 'Enterprise solutions architect with a decade of experience designing cloud-native platforms for financial services, insurance, and public sector clients. Bridges the gap between business stakeholders and engineering teams, delivering architectures that balance scalability, security, compliance, and total cost of ownership.',
                ),
                'experience' => [
                    [
                        'company' => 'Accenture South Africa',
                        'position' => 'Senior Solutions Architect',
                        'location' => 'Johannesburg, South Africa',
                        'start_date' => '2020-04',
                        'end_date' => '',
                        'current' => true,
                        'description' => "Lead architect on a R180M digital banking modernisation programme for a tier-1 African bank.\nDesigned event-driven integration layer connecting core banking, CRM, and mobile channels.\nAuthored architecture decision records, threat models, and non-functional requirements for 8 product squads.\nPresented solution designs to client steering committees and regulatory reviewers.",
                    ],
                    [
                        'company' => 'Oracle South Africa',
                        'position' => 'Cloud Solutions Architect',
                        'location' => 'Cape Town, South Africa',
                        'start_date' => '2016-09',
                        'end_date' => '2020-03',
                        'current' => false,
                        'description' => "Advised enterprise clients on OCI and hybrid cloud adoption strategies.\nCreated reference architectures for HA database clusters and disaster recovery across regions.\nDelivered pre-sales technical workshops and proof-of-concept implementations.",
                    ],
                    [
                        'company' => 'Telkom SA',
                        'position' => 'Systems Analyst',
                        'location' => 'Centurion, South Africa',
                        'start_date' => '2013-02',
                        'end_date' => '2016-08',
                        'current' => false,
                        'description' => "Gathered requirements and produced technical specifications for billing and provisioning systems.\nCoordinated UAT cycles and production cutovers with operations teams.\nDocumented integration flows between OSS/BSS platforms.",
                    ],
                ],
                'education' => [
                    [
                        'institution' => 'University of Cape Town',
                        'degree' => 'BSc Honours in Computer Science',
                        'field' => 'Computer Science',
                        'start_date' => '2011',
                        'end_date' => '2012',
                        'description' => 'Research focus on service-oriented architectures and enterprise integration patterns.',
                    ],
                    [
                        'institution' => 'Rhodes University',
                        'degree' => 'BSc Computer Science',
                        'field' => 'Computer Science',
                        'start_date' => '2008',
                        'end_date' => '2011',
                        'description' => 'Dean\'s merit list. Active in programming competitions and open-source contributions.',
                    ],
                ],
                'skills' => [
                    ['name' => 'Solution Architecture', 'level' => 'Expert'],
                    ['name' => 'AWS / Azure', 'level' => 'Expert'],
                    ['name' => 'Microservices', 'level' => 'Expert'],
                    ['name' => 'Enterprise Integration', 'level' => 'Expert'],
                    ['name' => 'TOGAF', 'level' => 'Advanced'],
                    ['name' => 'Security Architecture', 'level' => 'Advanced'],
                    ['name' => 'Kubernetes', 'level' => 'Advanced'],
                    ['name' => 'Terraform', 'level' => 'Advanced'],
                    ['name' => 'Technical Documentation', 'level' => 'Expert'],
                    ['name' => 'Stakeholder Workshops', 'level' => 'Expert'],
                ],
                'languages' => [
                    ['name' => 'English', 'proficiency' => 'Native'],
                    ['name' => 'Afrikaans', 'proficiency' => 'Fluent'],
                    ['name' => 'French', 'proficiency' => 'Intermediate'],
                ],
                'projects' => [
                    [
                        'name' => 'Open Banking API Gateway',
                        'url' => '',
                        'start_date' => '2022-01',
                        'end_date' => '2023-09',
                        'description' => 'Designed PSD2-inspired open banking gateway with OAuth 2.0, consent management, and rate limiting. Serves 2M+ API calls monthly with 99.99% availability SLA.',
                    ],
                    [
                        'name' => 'Insurance Claims Modernisation',
                        'url' => '',
                        'start_date' => '2018-03',
                        'end_date' => '2019-11',
                        'description' => 'Replaced paper-based claims workflow with a cloud-native platform using serverless functions and document OCR. Reduced average claim processing time from 14 days to 4 days.',
                    ],
                ],
                'certifications' => [
                    [
                        'name' => 'AWS Certified Solutions Architect – Professional',
                        'issuer' => 'Amazon Web Services',
                        'date' => '2023-04',
                        'url' => 'aws.amazon.com/certification',
                    ],
                    [
                        'name' => 'TOGAF 9 Certified',
                        'issuer' => 'The Open Group',
                        'date' => '2019-10',
                        'url' => 'opengroup.org',
                    ],
                    [
                        'name' => 'Certified Information Systems Security Professional (CISSP)',
                        'issuer' => 'ISC2',
                        'date' => '2021-05',
                        'url' => 'isc2.org',
                    ],
                ],
            ],
        ];
    }

    /**
     * @return array<string, string>
     */
    protected static function personalDetails(string $jobTitle, string $summary): array
    {
        return [
            'full_name' => 'John Doe',
            'job_title' => $jobTitle,
            'email' => 'john@example.com',
            'phone' => '+27 82 555 0142',
            'location' => 'Cape Town, Western Cape, South Africa',
            'website' => 'johndoe.co.za',
            'linkedin' => 'linkedin.com/in/johndoe',
            'summary' => $summary,
            'photo_preview' => '',
        ];
    }
}
