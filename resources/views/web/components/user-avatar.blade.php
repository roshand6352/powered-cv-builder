@props(['user' => auth()->user(), 'size' => 'md'])

@php
    $sizes = [
        'sm' => 'h-8 w-8 text-xs',
        'md' => 'h-9 w-9 text-sm',
        'lg' => 'h-16 w-16 text-lg',
        'xl' => 'h-24 w-24 text-2xl',
    ];
    $sizeClass = $sizes[$size] ?? $sizes['md'];
@endphp

@if ($user->avatar_url)
    <img
        {{ $attributes->merge(['class' => "{$sizeClass} rounded-full object-cover ring-2 ring-white"]) }}
        src="{{ $user->avatar_url }}"
        alt="{{ $user->name }}"
    >
@else
    <span {{ $attributes->merge(['class' => "{$sizeClass} inline-flex items-center justify-center rounded-full bg-brand-600 font-semibold text-white ring-2 ring-white"]) }}>
        {{ $user->initials }}
    </span>
@endif
