@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'ogImage' => null,
    'robots' => 'index, follow',
])

@php
    $siteName = config('app.name');
    $pageTitle = $title ? "{$title} | {$siteName}" : $siteName;
    $pageDescription = $description ?? 'Build professional, ATS-optimized CVs with ApplyDocs ZA using AI-powered writing assistance and beautiful templates.';
    $canonicalUrl = $canonical ?? url()->current();
    $ogImageUrl = $ogImage ?? asset('images/og-default.jpg');
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if ($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif

<meta property="og:type" content="website">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:site_name" content="{{ $siteName }}">

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
