<?php

namespace Database\Seeders;

use App\Models\Cv;
use App\Models\CvTemplate;
use App\Support\Cv\CvTemplateLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CvTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $variants = config('cv-templates.templates', []);
        $validSlugs = array_keys($variants);
        $classicTemplateId = null;

        foreach ($variants as $slug => $definition) {
            $layout = CvTemplateLayout::normalize([
                'layout' => $definition['layout'] ?? $slug,
                'settings' => array_replace_recursive(
                    CvTemplateLayout::defaultSettings(),
                    $definition['settings'] ?? [],
                ),
            ]);

            $template = CvTemplate::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'description' => $definition['description'] ?? null,
                    'layout_json' => $layout,
                    'status' => 1,
                ],
            );

            if ($slug === 'classic') {
                $classicTemplateId = $template->id;
            }
        }

        if ($variants === []) {
            $layout = CvTemplateLayout::defaults();

            $classicTemplateId = CvTemplate::query()->updateOrCreate(
                ['slug' => 'classic'],
                [
                    'name' => 'Classic',
                    'description' => 'Default admin-configurable CV template.',
                    'layout_json' => $layout,
                    'status' => 1,
                ],
            )->id;
        }

        $this->removeObsoleteTemplates($validSlugs, $classicTemplateId);
    }

    /**
     * @param  list<string>  $validSlugs
     */
    protected function removeObsoleteTemplates(array $validSlugs, ?int $fallbackTemplateId): void
    {
        if ($validSlugs === []) {
            return;
        }

        $obsolete = CvTemplate::query()
            ->cvOnly()
            ->whereNotIn('slug', $validSlugs)
            ->get();

        if ($obsolete->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($obsolete, $fallbackTemplateId): void {
            foreach ($obsolete as $template) {
                if ($fallbackTemplateId && $fallbackTemplateId !== $template->id) {
                    Cv::query()
                        ->where('template_id', $template->id)
                        ->update(['template_id' => $fallbackTemplateId]);
                }

                $template->forceDelete();
            }
        });
    }
}
