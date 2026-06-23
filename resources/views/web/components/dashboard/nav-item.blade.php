@props(['href', 'active' => false, 'icon' => null])

<a
    href="{{ $href }}"
    @class([
        'group flex items-center gap-3 rounded-lg px-3 py-2.5 text-sm font-medium transition',
        $active ? 'dash-nav-active' : 'dash-nav-idle',
    ])
>
    @if ($icon)
        <span @class([
            'flex h-8 w-8 shrink-0 items-center justify-center rounded-lg transition',
            $active ? 'bg-brand-100 text-brand-600' : 'bg-cream text-muted group-hover:bg-brand-50 group-hover:text-brand-600',
        ])>
            @include('web.components.dashboard.icons.' . $icon)
        </span>
    @endif
    <span>{{ $slot }}</span>
</a>
