@props(['section', 'settings'])

<section class="cv-section cv-section-languages" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Languages'" />
    @forelse ($section['items'] ?? [] as $item)
        @continue(empty($item['name']))
        <div class="cv-list-row">
            <strong>{{ $item['name'] }}</strong>
            <span>{{ $item['proficiency'] ?? '' }}</span>
        </div>
    @empty
        <p class="cv-item-text">No languages added yet.</p>
    @endforelse
</section>
