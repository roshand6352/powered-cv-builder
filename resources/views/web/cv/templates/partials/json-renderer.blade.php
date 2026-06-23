@php
    $renderSections = $renderSections ?? ($preview['sections'] ?? []);
@endphp

<x-cv.render.layout
    :sections="$renderSections"
    :section-layout="$sectionLayout"
    :settings="$settings"
    :renderer="$jsonRenderer"
/>
