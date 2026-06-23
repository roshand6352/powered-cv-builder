@props(['section', 'settings'])

<section class="cv-section" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" />
    <div class="cv-tags" style="display:grid;grid-template-columns:repeat(auto-fit,minmax(220px,1fr));gap:.75rem;">
        @forelse ($section['items'] ?? [] as $item)
            @continue(empty($item['name']))
            <article class="cv-item" style="margin:0;border:1px solid var(--cv-border);border-radius:.75rem;padding:.85rem;">
                <div class="cv-item-title">{{ $item['name'] }}</div>
                @if ($item['company'] ?? false)<div class="cv-item-subtitle">{{ $item['company'] }}</div>@endif
                @if ($item['relationship'] ?? false)<div class="cv-item-text">{{ $item['relationship'] }}</div>@endif
                @if ($item['email'] ?? false)<div class="cv-item-text">{{ $item['email'] }}</div>@endif
            </article>
        @empty
            <p class="cv-item-text">No references added yet.</p>
        @endforelse
    </div>
</section>
