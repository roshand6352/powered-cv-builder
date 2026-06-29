@php
    $sourceLogo = asset(config('marketing.brand.favicon', 'LOGO-10.png'));
    $faviconPng = asset('favicon.png');
    $faviconIco = asset('favicon.ico');
@endphp

<link rel="icon" type="image/png" sizes="512x512" href="{{ $faviconPng }}">
<link rel="icon" type="image/x-icon" href="{{ $faviconIco }}">
<link rel="shortcut icon" href="{{ $faviconIco }}">
<link rel="apple-touch-icon" href="{{ $sourceLogo }}">
