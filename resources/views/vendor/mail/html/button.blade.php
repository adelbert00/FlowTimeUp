@props([
    'url',
    'color' => 'primary',
    'align' => 'center',
])
@php
$colors = [
    'primary' => '#2563eb',
    'success' => '#10b981',
    'error' => '#ef4444',
    'blue' => '#2563eb',
    'green' => '#10b981',
    'red' => '#ef4444',
];
$bgColor = $colors[$color] ?? '#2563eb';
@endphp
<table class="action" align="{{ $align }}" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table width="100%" border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="{{ $align }}">
<table border="0" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td>
<!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" href="{{ $url }}" style="height:48px;v-text-anchor:middle;width:200px;" arcsize="21%" fillcolor="{{ $bgColor }}">
<w:anchorlock/>
<center style="color:#ffffff;font-size:15px;font-weight:600;">{{ $slot }}</center>
</v:roundrect>
<![endif]-->
<!--[if !mso]><!-->
<a href="{{ $url }}" class="button button-{{ $color }}" target="_blank" rel="noopener" style="display: inline-block; padding: 14px 32px; font-size: 15px; font-weight: 600; text-decoration: none; color: #ffffff; background-color: {{ $bgColor }}; border-radius: 10px; text-align: center; min-width: 120px;">{{ $slot }}</a>
<!--<![endif]-->
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
