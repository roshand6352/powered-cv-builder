@props(['section', 'settings'])

<section class="cv-section" data-section="{{ $section['key'] ?? 'unknown' }}">
    <x-cv.render.section-title :section="$section" :settings="$settings" />
    <p class="cv-item-text">This section is not configured for rendering.</p>
</section>
