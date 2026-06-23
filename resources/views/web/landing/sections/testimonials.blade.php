@php $items = config('marketing.testimonials', []); @endphp

<section class="testimonials">
    <div class="container">
        <span class="section-tag" style="display:block;text-align:center">Testimonials</span>
        <h2 class="section-title testi-title">South Africans getting hired with ApplyDocs ZA</h2>
        <div class="testi-grid">
            @foreach ($items as $item)
                <div class="testi-card">
                    <div class="testi-stars">&#9733;&#9733;&#9733;&#9733;&#9733;</div>
                    <p class="testi-quote">&ldquo;{{ $item['quote'] }}&rdquo;</p>
                    <div class="testi-name">{{ $item['name'] }}</div>
                    <div class="testi-meta">{{ $item['role'] }} &middot; {{ $item['plan'] }}</div>
                </div>
            @endforeach
        </div>
    </div>
</section>
