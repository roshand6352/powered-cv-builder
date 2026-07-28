<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @include('web.partials.favicon')

    <title>Redirecting to PayFast | {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@400;600;700;800&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css'])
</head>
<body class="bg-slate-100 font-sans antialiased" style="font-family:'DM Sans',sans-serif">
    <div class="flex min-h-screen items-center justify-center p-6">
        <div class="w-full max-w-md rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm">
            <div class="mx-auto mb-4 h-10 w-10 animate-spin rounded-full border-4 border-brand-200 border-t-brand-600"></div>
            <h1 class="text-lg font-semibold text-slate-900">Redirecting to PayFast</h1>
            <p class="mt-2 text-sm text-slate-500">
                You are being redirected to complete payment for the
                <strong>{{ $plan->name }}</strong> plan
                ({{ ($plan->currency_symbol ?: 'R').' '.number_format((float) ($formData['amount'] ?? $plan->price), 2, '.', ',') }}).
            </p>
        </div>
    </div>

    <form id="payfast-form" method="POST" action="{{ $processUrl }}">
        @foreach ($formData as $key => $value)
            <input type="hidden" name="{{ $key }}" value="{{ $value }}">
        @endforeach
    </form>

    <script>
        document.getElementById('payfast-form').submit();
    </script>
</body>
</html>
