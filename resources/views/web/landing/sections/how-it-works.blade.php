@php
    $cv = config('marketing.how_it_works_cv', []);
    $cl = config('marketing.how_it_works_cover_letter', []);
    $resolveUrl = function (string $key): string {
        return match ($key) {
            'register' => auth()->check() ? route('dashboard.cvs') : route('register'),
            'cover-letter' => auth()->check() ? route('dashboard.cover-letters') : route('register'),
            default => url($key),
        };
    };
@endphp

<section class="how" id="how">
    <div class="container">
        <div style="text-align:center;margin-bottom:12px">
            <span class="how-tool-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                {{ $cv['label'] ?? 'CV Builder' }}
            </span>
        </div>
        <h2 class="how-heading" style="text-align:center">{{ $cv['title'] ?? '' }}</h2>
        <p class="how-sub" style="text-align:center;max-width:540px;margin:0 auto 48px">{{ $cv['subtitle'] ?? '' }}</p>
        <div class="how-block">
            <div class="steps-grid">
                @foreach ($cv['steps'] ?? [] as $index => $step)
                    <div class="step-card" @if($loop->last) style="border-color:var(--orange)" @endif>
                        <div class="step-num-big" @if($loop->last) style="color:var(--orange-mid)" @endif>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <h4>{{ $step['title'] }}</h4>
                        <p>{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
            @if (! empty($cv['cta']) && ! empty($cv['href']))
                <div class="how-cta">
                    <a href="{{ $resolveUrl($cv['href']) }}" class="btn btn-primary btn-primary-lg">
                        {{ $cv['cta'] }}
                    </a>
                </div>
            @endif
        </div>

        <div style="text-align:center;margin-bottom:12px">
            <span class="how-tool-label">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                {{ $cl['label'] ?? 'Cover Letter Generator' }}
            </span>
        </div>
        <h2 class="how-heading" style="text-align:center">{{ $cl['title'] ?? '' }}</h2>
        <p class="how-sub" style="text-align:center;max-width:540px;margin:0 auto 48px">{{ $cl['subtitle'] ?? '' }}</p>
        <div class="how-block">
            <div class="steps-grid">
                @foreach ($cl['steps'] ?? [] as $index => $step)
                    <div class="step-card" @if($loop->last) style="border-color:var(--orange)" @endif>
                        <div class="step-num-big" @if($loop->last) style="color:var(--orange-mid)" @endif>{{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}</div>
                        <h4>{{ $step['title'] }}</h4>
                        <p>{{ $step['description'] }}</p>
                    </div>
                @endforeach
            </div>
            @if (! empty($cl['cta']) && ! empty($cl['href']))
                <div class="how-cta">
                    <a href="{{ $resolveUrl($cl['href']) }}" class="btn btn-primary btn-primary-lg">
                        {{ $cl['cta'] }}
                    </a>
                </div>
            @endif
        </div>
    </div>
</section>
