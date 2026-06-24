@props(['section', 'settings'])

<section class="cv-section cv-section-education" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Education'" />
    @forelse ($section['items'] ?? [] as $item)
        @continue(empty($item['institution']) && empty($item['degree']))
        <article class="cv-item">
            <div class="cv-item-head">
                <div>
                    <div class="cv-item-title">{{ $item['institution'] ?: 'Institution' }}</div>
                    <div class="cv-item-subtitle">{{ trim(($item['degree'] ?? '').(($item['degree'] ?? false) && ($item['field'] ?? false) ? ', ' : '').($item['field'] ?? '')) }}</div>
                </div>
                <div class="cv-item-meta">{{ $item['start_date'] ?? '' }}@if(($item['start_date'] ?? false) && (($item['end_date'] ?? false) || ($item['in_progress'] ?? false))) – @endif{{ ($item['in_progress'] ?? false) ? 'Present' : ($item['end_date'] ?? '') }}</div>
            </div>
            @if ($item['description'] ?? false)
                <x-cv.render.rich-text :content="$item['description']" class="cv-item-text" />
            @endif
        </article>
    @empty
        <p class="cv-item-text">No education added yet.</p>
    @endforelse
</section>
