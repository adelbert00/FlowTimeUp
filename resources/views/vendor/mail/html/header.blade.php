@props(['url'])
<tr>
<td style="padding: 32px 0 24px; text-align: center;">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<table cellpadding="0" cellspacing="0" style="margin: 0 auto;">
<tr>
<td style="padding-right: 12px; vertical-align: middle;">
<!--[if mso]>
<v:roundrect xmlns:v="urn:schemas-microsoft-com:vml" xmlns:w="urn:schemas-microsoft-com:office:word" style="height:44px;v-text-anchor:middle;width:44px;" arcsize="23%" fillcolor="#2563eb">
<w:anchorlock/>
<center style="color:#ffffff;font-size:20px;font-weight:bold;">F</center>
</v:roundrect>
<![endif]-->
<!--[if !mso]><!-->
<div style="width: 44px; height: 44px; border-radius: 10px; background: linear-gradient(135deg, #2563eb 0%, #3b82f6 100%); display: inline-flex; align-items: center; justify-content: center; vertical-align: middle;">
<svg xmlns="http://www.w3.org/2000/svg" width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;">
<circle cx="12" cy="12" r="10"></circle>
<polyline points="12 6 12 12 16 14"></polyline>
</svg>
</div>
<!--<![endif]-->
</td>
<td style="vertical-align: middle;">
<span style="color: #111827; font-size: 24px; font-weight: 700; letter-spacing: -0.025em;">{{ $slot }}</span>
</td>
</tr>
</table>
</a>
</td>
</tr>
