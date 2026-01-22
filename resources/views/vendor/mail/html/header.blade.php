@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block;">
@if (trim($slot) === 'Laravel')
<table cellpadding="0" cellspacing="0" style="margin: 0 auto;">
<tr>
<td style="padding-right: 10px; vertical-align: middle;">
<div style="width: 40px; height: 40px; border-radius: 10px; background: linear-gradient(135deg, #6366f1 0%, #a855f7 100%); display: flex; align-items: center; justify-content: center;">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
<circle cx="12" cy="12" r="10"></circle>
<polyline points="12 6 12 12 16 14"></polyline>
</svg>
</div>
</td>
<td style="vertical-align: middle;">
<span style="color: #1f2937; font-size: 22px; font-weight: bold;">FlowTime</span>
</td>
</tr>
</table>
@else
{{ $slot }}
@endif
</a>
</td>
</tr>
