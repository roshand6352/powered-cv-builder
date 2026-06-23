@php $items = config('marketing.faqs', []); @endphp

<section class="faq" id="faq">
    <div class="container">
        <span class="section-tag" style="display:block;text-align:center">FAQ</span>
        <h2 class="section-title faq-title">Frequently asked questions</h2>
        <div class="faq-list">
            @foreach ($items as $item)
                <div class="faq-item">
                    <button type="button" class="faq-q" onclick="toggleFaq(this)">
                        {{ $item['question'] }}
                        <span class="faq-arrow">&#9662;</span>
                    </button>
                    <div class="faq-a">
                        <div class="faq-a-inner">{{ $item['answer'] }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</section>
