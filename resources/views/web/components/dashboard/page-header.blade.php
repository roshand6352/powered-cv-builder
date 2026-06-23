@props([
    'title',
    'description' => null,
])

<div {{ $attributes->merge(['class' => 'mb-0 min-w-0']) }}>
    <h1 class="dash-page-title">{{ $title }}</h1>
    @if ($description)
        <p class="dash-page-desc">{{ $description }}</p>
    @endif
</div>
