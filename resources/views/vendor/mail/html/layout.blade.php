<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<title>{{ config('app.name') }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta name="color-scheme" content="light">
<meta name="supported-color-schemes" content="light">
<style>
/* Base styles */
body {
    margin: 0;
    padding: 0;
    width: 100%;
    background-color: #f9fafb;
    -webkit-text-size-adjust: none;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
}

table {
    border-collapse: collapse;
}

td {
    vertical-align: top;
}

/* Wrapper */
.wrapper {
    width: 100%;
    background-color: #f9fafb;
    padding: 40px 0;
}

/* Content */
.content {
    width: 100%;
    max-width: 600px;
}

/* Body */
.body {
    background-color: #f9fafb;
    border: hidden !important;
    width: 100%;
}

/* Inner body - the white card */
.inner-body {
    background-color: #ffffff;
    border-radius: 16px;
    border: 1px solid #e5e7eb;
    box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
    margin: 0 auto;
    width: 570px;
    overflow: hidden;
}

/* Content cell */
.content-cell {
    padding: 32px 40px;
}

/* Typography */
h1 {
    color: #111827;
    font-size: 24px;
    font-weight: 700;
    margin: 0 0 16px;
    line-height: 1.3;
}

p {
    color: #4b5563;
    font-size: 16px;
    line-height: 1.6;
    margin: 0 0 16px;
}

a {
    color: #2563eb;
    text-decoration: none;
}

a:hover {
    text-decoration: underline;
}

/* Button */
.button {
    display: inline-block;
    padding: 14px 28px;
    font-size: 15px;
    font-weight: 600;
    text-decoration: none;
    border-radius: 10px;
    transition: background-color 0.2s;
}

.button-primary {
    background-color: #2563eb;
    color: #ffffff !important;
}

.button-success {
    background-color: #10b981;
    color: #ffffff !important;
}

.button-error {
    background-color: #ef4444;
    color: #ffffff !important;
}

/* Action table */
.action {
    width: 100%;
    margin: 24px 0;
    padding: 0;
    text-align: center;
}

/* Panel */
.panel {
    background-color: #f3f4f6;
    border-radius: 10px;
    margin: 20px 0;
    padding: 20px;
}

.panel-content {
    color: #4b5563;
}

.panel-item {
    padding: 0;
}

/* Subcopy */
.subcopy {
    border-top: 1px solid #e5e7eb;
    margin-top: 24px;
    padding-top: 24px;
}

.subcopy p {
    font-size: 13px;
    color: #6b7280;
    line-height: 1.5;
}

/* Footer */
.footer {
    margin-top: 32px;
    text-align: center;
    width: 570px;
}

.footer p {
    color: #9ca3af;
    font-size: 13px;
    line-height: 1.5;
}

/* Table styles for data */
.table {
    width: 100%;
    border-collapse: collapse;
    margin: 20px 0;
}

.table th {
    background-color: #f9fafb;
    border-bottom: 2px solid #e5e7eb;
    color: #374151;
    font-size: 13px;
    font-weight: 600;
    padding: 12px;
    text-align: left;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.table td {
    border-bottom: 1px solid #e5e7eb;
    color: #4b5563;
    font-size: 15px;
    padding: 12px;
}

/* Responsive */
@media only screen and (max-width: 600px) {
    .wrapper {
        padding: 20px 16px;
    }
    
    .inner-body {
        width: 100% !important;
        border-radius: 12px;
    }
    
    .content-cell {
        padding: 24px 20px;
    }

    .footer {
        width: 100% !important;
    }
    
    h1 {
        font-size: 20px;
    }
    
    p {
        font-size: 15px;
    }
}

@media only screen and (max-width: 500px) {
    .button {
        width: 100% !important;
        text-align: center;
    }
}
</style>
{{ $head ?? '' }}
</head>
<body>

<table class="wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation">
<tr>
<td align="center">
<table class="content" width="100%" cellpadding="0" cellspacing="0" role="presentation">
{{ $header ?? '' }}

<!-- Email Body -->
<tr>
<td class="body" width="100%" cellpadding="0" cellspacing="0" style="border: hidden !important;">
<table class="inner-body" align="center" width="570" cellpadding="0" cellspacing="0" role="presentation">
<!-- Body content -->
<tr>
<td class="content-cell">
{{ Illuminate\Mail\Markdown::parse($slot) }}

{{ $subcopy ?? '' }}
</td>
</tr>
</table>
</td>
</tr>

{{ $footer ?? '' }}
</table>
</td>
</tr>
</table>
</body>
</html>
