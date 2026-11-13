@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'About Us',
        'description' => 'We believe everyone deserves a CV that opens doors, not one that gets filtered out.',
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                <h2>Our mission</h2>
                <p>
                    {{ config('app.name') }} was built to solve a simple problem: great candidates often lose opportunities because their CVs don't communicate their value clearly or don't survive ATS filters.
                </p>
                <p>
                    We combine recruiter-approved templates with AI writing tools so you can focus on your career story while we handle structure, keywords, and polish.
                </p>

                <h2>What we do</h2>
                <p>
                    {{ config('app.name') }} brings two tools together in one account: a CV Builder and a Cover Letter Generator. The CV Builder guides you through simple questions about your experience, skills and education, then structures your answers into a professional CV using ATS-friendly templates. You can preview your CV as it takes shape and download it as a PDF.
                </p>
                <p>
                    The Cover Letter Generator lets you paste the job description, answer a few prompts about why you are a good fit, and create a cover letter tailored to that specific role.
                </p>

                <h2>How our AI tools help</h2>
                <ul>
                    <li><strong>CV writing:</strong> AI-assisted writing helps you improve sections of your CV and present your experience in clear, professional language.</li>
                    <li><strong>Cover letters:</strong> AI uses the job description you paste to tailor your letter to the role and polish your answers into confident, professional wording.</li>
                    <li><strong>You stay in control:</strong> AI suggests. You decide what goes into your CV and cover letter.</li>
                </ul>
                <p>
                    You can create, edit and download a CV for free. Free downloads include the {{ config('app.name') }} watermark. AI writing, watermark-free downloads and other premium features are available on paid plans.
                </p>

                <h2>What we stand for</h2>
                <ul>
                    <li><strong>Accessibility:</strong> Professional CV tools shouldn't be expensive or complicated.</li>
                    <li><strong>Transparency:</strong> You always control your content. AI suggests; you decide.</li>
                    <li><strong>Privacy:</strong> Your career data stays yours. We never sell personal information.</li>
                </ul>

                <h2>Built for South African job seekers.</h2>
                <p>
                    {{ config('app.name') }} is made for job seekers in South Africa. Our plans are priced in rand, paid once-off through PayFast and never renew automatically, and we handle your personal information in line with POPIA. Whether you are writing your first CV or applying for your next role, our guided prompts and templates help you present yourself as the professional you are.
                </p>
            </div>
        </div>
    </section>
    @include('web.landing.partials.cta')
@endsection
