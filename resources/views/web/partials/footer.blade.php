@php
    $brand = config('marketing.brand.name', 'ApplyDocs ZA');
    $footer = config('marketing.footer', []);
    $logo = asset(config('marketing.brand.footer_logo', 'images/LOGO-19.png'));
@endphp

<footer>
    <div class="container">
        <div class="footer-top">
            <div>
                <img src="{{ $logo }}" alt="{{ $brand }}" class="footer-logo">
                <p class="footer-tagline">{{ $footer['description'] ?? '' }}</p>
            </div>
            <div class="footer-col">
                <h4>Tools</h4>
                <ul>
                    @foreach ($footer['tools'] ?? [] as $link)
                        <li><a href="{{ url($link['href']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="footer-col">
                <h4>Company</h4>
                <ul>
                    @foreach ($footer['company'] ?? [] as $link)
                        <li><a href="{{ url($link['href']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div class="footer-col">
                <h4>Legal</h4>
                <ul>
                    @foreach ($footer['legal'] ?? [] as $link)
                        <li><a href="{{ url($link['href']) }}">{{ $link['label'] }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span class="footer-copy">&copy; {{ date('Y') }} {{ $brand }} &middot; All rights reserved</span>
            <div class="footer-badges">
                @foreach ($footer['badges'] ?? [] as $badge)
                    <span class="f-badge">{{ $badge }}</span>
                @endforeach
            </div>
        </div>
    </div>
</footer>
