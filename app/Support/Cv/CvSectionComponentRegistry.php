<?php

namespace App\Support\Cv;

class CvSectionComponentRegistry
{
    /**
     * @return list<string>
     */
    public static function renderSectionKeys(): array
    {
        return [
            'header',
            'about',
            'experience',
            'education',
            'skills',
            'projects',
            'languages',
            'certificates',
            'references',
            'custom_sections',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function labels(): array
    {
        return [
            'header' => 'Header',
            'about' => 'About',
            'experience' => 'Experience',
            'education' => 'Education',
            'skills' => 'Skills',
            'projects' => 'Projects',
            'languages' => 'Languages',
            'certificates' => 'Certificates',
            'references' => 'References',
            'personal_details' => 'Header',
            'certifications' => 'Certificates',
        ];
    }

    /**
     * @return array<string, string>
     */
    public static function componentMap(): array
    {
        return [
            'header' => 'header',
            'personal_details' => 'header',
            'about' => 'about',
            'experience' => 'experience',
            'education' => 'education',
            'skills' => 'skills',
            'projects' => 'projects',
            'languages' => 'languages',
            'certificates' => 'certificates',
            'certifications' => 'certificates',
            'references' => 'references',
            'custom_sections' => 'custom_sections',
        ];
    }

    public static function componentFor(string $sectionKey): string
    {
        return static::componentMap()[$sectionKey] ?? 'fallback';
    }

    public static function viewName(string $sectionKey): string
    {
        return 'web.components.cv.render.'.static::componentFor($sectionKey);
    }

    /**
     * @return array<string, string>
     */
    public static function designerOptions(): array
    {
        return [
            'header' => 'Profile',
            'about' => 'Summary',
            'experience' => 'Experience',
            'education' => 'Education',
            'skills' => 'Skills',
            'projects' => 'Projects',
            'languages' => 'Languages',
            'certificates' => 'Certifications',
            'references' => 'References',
            'custom_sections' => 'Custom Sections',
        ];
    }
}
