@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'Terms of Service',
        'description' => 'Last updated: ' . now()->format('F j, Y'),
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                <h2>1. Acceptance of terms</h2>
                <p>By accessing or using {{ config('app.name') }}, you agree to be bound by these Terms of Service. If you do not agree, do not use the platform.</p>

                <h2>2. Account responsibilities</h2>
                <p>You are responsible for maintaining the confidentiality of your account credentials and for all activity under your account. You must provide accurate registration information.</p>

                <h2>3. Acceptable use</h2>
                <p>You agree not to misuse the service, including attempting unauthorized access, uploading malicious content, or using the platform for unlawful purposes.</p>

                <h2>4. Subscriptions & billing</h2>
                <p>Paid plans renew automatically unless cancelled before the renewal date. Refunds are handled according to our refund policy stated on the pricing page.</p>

                <h2>5. Intellectual property</h2>
                <p>You retain ownership of CV content you create. We retain ownership of the platform, templates, and underlying technology. You grant us a limited license to host and process your content to provide the service.</p>

                <h2>6. AI-generated content</h2>
                <p>AI suggestions are provided as-is. You are responsible for reviewing and verifying all content before submitting CVs to employers or third parties.</p>

                <h2>7. Limitation of liability</h2>
                <p>The service is provided "as is" without warranties. We are not liable for indirect damages arising from use of the platform, including employment outcomes.</p>

                <h2>8. Changes to terms</h2>
                <p>We may update these terms from time to time. Continued use after changes constitutes acceptance of the updated terms.</p>

                <h2>9. Contact</h2>
                <p>Questions about these terms? Email <a href="mailto:legal@example.com">legal@example.com</a>.</p>
            </div>
        </div>
    </section>
@endsection
