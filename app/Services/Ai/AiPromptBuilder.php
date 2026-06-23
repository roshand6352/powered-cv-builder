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
