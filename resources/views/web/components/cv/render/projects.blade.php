@props(['section', 'settings'])

<section class="cv-section cv-section-projects" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Projects'" />
    @forelse ($section['items'] ?? [] as $item)
        @continue(empty($item['name']))
        <article class="cv-item">
            <div class="cv-item-head">
                <div class="cv-item-title">{{ $item['name'] }}</div>
                <div class="cv-item-meta">
                    {{ \App\Support\Cv\CvDateFormatter::range(
                        $item['start_date'] ?? null,
                        $item['end_date'] ?? null,
                        (bool) ($item['in_progress'] ?? false),
                    ) }}
                </div>
            </div>
            @if ($item['url'] ?? false)<div class="cv-item-subtitle">{{ $item['url'] }}</div>@endif
            @if ($item['description'] ?? false)<x-cv.render.rich-text :content="$item['description']" class="cv-item-text" />@endif
        </article>
    @empty
        <p class="cv-item-text">No projects added yet.</p>
    @endforelse
</section>
