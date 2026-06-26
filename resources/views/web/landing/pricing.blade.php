@extends('web.layouts.marketing')

@section('content')
    @include('web.landing.partials.page-hero', [
        'title' => 'Pricing',
        'description' => 'Choose the plan that fits your job search. Pay securely with PayFast.',
    ])

    @include('web.landing.partials.pricing-plans', [
        'plans' => $plans,
        'currentPlan' => $currentPlan ?? null,
        'showAllPlansButton' => false,
        'showCurrentPlanBadge' => true,
    ])

    <section class="page-section page-section--white">
        <div class="container">
            <div class="section-intro">
                <h2>What's included</h2>
                <p>All paid plans remove the PDF watermark and increase AI credits.</p>
            </div>

            <div class="data-table-wrap">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Feature</th>
                            <th>Free</th>
                            <th class="col-accent">Paid plans</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ([
                            ['PDF watermark', 'Yes', 'Removed'],
                            ['AI credits', '10', '50 – Unlimited'],
                            ['Templates', 'All', 'All'],
                            ['PDF export', 'Yes', 'Yes'],
                            ['Cover letter AI', 'Limited', 'Full access'],
                            ['Plan expiry', 'Never', 'Based on plan duration'],
                        ] as $row)
                            <tr>
                                <td>{{ $row[0] }}</td>
                                <td>{{ $row[1] }}</td>
                                <td>{{ $row[2] }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </section>

    @include('web.landing.partials.faq')
    @include('web.landing.partials.cta')
@endsection
