@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'document',
    'color' => 'brand',
])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-600',
        'navy' => 'bg-brand-900/10 text-navy',
        'orange' => 'bg-brand-100 text-brand-700',
        'cream' => 'bg-cream text-brand-800 border border-line',
    ];
    $iconColor = $colors[$color] ?? $colors['brand'];
@endphp

<div {{ $attributes->merge(['class' => 'dash-card p-6']) }}>
    <div class="flex items-start justify-between">
        <div>
            <p class="text-sm font-medium text-muted">{{ $label }}</p>
            <p class="mt-2 text-3xl font-bold tracking-tight text-navy">{{ $value }}</p>
            @if ($hint)
                <p class="mt-1 text-sm text-muted">{{ $hint }}</p>
            @endif
        </div>
        <div class="flex h-11 w-11 items-center justify-center rounded-xl {{ $iconColor }}">
            @include('web.components.dashboard.icons.' . $icon)
        </div>
    </div>
</div>
