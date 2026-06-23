@php
    $favicon = asset(config('marketing.brand.favicon', 'LOGO-10.png'));
@endphp

<link rel="icon" type="image/png" href="{{ $favicon }}">
<link rel="shortcut icon" type="image/png" href="{{ $favicon }}">
<link rel="apple-touch-icon" href="{{ $favicon }}">
