<?php



namespace App\Support\Cv;



class CvSectionIcons

{

    /**

     * Font Awesome 6 Free icon classes for CV section headings.

     *

     * @return array<string, string>

     */

    public static function symbols(): array

    {

        return [

            'header' => 'fa-solid fa-id-card',

            'about' => 'fa-solid fa-user',

            'experience' => 'fa-solid fa-briefcase',

            'education' => 'fa-solid fa-graduation-cap',

            'skills' => 'fa-solid fa-star',

            'projects' => 'fa-solid fa-diagram-project',

            'languages' => 'fa-solid fa-language',

            'certificates' => 'fa-solid fa-award',

            'references' => 'fa-solid fa-user-group',

            'custom_sections' => 'fa-solid fa-folder-open',

            'awards' => 'fa-solid fa-trophy',

            'interests' => 'fa-solid fa-heart',

        ];

    }



    public static function symbol(string $sectionKey): string

    {

        $key = CvTemplateLayout::normalizeSectionKey($sectionKey);



        return static::symbols()[$key] ?? 'fa-solid fa-circle';

    }



    /**

     * @return array<string, string>

     */

    public static function contactIcons(): array

    {

        return [

            'email' => 'fa-solid fa-envelope',

            'phone' => 'fa-solid fa-phone',

            'location' => 'fa-solid fa-location-dot',

            'website' => 'fa-solid fa-globe',

            'linkedin' => 'fa-brands fa-linkedin',

            'github' => 'fa-brands fa-github',

        ];

    }



    public static function contactIcon(string $field): string

    {

        return static::contactIcons()[$field] ?? 'fa-solid fa-circle';

    }



    /**

     * @param  array<string, mixed>  $settings

     */

    public static function enabled(array $settings): bool

    {

        return (bool) ($settings['icons']['enabled'] ?? true);

    }

}

