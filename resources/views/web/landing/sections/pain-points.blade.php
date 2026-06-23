@php $cards = config('marketing.pain_cards', []); @endphp

<section class="pain" id="why">
    <div class="container">
        <div class="pain-intro">
            <span class="section-tag">The Problem</span>
            <h2 class="section-title">Give yourself a real opportunity to compete in the South African job market</h2>
            <p class="section-sub">When there are limited jobs and many strong applicants, a competitive edge is necessary if you want to be short-listed and interviewed. And it all begins with your CV.</p>
        </div>
        <h3 style="font-size:20px;font-weight:700;margin-bottom:28px;color:var(--navy)">How ApplyDocs ZA can help you get hired</h3>
        <div class="pain-cards">
            @foreach ($cards as $card)
                <div class="pain-card">
                    <div class="pain-icon">
                        @include('web.partials.icons.pain-' . ($card['icon'] ?? 'shield'))
                    </div>
                    <h3>{{ $card['title'] }}</h3>
                    <p>{{ $card['description'] }}</p>
                </div>
            @endforeach
        </div>
    </div>
</section>
