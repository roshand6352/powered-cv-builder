@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'POPIA Notice',
        'description' => 'Last updated: 21 October 2026',
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                <p>ApplyDocs ZA respects your privacy and is committed to protecting your personal information in accordance with the Protection of Personal Information Act 4 of 2013 (POPIA).</p>
                <p>This notice explains what information we collect, why we collect it, how it is used and the rights you have regarding your personal information.</p>

                <h2>1. Who is responsible for your information?</h2>
                <p>ApplyDocs ZA is the responsible party for personal information processed through the ApplyDocs ZA website and web application.</p>

                <h2>2. What information do we collect?</h2>
                <p>Depending on how you use ApplyDocs ZA, we may collect:</p>
                <ul>
                    <li>Your name, email address, telephone number and profile photograph.</li>
                    <li>Employment history, qualifications, skills, references and other information added to your CV or cover letter.</li>
                    <li>Documents you upload, including CVs and LinkedIn PDF files.</li>
                    <li>Job descriptions and other information submitted when using Job Match or AI-assisted features.</li>
                    <li>Account, subscription and transaction information.</li>
                    <li>Communications sent to our support team.</li>
                    <li>Technical information such as your IP address, browser, device and website usage information.</li>
                </ul>
                <p>Please avoid including unnecessary sensitive information in your CV or uploaded documents.</p>

                <h2>3. Why do we process your information?</h2>
                <p>We process your personal information to:</p>
                <ul>
                    <li>Create and manage your ApplyDocs ZA account.</li>
                    <li>Build, save, edit and export your CVs and cover letters.</li>
                    <li>Provide Job Match, AI-assisted improvements and other requested features.</li>
                    <li>Process payments and manage paid access.</li>
                    <li>Send account, payment, security and service-related communications.</li>
                    <li>Provide customer support.</li>
                    <li>Improve the security, functionality and performance of the platform.</li>
                    <li>Comply with legal, regulatory and financial obligations.</li>
                </ul>
                <p>Where marketing consent is required, promotional communications will only be sent with the appropriate permission. You may unsubscribe at any time.</p>

                <h2>4. Is providing this information compulsory?</h2>
                <p>Certain information, such as your email address, is required to create and manage an account. Information entered into a CV, cover letter or job-matching feature is provided voluntarily.</p>
                <p>If required information is not provided, we may be unable to create your account, process a payment or provide the requested service.</p>

                <h2>5. Who may receive your information?</h2>
                <p>We may share personal information with trusted service providers that assist us with:</p>
                <ul>
                    <li>Website and cloud hosting.</li>
                    <li>Payment processing.</li>
                    <li>Email delivery and customer support.</li>
                    <li>Document generation and storage.</li>
                    <li>Analytics, security and technical maintenance.</li>
                    <li>AI-assisted platform features.</li>
                </ul>
                <p>These providers may only process information for the services they provide to ApplyDocs ZA and must protect it appropriately.</p>
                <p>We do not sell your personal information. We may disclose information where required by law, a court order or a lawful request from a regulatory authority.</p>

                <h2>6. AI-assisted features</h2>
                <p>When you use an AI-assisted feature, the text you submit may be processed by a third-party technology provider to generate the requested improvement.</p>
                <p>ApplyDocs ZA’s AI features are intended to improve the wording of information supplied by you, not to invent qualifications, experience or achievements. You remain responsible for reviewing and confirming the accuracy of all generated content before using it.</p>
                <p>The development team must confirm whether user information is retained by the AI provider or used to train external AI models, and disclose this clearly.</p>

                <h2>7. Information processed outside South Africa</h2>
                <p>Some service providers may process or store information outside South Africa. Where this occurs, ApplyDocs ZA will take reasonable steps to ensure that the information receives an appropriate level of protection as required by POPIA.</p>

                <h2>8. How long do we retain information?</h2>
                <p>We retain personal information only for as long as it is reasonably required to provide our services, meet legal obligations, resolve disputes and maintain necessary business records.</p>
                <p>When information is no longer required, it will be deleted, destroyed or de-identified securely.</p>

                <h2>9. How do we protect your information?</h2>
                <p>ApplyDocs ZA uses reasonable technical and organisational safeguards to protect personal information against loss, unauthorised access, misuse, alteration or disclosure.</p>
                <p>However, no online service can guarantee absolute security. If a security compromise affects your personal information, we will take the steps required under POPIA, including notifying affected users and the Information Regulator where applicable.</p>

                <h2>10. Your rights</h2>
                <p>Subject to POPIA, you may:</p>
                <ul>
                    <li>Ask whether we hold personal information about you.</li>
                    <li>Request access to your personal information.</li>
                    <li>Request that inaccurate information be corrected or updated.</li>
                    <li>Request deletion of information where legally permitted.</li>
                    <li>Object to certain processing of your information.</li>
                    <li>Withdraw consent where processing is based on consent.</li>
                    <li>Lodge a complaint with the Information Regulator.</li>
                </ul>
                <p>Requests may be sent to <a href="mailto:info@applydocs.co.za">info@applydocs.co.za</a>. We may need to verify your identity before processing a request.</p>

                <h2>11. Information Regulator</h2>
                <p>If you believe your personal information has been processed unlawfully, you may contact the Information Regulator of South Africa:</p>
                <ul>
                    <li>Website: <a href="https://inforegulator.org.za" target="_blank" rel="noopener noreferrer">https://inforegulator.org.za</a></li>
                    <li>Email: <a href="mailto:enquiries@inforegulator.org.za">enquiries@inforegulator.org.za</a></li>
                    <li>Telephone: 010 023 5200</li>
                </ul>

                <h2>12. Changes to this notice</h2>
                <p>We may update this notice when our services, legal obligations or information-processing practices change. The latest version will be published on the ApplyDocs ZA website with its effective date.</p>
            </div>
        </div>
    </section>
@endsection
