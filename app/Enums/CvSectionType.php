<?php

namespace App\Enums;

enum CvSectionType: string
{
    case PersonalDetails = 'personal_details';
    case Experience = 'experience';
    case Education = 'education';
    case Skills = 'skills';
    case Languages = 'languages';
    case Projects = 'projects';
    case References = 'references';
    case Certifications = 'certifications';
    case CustomSections = 'custom_sections';

    public function label(): string
    {
        return match ($this) {
            self::PersonalDetails => 'Personal Details',
            self::Experience => 'Experience',
            self::Education => 'Education',
            self::Skills => 'Skills',
            self::Languages => 'Languages',
            self::Projects => 'Projects',
            self::References => 'References',
            self::Certifications => 'Certifications',
            self::CustomSections => 'Custom Sections',
        };
    }

    public function isRepeatable(): bool
    {
        return $this !== self::PersonalDetails;
    }

    /**
     * @return list<string>
     */
    public static function orderedKeys(): array
    {
        return array_map(
            fn (self $type) => $type->value,
            self::cases(),
        );
    }
}
