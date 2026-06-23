<?php

namespace App\DTOs\Cv;

use App\Support\Cv\CvDocumentMigrator;
use Illuminate\Contracts\Support\Arrayable;

class CvDocumentData implements Arrayable
{
    /**
     * @param  list<CvExperienceItemData>  $experience
     * @param  list<CvEducationItemData>  $education
     * @param  list<CvSkillItemData>  $skills
     * @param  list<CvLanguageItemData>  $languages
     * @param  list<CvProjectItemData>  $projects
     * @param  list<CvCertificationItemData>  $certifications
     * @param  list<CvReferenceItemData>  $references
     * @param  list<CvCustomSectionData>  $customSections
     */
    public function __construct(
        public string $schemaVersion,
        public CvTemplateContextData $template,
        public CvPersonalDetailsData $personalDetails,
        public array $experience = [],
        public array $education = [],
        public array $skills = [],
        public array $languages = [],
        public array $projects = [],
        public array $certifications = [],
        public array $references = [],
        public array $customSections = [],
        public CvDocumentMetaData $meta = new CvDocumentMetaData,
    ) {}

    public static function empty(): self
    {
        return self::fromArray([]);
    }

    /**
     * @param  array<string, mixed>|null  $data
     */
    public static function fromArray(?array $data): self
    {
        $data = CvDocumentMigrator::migrate($data ?? []);

        return new self(
            schemaVersion: (string) ($data['schema_version'] ?? CvDocumentMigrator::CURRENT_SCHEMA),
            template: CvTemplateContextData::fromArray($data['template'] ?? []),
            personalDetails: CvPersonalDetailsData::fromArray($data['personal_details'] ?? []),
            experience: self::mapItems($data['experience'] ?? [], CvExperienceItemData::class),
            education: self::mapItems($data['education'] ?? [], CvEducationItemData::class),
            skills: self::mapItems($data['skills'] ?? [], CvSkillItemData::class),
            languages: self::mapItems($data['languages'] ?? [], CvLanguageItemData::class),
            projects: self::mapItems($data['projects'] ?? [], CvProjectItemData::class),
            certifications: self::mapItems($data['certifications'] ?? [], CvCertificationItemData::class),
            references: self::mapItems($data['references'] ?? [], CvReferenceItemData::class),
            customSections: self::mapItems($data['custom_sections'] ?? [], CvCustomSectionData::class),
            meta: CvDocumentMetaData::fromArray($data['meta'] ?? []),
        );
    }

    /**
     * @param  list<mixed>  $items
     * @param  class-string  $class
     * @return list<object>
     */
    protected static function mapItems(array $items, string $class): array
    {
        return collect($items)
            ->filter(fn ($item) => is_array($item))
            ->map(fn (array $item) => $class::fromArray($item))
            ->values()
            ->all();
    }

    public function toArray(): array
    {
        return [
            'schema_version' => $this->schemaVersion,
            'template' => $this->template->toArray(),
            'personal_details' => $this->personalDetails->toArray(),
            'experience' => array_map(fn (CvExperienceItemData $item) => $item->toArray(), $this->experience),
            'education' => array_map(fn (CvEducationItemData $item) => $item->toArray(), $this->education),
            'skills' => array_map(fn (CvSkillItemData $item) => $item->toArray(), $this->skills),
            'languages' => array_map(fn (CvLanguageItemData $item) => $item->toArray(), $this->languages),
            'projects' => array_map(fn (CvProjectItemData $item) => $item->toArray(), $this->projects),
            'certifications' => array_map(fn (CvCertificationItemData $item) => $item->toArray(), $this->certifications),
            'references' => array_map(fn (CvReferenceItemData $item) => $item->toArray(), $this->references),
            'custom_sections' => array_map(fn (CvCustomSectionData $section) => $section->toArray(), $this->customSections),
            'meta' => $this->meta->toArray(),
        ];
    }

    /**
     * @return list<string>
     */
    public function orderedEnabledSections(): array
    {
        return $this->meta->orderedEnabledSections();
    }
}
