<?php

namespace App\Services\Cv\Templates;

use App\Support\Cv\CvSectionComponentRegistry;
use App\Services\BaseService;
use Illuminate\Support\Facades\View;

class CvJsonRenderer extends BaseService
{
    /**
     * @param  array<string, mixed>  $section
     * @param  array<string, mixed>  $settings
     */
    public function renderSection(array $section, array $settings = []): string
    {
        $view = CvSectionComponentRegistry::viewName($section['key'] ?? '');

        if (! View::exists($view)) {
            $view = 'web.components.cv.render.fallback';
        }

        return view($view, [
            'section' => $section,
            'settings' => $settings,
        ])->render();
    }

    /**
     * @param  list<array<string, mixed>>  $sections
     * @param  array<string, mixed>  $settings
     */
    public function renderSections(array $sections, array $settings = []): string
    {
        $html = '';

        foreach ($sections as $section) {
            $html .= $this->renderSection($section, $settings);
        }

        return $html;
    }
}
