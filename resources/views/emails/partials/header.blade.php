@php
    use App\Support\Mail\MailLogo;

    $brandName = MailLogo::alt();
    $logoUrl = MailLogo::url();
    $homeUrl = config('app.url', url('/'));
    // LOGO-19 is 4500×827 — keep that ratio at the existing 180px email width.
    $logoWidth = 180;
    $logoHeight = 33;
@endphp
<tr>
<td align="center" bgcolor="#172d40" style="background-color: #172d40; padding: 28px 24px; text-align: center;">
<a href="{{ $homeUrl }}" style="display: inline-block; text-decoration: none; border: 0; outline: none;">
<img
    src="{{ $logoUrl }}"
    width="{{ $logoWidth }}"
    height="{{ $logoHeight }}"
    alt="{{ $brandName }}"
    title="{{ $brandName }}"
    style="display: block; margin: 0 auto; border: 0; outline: none; text-decoration: none; height: {{ $logoHeight }}px; max-height: {{ $logoHeight }}px; max-width: {{ $logoWidth }}px; width: {{ $logoWidth }}px;"
>
</a>
</td>
</tr>
