@php
    $buildUrl = auth()->check() ? route('dashboard.cvs') : route('register');
    $coverLetterUrl = auth()->check() ? route('dashboard.cover-letters') : route('register');
@endphp

<section class="cta-section">
    <div class="container">
        <h2>Your next interview could be<br>10 minutes away</h2>
        <p>Build your CV free with no card and no pressure. Upgrade to Pro if you want the AI edge and a clean, watermark-free PDF. Either way, you leave with something better than you came with.</p>
        <div class="cta-btns">
            <a href="{{ $buildUrl }}" class="btn btn-primary btn-primary-lg" style="font-size:16px;padding:16px 32px">Build My CV Free</a>
            <a href="{{ $coverLetterUrl }}" class="btn btn-white btn-primary-lg" style="font-size:16px;padding:16px 32px">Generate Cover Letter</a>
            <a href="{{ route('contact') }}" class="btn btn-white-outline btn-primary-lg" style="font-size:16px;padding:16px 32px">Live Chat</a>
        </div>
    </div>
</section>
