@props(['section', 'settings' => [], 'label' => null])

@php
    $title = $label ?? ($section['label'] ?? '');
    $showIcon = \App\Support\Cv\CvSectionIcons::enabled($settings);
    $icon = $showIcon ? \App\Support\Cv\CvSectionIcons::symbol($section['key'] ?? '') : null;
@endphp

<h2 class="cv-section-title">
    @if ($icon)
        <i class="cv-section-icon {{ $icon }}" aria-hidden="true"></i>
    @endif
    {{ $title }}
</h2>
