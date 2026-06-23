@props(['section', 'settings'])

<section class="cv-section" data-section="{{ $section['key'] }}">
    @foreach ($section['items'] ?? [] as $group)
        @continue(empty($group['title']) && empty($group['items']))
        <div class="cv-group">
            @if ($group['title'] ?? false)
                <h2 class="cv-group-title">{{ $group['title'] }}</h2>
            @endif
            @foreach ($group['items'] ?? [] as $item)
                @continue(empty($item['title']) && empty($item['content']))
                <article class="cv-item">
                    @if ($item['title'] ?? false)<div class="cv-item-title">{{ $item['title'] }}</div>@endif
                    @if ($item['content'] ?? false)<x-cv.render.rich-text :content="$item['content']" class="cv-item-text" />@endif
                </article>
            @endforeach
        </div>
    @endforeach
</section>
