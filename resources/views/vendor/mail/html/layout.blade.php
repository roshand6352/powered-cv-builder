<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
{!! $head ?? '' !!}
</head>
<body style="margin: 0; padding: 0; width: 100% !important; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; background-color: #faf8f5; font-family: Arial, Helvetica, sans-serif;">

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0; padding: 0; width: 100%; background-color: #faf8f5;">
<tr>
<td align="center" style="padding: 0;">
<table class="content" width="600" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; max-width: 600px; margin: 0 auto;">
{!! $header ?? '' !!}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" bgcolor="#ffffff" style="border: hidden !important; background-color: #ffffff; width: 100%;">
<table class="inner-body" align="center" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="width: 100%; background-color: #ffffff; margin: 0 auto;">
<!-- Body content -->
<tr>
<td class="content-cell" style="padding: 32px 28px; font-family: Arial, Helvetica, sans-serif; color: #172d40;">
{!! Illuminate\Mail\Markdown::parse($slot) !!}

{!! $subcopy ?? '' !!}
</td>
</tr>
</table>
</td>
</tr>

{!! $footer ?? '' !!}
</table>
</td>
</tr>
</table>
</body>
</html>
