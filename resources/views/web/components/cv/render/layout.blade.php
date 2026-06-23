@props(['sections' => [], 'sectionLayout' => [], 'settings' => [], 'renderer'])

@php
    $asideSections = collect($sectionLayout['aside'] ?? []);
    $mainSections = collect($sectionLayout['main'] ?? []);
    $sidebarPosition = $sectionLayout['sidebar_position'] ?? 'left';
    $headerSection = $asideSections->first(fn (array $section): bool => in_array($section['key'] ?? '', ['header', 'personal_details'], true))
        ?? $mainSections->first(fn (array $section): bool => in_array($section['key'] ?? '', ['header', 'personal_details'], true))
        ?? collect($sections)->first(fn (array $section): bool => in_array($section['key'] ?? '', ['header', 'personal_details'], true));
@endphp

@if (($sectionLayout['mode'] ?? 'single') === 'sidebar')
    <div class="cv-grid-sidebar cv-sidebar--{{ $sidebarPosition }}">
        @if ($sidebarPosition === 'right')
            <main class="cv-main">
                <x-cv.render.sections :sections="$mainSections->all()" :settings="$settings" :renderer="$renderer" />
            </main>
            <aside class="cv-aside">
                @if ($headerSection)
                    <x-cv.render.sidebar-photo :section="$headerSection" :settings="$settings" />
                @endif
                <x-cv.render.sections :sections="$asideSections->all()" :settings="$settings" :renderer="$renderer" />
            </aside>
        @else
            <aside class="cv-aside">
                @if ($headerSection)
                    <x-cv.render.sidebar-photo :section="$headerSection" :settings="$settings" />
                @endif
                <x-cv.render.sections :sections="$asideSections->all()" :settings="$settings" :renderer="$renderer" />
            </aside>
            <main class="cv-main">
                <x-cv.render.sections :sections="$mainSections->all()" :settings="$settings" :renderer="$renderer" />
            </main>
        @endif
    </div>
@else
    <div class="cv-content">
        <x-cv.render.sections :sections="$sections" :settings="$settings" :renderer="$renderer" />
    </div>
@endif
