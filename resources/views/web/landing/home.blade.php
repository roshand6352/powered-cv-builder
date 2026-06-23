@extends('web.layouts.master')

@section('content')
    @include('web.landing.sections.hero')
    @include('web.landing.sections.stats-bar')
    @include('web.landing.sections.pain-points')
    @include('web.landing.sections.two-tools')
    @include('web.landing.sections.how-it-works')
    @include('web.landing.sections.differentiators')
    @include('web.landing.sections.pricing')
    @include('web.landing.sections.testimonials')
    @include('web.landing.sections.faq')
    @include('web.landing.sections.final-cta')
@endsection
