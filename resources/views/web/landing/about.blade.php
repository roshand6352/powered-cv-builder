@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'About Us',
        'description' => 'We believe everyone deserves a CV that opens doors — not one that gets filtered out.',
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="prose-content">
                <h2>Our mission</h2>
                <p>
                    {{ config('app.name') }} was built to solve a simple problem: great candidates often lose opportunities because their CVs don't communicate their value clearly — or don't survive ATS filters.
                </p>
                <p>
                    We combine recruiter-approved templates with AI writing tools so you can focus on your career story while we handle structure, keywords, and polish.
                </p>

                <h2>What we stand for</h2>
                <ul>
                    <li><strong>Accessibility</strong> — Professional CV tools shouldn't be expensive or complicated.</li>
                    <li><strong>Transparency</strong> — You always control your content. AI suggests; you decide.</li>
                    <li><strong>Privacy</strong> — Your career data stays yours. We never sell personal information.</li>
                </ul>

                <h2>Built for job seekers everywhere</h2>
                <p>
                    From recent graduates to senior executives, our platform helps professionals across industries create CVs that get noticed. We're constantly adding templates, AI capabilities, and integrations based on user feedback.
                </p>
            </div>
        </div>
    </section>

    @include('web.landing.partials.cta')
@endsection
