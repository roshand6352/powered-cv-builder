<?php



namespace App\Services\Cv\Templates;



use App\Models\CvTemplate;

use App\Services\BaseService;

use App\Support\Cache\ApplicationCache;

use App\Support\Cv\CvTemplateLayout;

use Illuminate\Support\Collection;

use Illuminate\Support\Facades\Cache;



class TemplateRegistry extends BaseService

{

    /**

     * @return array<string, mixed>|null

     */

    public function resolve(string $key): ?array

    {

        $template = CvTemplate::query()->cvOnly()->where('slug', $key)->first();



        return $template ? $this->mapTemplate($template) : null;

    }



    public function resolveById(?int $id): ?array

    {

        if (! $id) {

            return null;

        }



        $template = CvTemplate::query()->cvOnly()->find($id);



        return $template ? $this->mapTemplate($template) : null;

    }



    public function exists(string $key): bool

    {

        return CvTemplate::query()->cvOnly()->where('slug', $key)->exists();

    }



    /**

     * @return Collection<int, array<string, mixed>>

     */

    public function all(): Collection
    {
        return Cache::remember(
            ApplicationCache::CV_TEMPLATES_CATALOG,
            now()->addMinutes(10),
            fn () => CvTemplate::query()
                ->cvOnly()
                ->active()
                ->orderBy('name')
                ->get()
                ->map(fn (CvTemplate $template): array => $this->mapTemplate($template))
                ->values(),
        );
    }

    /**
     * Slimmer catalog payload for the CV builder page (avoids duplicate settings in HTML).
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function allForBuilder(): Collection
    {
        return Cache::remember(
            ApplicationCache::CV_TEMPLATES_BUILDER,
            now()->addMinutes(10),
            fn () => CvTemplate::query()
                ->cvOnly()
                ->active()
                ->orderBy('name')
                ->get(['id', 'name', 'slug', 'description', 'layout_json'])
                ->map(fn (CvTemplate $template): array => $this->mapTemplateForBuilder($template))
                ->values(),
        );
    }



    /**

     * @return list<string>

     */

    public function keys(): array

    {

        return CvTemplate::query()->cvOnly()->active()->pluck('slug')->all();

    }



    /**

     * @return array<string, mixed>

     */

    protected function mapTemplate(CvTemplate $template): array
    {
        $layout = CvTemplateLayout::normalize($template->layout_json);

        return [
            'key' => $template->slug,
            'slug' => $template->slug,
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description ?? '',
            'layout' => $layout['layout'],
            'layout_json' => $layout,
            'settings' => $layout['settings'],
            'section_order' => $layout['section_order'],
            'enabled_sections' => $layout['enabled_sections'],
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function mapTemplateForBuilder(CvTemplate $template): array
    {
        $layout = CvTemplateLayout::normalize($template->layout_json);

        return [
            'key' => $template->slug,
            'slug' => $template->slug,
            'id' => $template->id,
            'name' => $template->name,
            'description' => $template->description ?? '',
            'layout' => $layout['layout'],
            'layout_json' => $layout,
            'settings' => $layout['settings'],
            'section_order' => $layout['section_order'],
            'enabled_sections' => $layout['enabled_sections'],
        ];
    }

}


