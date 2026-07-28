@props(['section', 'settings'])

<section class="cv-section cv-section-certificates" data-section="{{ $section['key'] }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'Certificates'" />
    @forelse ($section['items'] ?? [] as $item)
        @continue(empty($item['name']))
        @php
            $issuer = trim((string) ($item['issuer'] ?? ''));
            $date = \App\Support\Cv\CvDateFormatter::display($item['date'] ?? null);
            $subtitle = collect([$issuer, $date])->filter()->implode(' · ');
        @endphp
        <article class="cv-item">
            <div class="cv-item-title">{{ $item['name'] }}</div>
            @if ($subtitle !== '')
                <div class="cv-item-subtitle">{{ $subtitle }}</div>
            @endif
            @if ($item['url'] ?? false)
                <div class="cv-item-text">{{ $item['url'] }}</div>
            @endif
        </article>
    @empty
        <p class="cv-item-text">No certificates added yet.</p>
    @endforelse
</section>
