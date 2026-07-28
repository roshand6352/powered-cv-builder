@php
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
    $logoUrl = asset('images/LOGO-02.png');
    $homeUrl = config('app.url', url('/'));
@endphp
<tr>
<td align="center" bgcolor="#172d40" style="background-color: #172d40; padding: 28px 24px; text-align: center;">
<a href="{{ $homeUrl }}" style="display: inline-block; text-decoration: none; border: 0; outline: none;">
<img src="{{ $logoUrl }}" width="180" height="auto" alt="{{ $brandName }}" style="display: block; margin: 0 auto; border: 0; outline: none; text-decoration: none; height: auto; max-width: 180px; width: 180px;">
</a>
</td>
</tr>
