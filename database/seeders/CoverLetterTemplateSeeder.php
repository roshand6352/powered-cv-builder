<?php

namespace Database\Seeders;

use App\Enums\TemplateType;
use App\Models\CoverLetter;
use App\Models\CvTemplate;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoverLetterTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $variants = config('cover-letter-templates.templates', []);
        $validSlugs = [];
        $fallbackTemplateId = null;

        foreach ($variants as $definition) {
            $slug = $definition['slug'] ?? null;

            if (! is_string($slug) || $slug === '') {
                continue;
            }

            $layout = CoverLetterTemplateLayout::normalize([
                'layout' => $definition['layout'] ?? 'professional',
                'settings' => array_replace_recursive(
                    CoverLetterTemplateLayout::defaultSettings(),
                    $definition['settings'] ?? [],
                ),
            ]);

            $template = CvTemplate::query()->updateOrCreate(
                ['slug' => $slug],
                [
                    'name' => $definition['name'],
                    'type' => TemplateType::CoverLetter,
                    'description' => $definition['description'] ?? null,
                    'layout_json' => $layout,
                    'status' => 1,
                ],
            );

            $validSlugs[] = $slug;

            if ($slug === 'cover-letter-professional') {
                $fallbackTemplateId = $template->id;
            }
        }

        $this->removeObsoleteTemplates($validSlugs, $fallbackTemplateId);
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
            ->coverLetterOnly()
            ->whereNotIn('slug', $validSlugs)
            ->get();

        if ($obsolete->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($obsolete, $fallbackTemplateId): void {
            foreach ($obsolete as $template) {
                if ($fallbackTemplateId && $fallbackTemplateId !== $template->id) {
                    CoverLetter::query()
                        ->where('template_id', $template->id)
                        ->update(['template_id' => $fallbackTemplateId]);
                }

                $template->forceDelete();
            }
        });
    }
}
