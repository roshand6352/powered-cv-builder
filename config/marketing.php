<?php

return [

    'brand' => [
        'name' => 'ApplyDocs ZA',
        'short' => 'ApplyDocs',
        'logo' => 'images/LOGO-02.png',
        'footer_logo' => 'images/LOGO-19.png',
        'favicon' => 'LOGO-10.png',
    ],

    'stats' => [
        'cvs_built' => '50,000',
        'trusted_users' => '10,000',
    ],

    'nav' => [
        ['label' => 'Tools', 'href' => '/#tools'],
        ['label' => 'How it works', 'href' => '/#how'],
        ['label' => 'Pricing', 'href' => '/#pricing'],
        ['label' => 'FAQ', 'href' => '/#faq'],
    ],

    'hero_carousel' => [
        [
            'step' => 'Step 1',
            'title' => 'Answer guided questions',
            'description' => 'Tell us about your experience, skills and education.',
        ],
        [
            'step' => 'Step 2',
            'title' => 'Watch your CV build live',
            'description' => 'Your CV takes shape in real time as you go.',
        ],
        [
            'step' => 'Step 3',
            'title' => 'Refine with AI (Pro)',
            'description' => 'Polish bullet points into professional language.',
        ],
        [
            'step' => 'Result',
            'title' => 'Download & apply',
            'description' => 'ATS-ready PDF ready for SA employers.',
        ],
    ],

    'social_proof' => [
        ['value' => '50,000+', 'label' => 'CVs built'],
        ['value' => 'FREE', 'label' => 'to get started'],
        ['value' => '<10 min', 'label' => 'to build your CV'],
        ['value' => 'ATS ready', 'label' => 'optimised templates'],
    ],

    'pain_cards' => [
        [
            'title' => 'Beat the bots',
            'description' => 'Most employers, recruitment agencies and job platforms use ATS software to screen your application before anyone even sees it. Our templates are formatted to ensure yours is screened accurately.',
            'icon' => 'cpu',
        ],
        [
            'title' => 'Present yourself as the professional you are',
            'description' => 'No more staring at a blank page wondering how to make the best impression. Follow our questions and we\'ll do the hard work for you. No more underselling yourself!',
            'icon' => 'user',
        ],
        [
            'title' => 'Accessible and affordable',
            'description' => 'Who has R 800 - R 2,500 to spend on professional CV services in this economy? We\'re affordable because we want to see you win without burdening you financially.',
            'icon' => 'wallet',
        ],
    ],

    'tools' => [
        [
            'featured' => true,
            'badge' => 'Start here',
            'title' => 'CV Builder',
            'description' => 'Follow guided prompts to build your CV section by section. No blank page. No guessing. Just answer questions and your CV comes together automatically.',
            'features' => [
                ['text' => 'ATS-optimised templates', 'pro' => false],
                ['text' => 'Step-by-step guided prompts', 'pro' => false],
                ['text' => 'Download free (with watermark)', 'pro' => false],
                ['text' => 'AI-assisted paragraph writing', 'pro' => true],
                ['text' => 'Watermark-free download', 'pro' => true],
            ],
            'cta' => 'Build My CV Free',
            'href' => 'register',
        ],
        [
            'featured' => false,
            'badge' => null,
            'title' => 'Cover Letter Generator',
            'description' => 'Paste the job description, answer a few prompts about your experience, and generate a tailored cover letter that matches the role. Takes under 3 minutes.',
            'features' => [
                ['text' => 'Role-specific, not generic', 'pro' => false],
                ['text' => 'Professional & scannable format', 'pro' => false],
                ['text' => 'Download free (with watermark)', 'pro' => false],
                ['text' => 'AI tone and language polish', 'pro' => true],
                ['text' => 'Watermark-free download', 'pro' => true],
            ],
            'cta' => 'Generate Cover Letter',
            'href' => 'cover-letter',
        ],
    ],

    'how_it_works_cv' => [
        'label' => 'CV Builder',
        'title' => 'From blank page to downloaded CV in under 10 minutes',
        'subtitle' => 'No design skills needed. No CV-writing experience required. Just follow the steps.',
        'steps' => [
            ['title' => 'Create your free account', 'description' => 'Sign up in 30 seconds. No credit card and no commitment required. Just your email and a password.'],
            ['title' => 'Follow the guided prompts', 'description' => 'Answer simple questions about your experience, skills and education. The builder structures it all for you.'],
            ['title' => 'Preview and refine', 'description' => 'Watch your CV take shape in real time. Upgrade to Pro to use AI to improve any section instantly.'],
            ['title' => 'Download and apply', 'description' => 'Download free with watermark or go Pro for a clean, watermark-free PDF ready for any job application.'],
        ],
        'cta' => 'Build My CV Free',
        'href' => 'register',
    ],

    'how_it_works_cover_letter' => [
        'label' => 'Cover Letter Generator',
        'title' => 'From job listing to tailored cover letter in under 3 minutes',
        'subtitle' => 'No writing experience needed. Paste the job, answer a few prompts and you are done.',
        'steps' => [
            ['title' => 'Log in or sign up free', 'description' => 'Already built your CV with ApplyDocs ZA? Jump straight in. New here? Takes 30 seconds to get started.'],
            ['title' => 'Paste the job description', 'description' => 'Copy the job listing you are applying for. ApplyDocs ZA reads it and tailors your letter to that specific role.'],
            ['title' => 'Answer a few quick prompts', 'description' => 'Tell us why you are a good fit. Upgrade to Pro and AI polishes your answers into confident, professional language.'],
            ['title' => 'Download and attach', 'description' => 'Your tailored cover letter is ready in under 3 minutes. Download free with watermark or go Pro for a clean PDF.'],
        ],
        'cta' => 'Generate Cover Letter',
        'href' => 'cover-letter',
    ],

    'differentiators' => [
        [
            'title' => 'ATS-compliant',
            'description' => 'Tested against the filters used by employers, recruitment agencies and job platforms across South Africa.',
        ],
        [
            'title' => 'Genuinely free to start',
            'description' => 'Build and download free. No trial period. No card details upfront. No surprises — just results.',
        ],
        [
            'title' => 'AI that actually helps',
            'description' => 'Pro AI rewrites your bullet points into polished, professional language — without fabricating a single word.',
        ],
        [
            'title' => 'SA market-specific',
            'description' => 'Built around SA CV norms by a team that understands the local employment process inside and out.',
        ],
    ],

    'pricing' => [
        'free' => [
            'name' => 'FREE',
            'price' => 'R 0',
            'period' => 'forever free',
            'features' => [
                'CV Builder with guided prompts',
                'Cover Letter Generator',
                '3 ATS-optimised templates',
                'PDF download',
                'Edit and save your CV anytime',
            ],
            'excluded' => [
                'AI writing assistance (Pro feature)',
                'Watermark-free PDF (Pro feature)',
            ],
        ],
        'pro' => [
            'name' => 'PRO',
            'badge' => 'Most popular',
            'price' => 'Starting from R 80',
            'period' => 'once-off · no subscription',
            'features' => [
                'Watermark-free PDF download',
                'AI paragraph rewriter - turn rough notes into polished sentences',
                'AI cover letter role-specific personalisation',
                'All premium templates',
            ],
        ],
    ],

    'testimonials' => [
        [
            'quote' => 'I had been applying for months with my old CV and heard nothing back. Within two weeks of using ApplyDocs ZA, I had three interview callbacks. The AI feature is worth every rand.',
            'name' => 'Thandi M.',
            'role' => 'Admin assistant, Johannesburg',
            'plan' => 'Pro user',
        ],
        [
            'quote' => 'As a matric graduate I had no idea what to put in a CV. The prompts walked me through everything and it just built itself. I used the free version and it was more than enough to get started.',
            'name' => 'Lungelo N.',
            'role' => 'School leaver, Durban',
            'plan' => 'Free user',
        ],
        [
            'quote' => 'I upgraded to Pro to remove the watermark and the AI turned my bullet points into something I would never have written myself. Landed a role in construction management shortly after.',
            'name' => 'Petrus W.',
            'role' => 'Site foreman, Pretoria',
            'plan' => 'Pro user',
        ],
    ],

    'faqs' => [
        [
            'question' => 'Is the CV Builder really free? What is the catch?',
            'answer' => 'There is no catch. The CV Builder and Cover Letter Generator are completely free to use. You can build, edit and download your CV at no cost. Free downloads include an ApplyDocs ZA watermark. If you want a clean, watermark-free PDF and access to AI writing features, you can upgrade to Pro - starting from R 80 once-off.',
        ],
        [
            'question' => 'What does the watermark look like on my free download?',
            'answer' => 'The watermark appears as a faint diagonal "ApplyDocs ZA" stamp across your CV document. It is visible but does not obscure your content. Upgrading to Pro removes it entirely, giving you a clean, professional PDF ready to submit to any employer.',
        ],
        [
            'question' => 'What does the AI actually do — will it make my CV sound fake?',
            'answer' => 'The AI improves how you express your experience, not what you have done. Your facts, your roles, your voice - it just helps you phrase things more professionally. You stay in control of all content. The AI rewrites clunky bullet points into cleaner, stronger sentences without fabricating anything.',
        ],
        [
            'question' => 'Will my CV pass ATS filters used by employers and recruitment agencies?',
            'answer' => 'Yes. All ApplyDocs ZA templates are built to be ATS-compliant - formatted to be parsed correctly by the automated screening software used by employers, recruitment agencies and job platforms. We avoid common formatting pitfalls like tables, graphics and unusual fonts that cause ATS rejection.',
        ],
        [
            'question' => 'Is my personal information safe? Is ApplyDocs ZA POPIA compliant?',
            'answer' => 'Yes. ApplyDocs ZA is fully POPIA compliant. Your personal information is stored securely and is never sold or shared with third parties. You can request deletion of your data at any time. Our full POPIA notice and privacy policy are available in the footer.',
        ],
        [
            'question' => 'What payment methods are available?',
            'answer' => 'We accept Card, EFT, Capitec Pay and many more payment options. All transactions are processed securely in South African Rand. There are no dollar conversions or hidden fees. Pro access is available as a once-off payment - choose 24 hours, weekly, monthly or lifetime access.',
        ],
        [
            'question' => 'Can I edit my CV after I have already downloaded it?',
            'answer' => 'Yes. Your CV is saved to your account and you can return to edit it at any time - even after downloading. Free users can edit and re-download with the watermark. Pro users can always download a clean, watermark-free version of their latest saved CV.',
        ],
    ],

    'footer' => [
        'description' => 'A free CV builder and cover letter generator built for South African job seekers. ATS-compliant, guided and genuinely affordable.',
        'tools' => [
            ['label' => 'CV Builder', 'href' => '/register'],
            ['label' => 'Cover Letter Generator', 'href' => '/dashboard/cover-letters'],
            ['label' => 'Templates', 'href' => '/templates'],
        ],
        'company' => [
            ['label' => 'About us', 'href' => '/about'],
            ['label' => 'Blog', 'href' => '/about'],
            ['label' => 'Contact', 'href' => '/contact'],
        ],
        'legal' => [
            ['label' => 'Privacy policy', 'href' => '/privacy-policy'],
            ['label' => 'POPIA notice', 'href' => '/privacy-policy'],
            ['label' => 'Terms of use', 'href' => '/terms'],
        ],
        'badges' => ['POPIA compliant', 'SSL secured', 'Made in ZA'],
    ],

];
