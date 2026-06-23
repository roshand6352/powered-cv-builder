@props(['title' => null])

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @include('web.partials.favicon')

    <title>{{ $title ? "{$title} | " : '' }}{{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="overflow-x-hidden font-sans antialiased text-body" style="font-family:'DM Sans',sans-serif">
    <div x-data="{ sidebarOpen: false }" class="min-h-screen bg-cream lg:flex">
        <div
            x-show="sidebarOpen"
            x-cloak
            x-transition:enter="transition-opacity ease-linear duration-200"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-200"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-40 bg-navy/50 lg:hidden"
            @click="sidebarOpen = false"
        ></div>

        <x-dashboard.sidebar />

        <div class="flex min-h-screen min-w-0 flex-1 flex-col">
            <x-dashboard.navbar>
                @isset($header)
                    <x-slot:header>{{ $header }}</x-slot:header>
                @endisset
            </x-dashboard.navbar>

            <main class="mx-auto w-full max-w-7xl flex-1 p-4 sm:p-6 lg:p-8">
                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
