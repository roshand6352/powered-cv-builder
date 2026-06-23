@php
    $stats = config('marketing.stats', []);
    $buildUrl = auth()->check() ? route('dashboard.cvs') : route('register');
@endphp

<section class="hero">
    <div class="container">
        <div class="hero-grid">
            <div>
                <div class="hero-badge">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                    Free CV Builder &middot; Made for South Africa
                </div>
                <h1>Build a professional CV in <span>minutes.</span></h1>
                <p class="hero-sub">Answer a few simple questions and ApplyDocs ZA generates your CV and cover letter for you. Download free or unlock AI-powered writing to make every line stand out.</p>
                <div class="hero-ctas">
                    <a href="{{ $buildUrl }}" class="btn btn-primary btn-primary-lg">Build My CV Free</a>
                    <a href="#how" class="btn btn-outline-lg" style="color:white;border-color:rgba(255,255,255,0.3)">See how it works</a>
                </div>
                <div class="hero-trust">
                    <span class="stars">&#9733;&#9733;&#9733;&#9733;&#9733;</span>
                    <span class="hero-trust-text">Trusted by <b>{{ $stats['trusted_users'] ?? '10,000' }}+</b> South Africans &nbsp;&middot;&nbsp; No card required</span>
                </div>
            </div>
            <div class="hero-visual">
                @include('web.landing.sections.cv-mockup')
            </div>
        </div>
    </div>
</section>
