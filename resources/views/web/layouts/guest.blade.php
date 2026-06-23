<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    <title>{{ config('app.name', 'ApplyDocs ZA') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @include('web.partials.css')
</head>
<body>
    <div class="auth-wrap">
        <x-marketing.logo class="auth-logo" />

        <div class="auth-card">
            {{ $slot }}
        </div>

        <p class="auth-back">
            <a href="{{ route('home') }}">&larr; Back to home</a>
        </p>
    </div>
</body>
</html>
