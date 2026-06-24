<?php

namespace App\Support\Cv;

use Illuminate\Validation\Rule;

class CvDocumentValidationRules
{
    /**
     * @return array<string, mixed>
     */
    public static function rules(bool $strict = false): array
    {
        $sectionKeys = array_merge(array_keys(config('cv.sections')), ['about']);

        return [
            'json_data' => ['required', 'array'],
            'json_data.schema_version' => ['nullable', 'string', Rule::in([CvDocumentMigrator::CURRENT_SCHEMA])],
            'json_data.template' => ['nullable', 'array'],
            'json_data.template.id' => ['nullable', 'integer'],
            'json_data.template.slug' => ['nullable', 'string', 'max:255'],
            'json_data.template.key' => ['nullable', 'string', 'max:100'],
            'json_data.template.settings' => ['nullable', 'array'],
            ...self::personalDetailsRules($strict),
            ...self::experienceRules(),
            ...self::educationRules(),
            ...self::skillsRules(),
            ...self::languagesRules(),
            ...self::projectsRules(),
            ...self::certificationsRules(),
            ...self::referencesRules(),
            ...self::customSectionsRules(),
            'json_data.meta' => ['nullable', 'array'],
            'json_data.meta.section_order' => ['nullable', 'array'],
            'json_data.meta.section_order.*' => ['string', Rule::in($sectionKeys)],
            'json_data.meta.enabled_sections' => ['nullable', 'array'],
            'json_data.meta.enabled_sections.*' => ['string', Rule::in($sectionKeys)],
            'json_data.meta.template_bindings' => ['nullable', 'array'],
            'json_data.meta.last_edited_section' => ['nullable', 'string', Rule::in($sectionKeys)],
            'json_data.meta.locale' => ['nullable', 'string', 'max:10'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function personalDetailsRules(bool $strict): array
    {
        return [
            'json_data.personal_details' => ['nullable', 'array'],
            'json_data.personal_details.full_name' => [$strict ? 'required' : 'nullable', 'string', 'max:255'],
            'json_data.personal_details.job_title' => ['nullable', 'string', 'max:255'],
            'json_data.personal_details.email' => ['nullable', 'email', 'max:255'],
            'json_data.personal_details.phone' => ['nullable', 'string', 'max:50'],
            'json_data.personal_details.location' => ['nullable', 'string', 'max:255'],
            'json_data.personal_details.website' => ['nullable', 'string', 'max:255'],
            'json_data.personal_details.linkedin' => ['nullable', 'string', 'max:255'],
            'json_data.personal_details.summary' => ['nullable', 'string', 'max:5000'],
            'json_data.personal_details.photo_preview' => ['nullable', 'string', 'max:500000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function experienceRules(): array
    {
        return [
            'json_data.experience' => ['nullable', 'array'],
            'json_data.experience.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.experience.*.company' => ['nullable', 'string', 'max:255'],
            'json_data.experience.*.position' => ['nullable', 'string', 'max:255'],
            'json_data.experience.*.location' => ['nullable', 'string', 'max:255'],
            'json_data.experience.*.start_date' => ['nullable', 'string', 'max:50'],
            'json_data.experience.*.end_date' => ['nullable', 'string', 'max:50'],
            'json_data.experience.*.current' => ['nullable', 'boolean'],
            'json_data.experience.*.description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function educationRules(): array
    {
        return [
            'json_data.education' => ['nullable', 'array'],
            'json_data.education.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.education.*.institution' => ['nullable', 'string', 'max:255'],
            'json_data.education.*.degree' => ['nullable', 'string', 'max:255'],
            'json_data.education.*.field' => ['nullable', 'string', 'max:255'],
            'json_data.education.*.start_date' => ['nullable', 'string', 'max:50'],
            'json_data.education.*.end_date' => ['nullable', 'string', 'max:50'],
            'json_data.education.*.in_progress' => ['nullable', 'boolean'],
            'json_data.education.*.description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function skillsRules(): array
    {
        return [
            'json_data.skills' => ['nullable', 'array'],
            'json_data.skills.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.skills.*.name' => ['nullable', 'string', 'max:255'],
            'json_data.skills.*.level' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function languagesRules(): array
    {
        return [
            'json_data.languages' => ['nullable', 'array'],
            'json_data.languages.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.languages.*.name' => ['nullable', 'string', 'max:255'],
            'json_data.languages.*.proficiency' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function projectsRules(): array
    {
        return [
            'json_data.projects' => ['nullable', 'array'],
            'json_data.projects.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.projects.*.name' => ['nullable', 'string', 'max:255'],
            'json_data.projects.*.url' => ['nullable', 'string', 'max:255'],
            'json_data.projects.*.start_date' => ['nullable', 'string', 'max:50'],
            'json_data.projects.*.end_date' => ['nullable', 'string', 'max:50'],
            'json_data.projects.*.in_progress' => ['nullable', 'boolean'],
            'json_data.projects.*.description' => ['nullable', 'string', 'max:5000'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function certificationsRules(): array
    {
        return [
            'json_data.certifications' => ['nullable', 'array'],
            'json_data.certifications.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.certifications.*.name' => ['nullable', 'string', 'max:255'],
            'json_data.certifications.*.issuer' => ['nullable', 'string', 'max:255'],
            'json_data.certifications.*.date' => ['nullable', 'string', 'max:50'],
            'json_data.certifications.*.url' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function referencesRules(): array
    {
        return [
            'json_data.references' => ['nullable', 'array'],
            'json_data.references.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.references.*.name' => ['nullable', 'string', 'max:255'],
            'json_data.references.*.company' => ['nullable', 'string', 'max:255'],
            'json_data.references.*.email' => ['nullable', 'email', 'max:255'],
            'json_data.references.*.phone' => ['nullable', 'string', 'max:50'],
            'json_data.references.*.relationship' => ['nullable', 'string', 'max:255'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected static function customSectionsRules(): array
    {
        return [
            'json_data.custom_sections' => ['nullable', 'array'],
            'json_data.custom_sections.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.custom_sections.*.title' => ['nullable', 'string', 'max:255'],
            'json_data.custom_sections.*.order' => ['nullable', 'integer', 'min:0'],
            'json_data.custom_sections.*.items' => ['nullable', 'array'],
            'json_data.custom_sections.*.items.*.id' => ['nullable', 'string', 'max:36'],
            'json_data.custom_sections.*.items.*.title' => ['nullable', 'string', 'max:255'],
            'json_data.custom_sections.*.items.*.content' => ['nullable', 'string', 'max:5000'],
        ];
    }
}
