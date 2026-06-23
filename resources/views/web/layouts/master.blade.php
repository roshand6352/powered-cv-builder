<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    <x-marketing.seo
        :title="$seo['title'] ?? null"
        :description="$seo['description'] ?? null"
        :keywords="$seo['keywords'] ?? null"
        :canonical="$seo['canonical'] ?? null"
    />

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;1,9..40,400&display=swap" rel="stylesheet">

    @include('web.partials.css')
    @stack('head')
</head>
<body>
    @include('web.partials.header')

    <main>
        @yield('content')
    </main>

    @include('web.partials.footer')
    @include('web.partials.scripts')
    @stack('scripts')
</body>
</html>
