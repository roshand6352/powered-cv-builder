@php
    $items = config('marketing.differentiators', []);
    $icons = ['shield', 'wallet', 'star', 'globe'];
@endphp

<section class="why">
    <div class="container">
        <span class="section-tag">Why us</span>
        <h2 class="section-title" style="margin-bottom:14px">Built for South Africans,<br>by South Africans</h2>
        <p class="why-tagline">We understand the SA job market — its norms, its pressures, and what it takes to get shortlisted.</p>
        <div class="why-grid">
            @foreach ($items as $index => $item)
                <div class="why-card">
                    <div class="why-icon">
                        @include('web.partials.icons.why-' . ($icons[$index] ?? 'shield'))
                    </div>
                    <h3>{{ $item['title'] }}</h3>
                    <p>{{ $item['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
