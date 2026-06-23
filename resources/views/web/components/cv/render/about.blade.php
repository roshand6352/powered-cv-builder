@props(['section', 'settings'])

@php($summary = $section['data']['summary'] ?? null)

@if (filled($summary))
    <section class="cv-section cv-section-about" data-section="{{ $section['key'] }}">
        <x-cv.render.section-title :section="$section" :settings="$settings" :label="$section['label'] ?? 'About'" />
        <x-cv.render.rich-text :content="$summary" class="cv-header-summary cv-item-text" />
    </section>
@endif
