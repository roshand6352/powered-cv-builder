@php
    $brand = config('marketing.brand.name', 'ApplyDocs ZA');
    $nav = config('marketing.nav', []);
    $logo = asset(config('marketing.brand.logo', 'images/LOGO-02.png'));
    $buildUrl = auth()->check() ? route('dashboard.cvs') : route('register');
    $coverLetterUrl = auth()->check() ? route('dashboard.cover-letters') : route('register');
@endphp

<nav>
    <div class="container">
        <div class="nav-inner">
            <a href="{{ route('home') }}" class="nav-logo">
                <img src="{{ $logo }}" alt="{{ $brand }}">
            </a>

            <ul class="nav-links">
                @foreach ($nav as $item)
                    <li><a href="{{ url($item['href']) }}">{{ $item['label'] }}</a></li>
                @endforeach
            </ul>

            <div class="nav-actions">
                @auth
                    <a href="{{ route('dashboard') }}" class="btn btn-ghost">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-ghost">Log in</a>
                @endauth
                <a href="{{ $coverLetterUrl }}" class="btn btn-ghost">Generate Cover Letter</a>
                <a href="{{ $buildUrl }}" class="btn btn-primary">Build My CV Free</a>
            </div>

            <button type="button" class="nav-toggle" aria-label="Open menu" aria-expanded="false" data-nav-toggle>
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="3" y1="6" x2="21" y2="6"></line>
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                    <line x1="3" y1="18" x2="21" y2="18"></line>
                </svg>
            </button>
        </div>
    </div>
</nav>

<div class="nav-mobile" data-nav-mobile>
    <ul class="nav-mobile-links">
        @foreach ($nav as $item)
            <li><a href="{{ url($item['href']) }}">{{ $item['label'] }}</a></li>
        @endforeach
    </ul>
    <div class="nav-mobile-actions">
        @auth
            <a href="{{ route('dashboard') }}" class="btn btn-ghost" style="width:100%">Dashboard</a>
        @else
            <a href="{{ route('login') }}" class="btn btn-ghost" style="width:100%">Log in</a>
        @endauth
        <a href="{{ $coverLetterUrl }}" class="btn btn-ghost" style="width:100%">Generate Cover Letter</a>
        <a href="{{ $buildUrl }}" class="btn btn-primary" style="width:100%">Build My CV Free</a>
    </div>
</div>
