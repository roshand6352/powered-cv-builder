<?php

namespace App\Enums;

enum AiFeature: string
{
    case GenerateSummary = 'generate_summary';
    case ImproveExperience = 'improve_experience';
    case ImproveSkills = 'improve_skills';
    case GrammarFix = 'grammar_fix';
    case CoverLetterGeneration = 'cover_letter_generation';

    public function label(): string
    {
        return match ($this) {
            self::GenerateSummary => 'Generate Summary',
            self::ImproveExperience => 'Improve Experience',
            self::ImproveSkills => 'Improve Skills',
            self::GrammarFix => 'Grammar Fix',
            self::CoverLetterGeneration => 'Cover Letter Generation',
        };
    }

    public function creditCost(): int
    {
        return (int) config("ai.credit_costs.{$this->value}", 1);
    }

    public function isQueued(): bool
    {
        return in_array($this->value, config('ai.queued_features', []), true);
    }
}
