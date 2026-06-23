@props(['section', 'settings'])

<section class="cv-section cv-section-skills" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Skills'" />
    <div class="cv-tags">
        @forelse ($section['items'] ?? [] as $item)
            @continue(empty($item['name']))
            <span class="cv-tag">{{ $item['name'] }}@if($item['level'] ?? false) ({{ $item['level'] }})@endif</span>
        @empty
            <span class="cv-item-text">No skills added yet.</span>
        @endforelse
    </div>
</section>
