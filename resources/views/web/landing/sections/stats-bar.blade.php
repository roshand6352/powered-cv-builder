@php $items = config('marketing.social_proof', []); @endphp

<div class="stats-bar">
    <div class="container">
        <div class="stats-grid">
            @foreach ($items as $item)
                <div class="stat-item">
                    <div class="stat-num">{{ $item['value'] }}</div>
                    <div class="stat-label">{{ $item['label'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</div>
