@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'Privacy Policy',
        'description' => 'Last updated: ' . now()->format('F j, Y'),
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                <h2>1. Information we collect</h2>
                <p>We collect information you provide directly, including account details, CV content, and communications with our support team. We also collect usage data to improve our services.</p>

                <h2>2. How we use your information</h2>
                <p>We use your information to provide and improve the CV builder, process subscriptions, send service-related communications, and ensure platform security.</p>

                <h2>3. AI processing</h2>
                <p>When you use AI features, your CV content may be processed by third-party AI providers solely to generate suggestions. We do not use your content to train public models without consent.</p>

                <h2>4. Data storage & security</h2>
                <p>Data is encrypted in transit and at rest. We retain your information while your account is active and for a reasonable period thereafter as required by law.</p>

                <h2>5. Your rights</h2>
                <p>You may access, export, correct, or delete your personal data at any time from your account settings or by contacting us at support@example.com.</p>

                <h2>6. Cookies</h2>
                <p>We use essential cookies for authentication and session management. Analytics cookies are optional and can be disabled in your browser settings.</p>

                <h2>7. Contact</h2>
                <p>For privacy-related questions, contact us at <a href="mailto:privacy@example.com">privacy@example.com</a>.</p>
            </div>
        </div>
    </section>
@endsection
