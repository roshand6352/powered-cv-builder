@props(['title' => null])
@php
    $pageTitle = $title ?: config('marketing.brand.name', config('app.name', 'ApplyDocs ZA'));
@endphp
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta name="color-scheme" content="light" />
<meta name="supported-color-schemes" content="light" />
<title>{{ $pageTitle }}</title>
</head>
<body style="margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #faf8f5; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 0; padding: 0; width: 100%; background-color: #faf8f5;">
<tr>
<td align="center" style="padding: 0;">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" border="0" style="width: 100%; max-width: 600px; margin: 0 auto; background-color: #ffffff;">
@include('emails.partials.header')
<tr>
<td bgcolor="#ffffff" style="background-color: #ffffff; padding: 32px 28px; font-family: Arial, Helvetica, sans-serif; color: #172d40;">
{{ $slot }}
</td>
</tr>
@include('emails.partials.footer')
</table>
</td>
</tr>
</table>
</body>
</html>
