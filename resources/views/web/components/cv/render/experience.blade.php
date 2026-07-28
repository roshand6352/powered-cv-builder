@props(['section', 'settings'])

<section class="cv-section cv-section-experience" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Experience'" />
    @forelse ($section['items'] ?? [] as $item)
        @continue(empty($item['company']) && empty($item['position']))
        <article class="cv-item">
            <div class="cv-item-head">
                <div>
                    <div class="cv-item-title">{{ $item['position'] ?: 'Position' }}</div>
                    <div class="cv-item-subtitle">{{ trim(($item['company'] ?? '').(($item['company'] ?? false) && ($item['location'] ?? false) ? ' · ' : '').($item['location'] ?? '')) }}</div>
                </div>
                <div class="cv-item-meta">
                    {{ \App\Support\Cv\CvDateFormatter::range(
                        $item['start_date'] ?? null,
                        $item['end_date'] ?? null,
                        (bool) ($item['current'] ?? false),
                    ) }}
                </div>
            </div>
            @if ($item['description'] ?? false)
                <x-cv.render.rich-text :content="$item['description']" class="cv-item-text" />
            @endif
        </article>
    @empty
        <p class="cv-item-text">No experience added yet.</p>
    @endforelse
</section>
