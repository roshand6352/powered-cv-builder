@php
    $buildUrl = auth()->check() ? route('dashboard.cvs') : route('register');
@endphp

<section class="inner-cta">
    <div class="container">
        <h2>Ready to build your winning CV?</h2>
        <p>Join thousands of South Africans who create professional, ATS-optimised CVs in minutes — not hours.</p>
        <div class="inner-cta-btns">
            <a href="{{ $buildUrl }}" class="btn btn-primary btn-primary-lg">Get Started Free</a>
            <a href="{{ route('pricing') }}" class="btn btn-white-outline btn-primary-lg">View Pricing</a>
        </div>
    </div>
</section>
