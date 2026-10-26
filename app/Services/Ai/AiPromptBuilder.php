<?php

namespace App\Services\Ai;

use App\Enums\AiFeature;
use App\Services\BaseService;
use Illuminate\Support\Str;

class AiPromptBuilder extends BaseService
{
    /**
     * @return list<array{role: string, content: string}>
     */
    public function build(AiFeature $feature, array $payload): array
    {
        return match ($feature) {
            AiFeature::GenerateSummary => $this->generateSummary($payload),
            AiFeature::ImproveExperience => $this->improveExperience($payload),
            AiFeature::ImproveSkills => $this->improveSkills($payload),
            AiFeature::GrammarFix => $this->grammarFix($payload),
            AiFeature::CoverLetterGeneration => $this->coverLetter($payload),
        };
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<array{role: string, content: string}>
     */
    public function buildJobMatch(array $payload): array
    {
        $lines = [
            'Compare the candidate profile against the job description and return JSON only.',
            '',
            'Required JSON shape:',
            '{',
            '  "score": 0,',
            '  "matched_keywords": [],',
            '  "missing_keywords": [],',
            '  "strengths": [],',
            '  "improvements": [],',
            '  "summary": ""',
            '}',
            '',
            'Job Description:',
            (string) ($payload['job_description'] ?? ''),
            '',
            'Selected CV JSON:',
            json_encode($payload['cv_json'] ?? [], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
        ];

        if (! empty($payload['cover_letter'])) {
            $lines[] = '';
            $lines[] = 'Selected Cover Letter:';
            $lines[] = json_encode($payload['cover_letter'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}';
        }

        return [
            $this->systemMessage('Return valid JSON only.'),
            $this->userMessage(implode("\n", $lines)),
        ];
    }

    /**
     * @param  array<string, mixed>  $payload
     * @return list<array{role: string, content: string}>
     */
    public function buildLinkedinPdfImport(array $payload): array
    {
        $cvSchema = [
            'json_data' => [
                'schema_version' => config('cv.schema_version'),
                'template' => config('cv.schema.template'),
                'personal_details' => config('cv.schema.personal_details'),
                'experience' => [config('cv.schema.experience')],
                'education' => [config('cv.schema.education')],
                'skills' => [config('cv.schema.skills')],
                'languages' => [config('cv.schema.languages')],
                'projects' => [config('cv.schema.projects')],
                'certifications' => [config('cv.schema.certifications')],
                'references' => [],
                'custom_sections' => [],
                'meta' => config('cv.defaults.meta'),
            ],
        ];

        $lines = [
            'Read the LinkedIn profile PDF text and extract only the information you can confidently identify.',
            'Return valid JSON only. Do not include markdown, notes, or explanations.',
            'Match the CV builder JSON structure exactly.',
            'Use empty strings or empty arrays when the LinkedIn PDF does not contain a value.',
            'Do not invent references, custom sections, profile photos, URLs, or dates that are not supported by the source text.',
            'Map the LinkedIn summary/about content into personal_details.summary.',
            'Map contact details into personal_details fields when available: email, phone, location, website, linkedin.',
            'Each experience item must use: company, position, location, start_date, end_date, current, description.',
            'Each education item must use: institution, degree, field, start_date, end_date, in_progress, description.',
            'Each skill item must use: name, level.',
            'Each language item must use: name, proficiency.',
            'Each project item must use: name, url, start_date, end_date, in_progress, description.',
            'Each certification item must use: name, issuer, date, url.',
            '',
            'Required JSON shape:',
            json_encode($cvSchema, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?: '{}',
            '',
            'LinkedIn PDF text:',
            (string) ($payload['extracted_text'] ?? ''),
        ];

        return [
            $this->systemMessage('Return valid JSON only.'),
            $this->userMessage(implode("\n", $lines)),
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function generateSummary(array $payload): array
    {
        $summary = trim(strip_tags((string) ($payload['personal_details']['summary'] ?? $payload['summary'] ?? '')));

        return [
            $this->systemMessage(),
            $this->userMessage(
                config('ai.prompts.generate_summary.instruction')."\n\nSummary to improve:\n".$summary
            ),
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function improveExperience(array $payload): array
    {
        $lines = [
            'Company: '.($payload['company'] ?? 'N/A'),
            'Position: '.($payload['position'] ?? 'N/A'),
            'Location: '.($payload['location'] ?? 'N/A'),
            'Dates: '.($payload['start_date'] ?? '').' - '.($payload['end_date'] ?? 'Present'),
            '',
            'Current description:',
            (string) ($payload['description'] ?? ''),
        ];

        return [
            $this->systemMessage(),
            $this->userMessage(
                config('ai.prompts.improve_experience.instruction')."\n\n".implode("\n", $lines)
            ),
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function improveSkills(array $payload): array
    {
        $skills = collect($payload['skills'] ?? [])
            ->map(fn (array $skill) => '- '.($skill['name'] ?? '').' ('.($skill['level'] ?? 'unspecified').')')
            ->implode("\n");

        return [
            $this->systemMessage('Return valid JSON only.'),
            $this->userMessage(
                config('ai.prompts.improve_skills.instruction')."\n\nCurrent skills:\n".$skills
            ),
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function grammarFix(array $payload): array
    {
        return [
            $this->systemMessage(),
            $this->userMessage(
                config('ai.prompts.grammar_fix.instruction')."\n\nText:\n".($payload['text'] ?? '')
            ),
        ];
    }

    /**
     * @return list<array{role: string, content: string}>
     */
    protected function coverLetter(array $payload): array
    {
        $lines = [
            'Target role: '.($payload['job_title'] ?? 'N/A'),
            'Company: '.($payload['company'] ?? 'N/A'),
            'Tone: '.($payload['tone'] ?? 'professional'),
            '',
            'Job description:',
            (string) ($payload['job_description'] ?? ''),
            '',
            'Candidate profile:',
            $this->formatCandidateContext($payload),
        ];

        return [
            $this->systemMessage('Return valid JSON only.'),
            $this->userMessage(
                config('ai.prompts.cover_letter_generation.instruction')."\n\n".implode("\n", $lines)
            ),
        ];
    }

    protected function formatCandidateContext(array $payload): string
    {
        $personal = $payload['personal_details'] ?? [];
        $lines = [
            'Name: '.($personal['full_name'] ?? $payload['full_name'] ?? 'N/A'),
            'Job title: '.($personal['job_title'] ?? $payload['job_title'] ?? 'N/A'),
            'Location: '.($personal['location'] ?? $payload['location'] ?? 'N/A'),
        ];

        if ($summary = ($personal['summary'] ?? $payload['existing_summary'] ?? null)) {
            $lines[] = '';
            $lines[] = 'Existing summary:';
            $lines[] = $summary;
        }

        if ($experience = $payload['experience'] ?? []) {
            $lines[] = '';
            $lines[] = 'Experience highlights:';
            foreach (array_slice($experience, 0, 5) as $item) {
                $lines[] = '- '.($item['position'] ?? '').' at '.($item['company'] ?? '');
                if (! empty($item['description'])) {
                    $lines[] = '  '.Str::limit((string) $item['description'], 200);
                }
            }
        }

        if ($education = $payload['education'] ?? []) {
            $lines[] = '';
            $lines[] = 'Education:';
            foreach (array_slice($education, 0, 4) as $item) {
                $degree = trim(($item['degree'] ?? '').' '.($item['field'] ?? ''));
                $lines[] = '- '.($degree !== '' ? $degree : 'Studies').' at '.($item['institution'] ?? '');
            }
        }

        if ($skills = $payload['skills'] ?? []) {
            $lines[] = '';
            $lines[] = 'Skills: '.collect($skills)->pluck('name')->filter()->implode(', ');
        }

        return implode("\n", $lines);
    }

    /**
     * @return array{role: string, content: string}
     */
    protected function systemMessage(?string $suffix = null): array
    {
        $content = config('ai.prompts.system');

        if ($suffix) {
            $content .= ' '.$suffix;
        }

        return ['role' => 'system', 'content' => $content];
    }

    /**
     * @return array{role: string, content: string}
     */
    protected function userMessage(string $content): array
    {
        return ['role' => 'user', 'content' => $content];
    }
}
