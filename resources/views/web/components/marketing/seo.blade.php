@props([
    'title' => null,
    'description' => null,
    'keywords' => null,
    'canonical' => null,
    'ogImage' => null,
    'ogType' => 'website',
    'robots' => 'index, follow',
    'article' => null,
])

@php
    $siteName = config('app.name');
    $pageTitle = $title ? "{$title} | {$siteName}" : $siteName;
    $pageDescription = $description ?? 'Build professional, ATS-optimized CVs with ApplyDocs ZA using AI-powered writing assistance and beautiful templates.';
    $canonicalUrl = $canonical ?? url()->current();
    $ogImageUrl = $ogImage ?? asset('images/og-default.jpg');
    $ogTypeValue = $ogType ?: 'website';
    $articleMeta = is_array($article) ? $article : [];
@endphp

<title>{{ $pageTitle }}</title>
<meta name="description" content="{{ $pageDescription }}">
<meta name="robots" content="{{ $robots }}">
<link rel="canonical" href="{{ $canonicalUrl }}">

@if ($keywords)
    <meta name="keywords" content="{{ $keywords }}">
@endif

<meta property="og:type" content="{{ $ogTypeValue }}">
<meta property="og:url" content="{{ $canonicalUrl }}">
<meta property="og:title" content="{{ $pageTitle }}">
<meta property="og:description" content="{{ $pageDescription }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:image" content="{{ $ogImageUrl }}">

@if (! empty($articleMeta['published_time']))
    <meta property="article:published_time" content="{{ $articleMeta['published_time'] }}">
@endif
@if (! empty($articleMeta['modified_time']))
    <meta property="article:modified_time" content="{{ $articleMeta['modified_time'] }}">
@endif
@if (! empty($articleMeta['author']))
    <meta property="article:author" content="{{ $articleMeta['author'] }}">
@endif
@if (! empty($articleMeta['publisher']))
    <meta property="article:publisher" content="{{ $articleMeta['publisher'] }}">
@endif

<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $pageTitle }}">
<meta name="twitter:description" content="{{ $pageDescription }}">
<meta name="twitter:image" content="{{ $ogImageUrl }}">
<meta name="twitter:url" content="{{ $canonicalUrl }}">
