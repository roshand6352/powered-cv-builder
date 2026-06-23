@php
    $tools = config('marketing.tools', []);
    $resolveUrl = function (string $key): string {
        return match ($key) {
            'register' => auth()->check() ? route('dashboard.cvs') : route('register'),
            'cover-letter' => auth()->check() ? route('dashboard.cover-letters') : route('register'),
            default => url($key),
        };
    };
@endphp

<section class="tools" id="tools">
    <div class="container">
        <span class="section-tag" style="display:block;text-align:center">Our Tools</span>
        <h2 class="section-title tools-title">Two tools. One goal &rarr; Getting you hired</h2>
        <p class="tools-sub">Start with your CV, then generate a matching cover letter in seconds.</p>
        <div class="tools-grid">
            @foreach ($tools as $tool)
                <div class="tool-card {{ ! empty($tool['featured']) ? 'featured' : '' }}">
                    @if (! empty($tool['badge']))
                        <div class="tool-start-badge">{{ $tool['badge'] }}</div>
                    @endif
                    <div class="tool-icon-wrap">
                        @if (str_contains(strtolower($tool['title']), 'cover'))
                            <svg viewBox="0 0 24 24"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        @else
                            <svg viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        @endif
                    </div>
                    <h3>{{ $tool['title'] }}</h3>
                    <p>{{ $tool['description'] }}</p>
                    <ul class="feature-list">
                        @foreach ($tool['features'] as $feature)
                            <li class="{{ ! empty($feature['pro']) ? 'feat-pro' : 'feat-free' }}">
                                <span class="feat-icon">{{ ! empty($feature['pro']) ? '✨' : '✓' }}</span>
                                {{ $feature['text'] }}{{ ! empty($feature['pro']) ? ' → Pro' : '' }}
                            </li>
                        @endforeach
                    </ul>
                    @if (! empty($tool['cta']) && ! empty($tool['href']))
                        <div class="tool-cta">
                            <a
                                href="{{ $resolveUrl($tool['href']) }}"
                                @class([
                                    'btn',
                                    'btn-primary' => ! empty($tool['featured']),
                                    'btn-outline' => empty($tool['featured']),
                                ])
                                style="width:100%;justify-content:center;padding:14px"
                            >
                                {{ $tool['cta'] }}
                            </a>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</section>
