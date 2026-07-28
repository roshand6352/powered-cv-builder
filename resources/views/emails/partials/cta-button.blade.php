@php
    $align = $align ?? 'center';
    $label = $label ?? __('Continue');
    $url = $url ?? '#';
@endphp
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin: 28px 0;">
<tr>
<td align="{{ $align }}" style="text-align: {{ $align }};">
<table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin: 0 auto;">
<tr>
<td align="center" bgcolor="#c1623d" style="background-color: #c1623d; border-radius: 6px;">
<a href="{{ $url }}" target="_blank" rel="noopener" style="display: inline-block; padding: 14px 28px; font-family: Arial, Helvetica, sans-serif; font-size: 16px; font-weight: bold; line-height: 20px; color: #ffffff; text-decoration: none; border-radius: 6px; background-color: #c1623d;">
{{ $label }}
</a>
</td>
</tr>
</table>
</td>
</tr>
</table>
