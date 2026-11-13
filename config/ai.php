<?php

return [

    'provider' => env('AI_PROVIDER', 'openai'),

    /*
    |--------------------------------------------------------------------------
    | Registered AI providers
    |--------------------------------------------------------------------------
    |
    | Map provider keys to client classes. AiProviderManager resolves the active
    | provider from AI_PROVIDER and delegates chat completions accordingly.
    |
    */
    'providers' => [
        'openai' => \App\Services\Ai\OpenAiClient::class,
        'groq' => \App\Services\Ai\GroqClient::class,
    ],

    'queue' => env('AI_QUEUE', 'default'),

    /*
    |--------------------------------------------------------------------------
    | LinkedIn PDF import
    |--------------------------------------------------------------------------
    |
    | A complete CV as JSON needs far more output tokens than the default chat
    | limit, so the import uses its own, larger budget and a low temperature to
    | keep the structured output stable.
    |
    */
    'linkedin_import' => [
        'max_tokens' => (int) env('LINKEDIN_IMPORT_MAX_TOKENS', 8000),
        'temperature' => (float) env('LINKEDIN_IMPORT_TEMPERATURE', 0.2),
        'timeout' => (int) env('LINKEDIN_IMPORT_AI_TIMEOUT', 120),
    ],

    'default_credits_limit' => (int) env('AI_DEFAULT_CREDITS_LIMIT', 10),

    'openai' => [
        'api_key' => env('OPENAI_API_KEY'),
        'organization' => env('OPENAI_ORGANIZATION'),
        'base_url' => env('OPENAI_BASE_URL', 'https://api.openai.com/v1'),
        'model' => env('OPENAI_MODEL', 'gpt-4o-mini'),
        'max_tokens' => (int) env('OPENAI_MAX_TOKENS', 1500),
        'temperature' => (float) env('OPENAI_TEMPERATURE', 0.7),
        'timeout' => (int) env('OPENAI_TIMEOUT', 60),
    ],

    'groq' => [
        'api_key' => env('GROQ_API_KEY'),
        'base_url' => env('GROQ_BASE_URL', 'https://api.groq.com/openai/v1'),
        'model' => env('GROQ_MODEL', 'llama-3.3-70b-versatile'),
        'max_tokens' => (int) env('GROQ_MAX_TOKENS', 1500),
        'temperature' => (float) env('GROQ_TEMPERATURE', 0.7),
        'timeout' => (int) env('GROQ_TIMEOUT', 60),
    ],

    'credit_costs' => [
        'generate_summary' => 1,
        'improve_experience' => 1,
        'improve_skills' => 1,
        'grammar_fix' => 1,
        'cover_letter_generation' => 2,
    ],

    'queued_features' => [
        'cover_letter_generation',
    ],

    'prompts' => [
        'system' => 'You are an expert CV and career writing assistant. Improve only the text the user provides. Never invent employers, dates, metrics, skills, qualifications, credentials, or achievements. Do not add facts that are not already present or clearly implied in the supplied text. Return only the requested output with no preamble unless JSON is requested.',

        'generate_summary' => [
            'instruction' => 'Improve the professional summary below so it reads more polished and professional. Keep the same facts and meaning. Do not add employers, dates, metrics, or achievements that are not already stated. Return only the improved summary text.',
        ],

        'improve_experience' => [
            'instruction' => 'Improve the wording, grammar, clarity, and professionalism of the work experience description below. The description is the only input: the job title, company, location, and dates are shown in separate fields, so never repeat, add, or mention them. Keep every fact truthful and grounded in the text provided; do not invent responsibilities, metrics, employers, dates, tools, or achievements. Keep the existing structure: if the text is a list, return a list with one item per line, each starting with "- "; otherwise return short paragraphs, one per line. Do not use bold, headings, or other Markdown formatting, and do not add a title or introduction. Return only the improved description.',
        ],

        'improve_skills' => [
            'instruction' => 'Review and improve the skills list. Return valid JSON only: {"skills":[{"name":"...","level":"Beginner|Intermediate|Advanced|Expert"}]}. Merge duplicates, use industry-standard names, and assign realistic levels. Do not add skills that are not already listed.',
        ],

        'grammar_fix' => [
            'instruction' => 'Improve the text so it sounds more professional. Fix grammar, spelling, punctuation, and clarity. Preserve the original meaning and facts — do not add new information, achievements, or details.',
        ],

        'cover_letter_generation' => [
            'instruction' => 'Write a tailored, professional cover letter using ONLY the candidate profile and job details provided. Return valid JSON only with this exact shape: {"subject":"...","greeting":"...","introduction":"...","body":"...","closing_paragraph":"...","signature":"..."}. "subject" is a concise subject line referencing the role. "greeting" is the salutation (e.g. "Dear Hiring Manager,"). "introduction" is an engaging opening paragraph. "body" is 1-2 paragraphs highlighting relevant experience, skills, and achievements (use real line breaks between paragraphs). "closing_paragraph" is a polite closing sign-off line (e.g. "Sincerely,"). "signature" is the candidate full name. Do not invent employers, dates, metrics, or credentials.',
        ],
    ],

];
