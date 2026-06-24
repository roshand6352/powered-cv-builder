<?php

namespace Database\Seeders;

use App\Models\CvTemplate;
use App\Support\Cv\CvTemplateLayout;
use Illuminate\Database\Seeder;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $this->seedTemplates(config('cv-templates.templates', []));
        $this->seedTemplates(config('cv-templates.dompdf_templates', []), dompdf: true);

        if (config('cv-templates.templates', []) === [] && config('cv-templates.dompdf_templates', []) === []) {
            $layout = CvTemplateLayout::defaults();

            CvTemplate::query()->updateOrCreate(
                ['slug' => 'classic'],
                [
                    'name' => 'Classic',
                    'description' => 'Default admin-configurable CV template.',
                    'layout_json' => $layout,
                    'status' => 1,
                ],
            );
        }
    }

    /**
     * @param  array<string, array<string, mixed>>  $variants
     */
    protected function seedTemplates(array $variants, bool $dompdf = false): void
    {
        foreach ($variants as $slug => $definition) {
            $settings = array_replace_recursive(
                CvTemplateLayout::defaultSettings(),
                $definition['settings'] ?? [],
            );

            if ($dompdf && ! empty($definition['pdf_blade'])) {
                $settings['pdf_blade'] = $definition['pdf_blade'];
                $settings['pdf_engine'] = 'dompdf';
            }

            $layout = CvTemplateLayout::normalize([
                'layout' => $definition['layout'] ?? $slug,
                'settings' => $settings,
            ]);

            CvTemplate::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'layout_json' => $layout,
                    'status' => 1,
                ],
            );
        }
    }
}
