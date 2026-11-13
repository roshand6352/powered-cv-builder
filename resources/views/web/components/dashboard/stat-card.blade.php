@props([
    'label',
    'value',
    'hint' => null,
    'icon' => 'document',
    'color' => 'brand',
    'route' => null,
])

@php
    $colors = [
        'brand' => 'bg-brand-50 text-brand-600',
        'navy' => 'bg-brand-900/10 text-navy',
        'orange' => 'bg-brand-100 text-brand-700',
        'cream' => 'bg-cream text-brand-800 border border-line',
    ];
    $iconColor = $colors[$color] ?? $colors['brand'];
    $linkClasses = $route
        ? 'relative max-lg:cursor-pointer max-lg:transition max-lg:hover:-translate-y-0.5 max-lg:hover:shadow-md max-lg:active:translate-y-0 max-lg:active:shadow-sm'
        : '';
@endphp

<div {{ $attributes->merge(['class' => trim('dash-card min-w-0 p-5 sm:p-6 ' . $linkClasses)]) }}>
    @if ($route)
        <a
            href="{{ $route }}"
            class="absolute inset-0 z-10 rounded-2xl focus:outline-none focus-visible:ring-2 focus-visible:ring-brand-500 focus-visible:ring-offset-2 lg:hidden"
            aria-label="{{ $label }}"
        ></a>
    @endif
    <div class="flex items-start justify-between gap-3">
        <div class="min-w-0 flex-1">
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
