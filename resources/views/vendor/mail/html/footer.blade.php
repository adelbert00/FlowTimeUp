@props(['url'])
<tr>
<td>
<table class="footer" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td style="padding: 32px 40px; text-align: center;">
{{ Illuminate\Mail\Markdown::parse($slot) }}

<table cellpadding="0" cellspacing="0" style="margin: 24px auto 0;">
<tr>
<td style="padding: 0 8px;">
<a href="{{ config('app.url') }}" style="color: #6b7280; font-size: 13px; text-decoration: none;">
Home
</a>
</td>
<td style="color: #d1d5db; font-size: 13px;">|</td>
<td style="padding: 0 8px;">
<a href="{{ config('app.url') }}/tasks" style="color: #6b7280; font-size: 13px; text-decoration: none;">
Tasks
</a>
</td>
<td style="color: #d1d5db; font-size: 13px;">|</td>
<td style="padding: 0 8px;">
<a href="{{ config('app.url') }}/profile" style="color: #6b7280; font-size: 13px; text-decoration: none;">
Profile
</a>
</td>
</tr>
</table>

<p style="margin-top: 24px; color: #9ca3af; font-size: 12px; line-height: 1.5;">
&copy; {{ date('Y') }} FlowTime. All rights reserved.
</p>
</td>
</tr>
</table>
</td>
</tr>
