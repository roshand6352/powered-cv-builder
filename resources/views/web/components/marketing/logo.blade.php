@props(['href' => null])

<a href="{{ $href ?? route('home') }}" {{ $attributes->merge(['class' => 'site-logo']) }}>
    <img
        src="{{ asset(config('marketing.brand.logo', 'images/LOGO-02.png')) }}"
        alt="{{ config('marketing.brand.name', config('app.name')) }}"
    >
</a>
