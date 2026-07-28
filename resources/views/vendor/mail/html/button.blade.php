@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation" style="margin: 28px 0; width: 100%;">
<tr>
<td align="{{ $align }}" style="text-align: {{ $align }};">
<table border="0" cellpadding="0" cellspacing="0" role="presentation" style="margin: 0 auto;">
<tr>
<td align="center" bgcolor="#c1623d" style="background-color: #c1623d; border-radius: 6px;">
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener" style="display: inline-block; padding: 14px 28px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; line-height: 20px; color: #ffffff !important; text-decoration: none; border-radius: 6px; background-color: #c1623d;">{!! $slot !!}</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
