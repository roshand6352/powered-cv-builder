<?php

namespace App\Services\CoverLetter;

use App\Models\CvTemplate;
use App\Services\BaseService;
use App\Support\CoverLetter\CoverLetterTemplateLayout;
use Illuminate\Support\Collection;

class CoverLetterTemplateRegistry extends BaseService
{
    public function resolve(string $key): ?array
    {
        $template = CvTemplate::query()->coverLetterOnly()->where('slug', $key)->first();

        return $template ? $this->mapTemplate($template) : null;
    }

    public function resolveModel(?int $id): ?CvTemplate
    {
        if (! $id) {
            return null;
        }

        return CvTemplate::query()->coverLetterOnly()->find($id);
    }

    public function exists(string $key): bool
    {
        return CvTemplate::query()->coverLetterOnly()->where('slug', $key)->exists();
    }

    public function findActiveBySlug(string $key): ?CvTemplate
    {
        return CvTemplate::query()->coverLetterOnly()->active()->where('slug', $key)->first();
    }

    /**
     * @return Collection<int, array<string, mixed>>
     */
    public function all(): Collection
    {
        return CvTemplate::query()
            ->coverLetterOnly()
            ->active()
            ->orderBy('name')
            ->get()
            ->map(fn (CvTemplate $template): array => $this->mapTemplate($template))
            ->values();
    }

    /**
     * @return array<string, mixed>
     */
    public function mapTemplate(CvTemplate $template): array
    {
        $layout = CoverLetterTemplateLayout::normalize($template->layout_json);

        return [
            'key' => $template->slug,
            'slug' => $template->slug,
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description ?? '',
            'thumbnail' => null,
            'layout' => $layout['layout'],
            'layout_json' => $layout,
            'settings' => $layout['settings'],
            'section_order' => $layout['section_order'],
            'enabled_sections' => $layout['enabled_sections'],
        ];
    }
}
