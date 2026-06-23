<section id="faq" class="page-section page-section--cream">
    <div class="container">
        <div class="section-intro">
            <span class="section-tag">FAQ</span>
            <h2>Frequently asked questions</h2>
            <p>Everything you need to know about building CVs with ApplyDocs ZA.</p>
        </div>

        <div class="faq-list faq-box">
            @foreach (config('marketing.faqs') as $item)
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
