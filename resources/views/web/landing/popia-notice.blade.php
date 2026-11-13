@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'POPIA Notice',
        'description' => 'How we process your personal information under the Protection of Personal Information Act 4 of 2013 (POPIA).',
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                {{-- DRAFT: this notice must be reviewed by a legal adviser before publishing. Items marked [TO BE CONFIRMED] need real details. --}}
                <h2>1. Who we are</h2>
                <p>ApplyDocs ZA is the responsible party for the personal information processed through this website and its CV Builder and Cover Letter tools.</p>

                <h2>2. Information Officer</h2>
                <p>Information Officer: [TO BE CONFIRMED]. Privacy queries can be sent to <a href="mailto:privacy@example.com">privacy@example.com</a> [TO BE CONFIRMED].</p>

                <h2>3. Personal information we collect</h2>
                <p>We collect information you provide directly, including account details, CV and cover letter content, and communications with our support team. We also collect usage data to improve our services.</p>

                <h2>4. Why we process it</h2>
                <p>We use your information to provide and improve the CV builder, process payments for paid plans, send service-related communications, and ensure platform security.</p>

                <h2>5. Third-party processing</h2>
                <p>When you use AI features, your CV content may be processed by third-party AI providers solely to generate suggestions. Payments are processed by our payment provider. We do not sell your personal information.</p>

                <h2>6. Security and retention</h2>
                <p>Data is encrypted in transit and at rest. We retain your information while your account is active and for a reasonable period thereafter as required by law.</p>

                <h2>7. Your rights</h2>
                <p>Under POPIA you may request access to, correction of, or deletion of your personal information, and you may object to its processing. You can manage your data from your account settings or by contacting us at support@example.com [TO BE CONFIRMED].</p>

                <h2>8. Complaints</h2>
                <p>If you believe your personal information has been processed unlawfully, please contact us first. You also have the right to lodge a complaint with the Information Regulator (South Africa).</p>

                <h2>9. More information</h2>
                <p>See our <a href="{{ route('privacy') }}">Privacy Policy</a> for further details on how we handle your information.</p>
            </div>
        </div>
    </section>
@endsection
