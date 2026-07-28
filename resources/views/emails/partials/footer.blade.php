@php
    $brandName = config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
    $supportUrl = url('/contact');
@endphp
<tr>
<td align="center" bgcolor="#faf8f5" style="background-color: #faf8f5; padding: 28px 24px; text-align: center;">
<p style="margin: 0 0 8px 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 18px; color: #5c6b7a; text-align: center;">
&copy; {{ date('Y') }} {{ $brandName }}. {{ __('All rights reserved.') }}
</p>
<p style="margin: 0; padding: 0; font-family: Arial, Helvetica, sans-serif; font-size: 12px; line-height: 18px; color: #5c6b7a; text-align: center;">
<a href="{{ $supportUrl }}" style="color: #c1623d; text-decoration: underline;">{{ __('Contact support') }}</a>
</p>
</td>
</tr>
