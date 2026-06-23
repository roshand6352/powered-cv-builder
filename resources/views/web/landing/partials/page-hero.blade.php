@props([
    'title',
    'description' => null,
])

<section class="page-hero">
    <div class="container">
        <h1>{{ $title }}</h1>
        @if ($description)
            <p>{{ $description }}</p>
        @endif
    </div>
</section>
