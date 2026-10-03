<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Invoice {{ $invoice->invoice_number }}</title>
<style>
body{font-family:DejaVu Sans, sans-serif;color:#16233f;font-size:12px;margin:0;padding:24px}
.header{border-bottom:3px solid #b45309;padding-bottom:12px;margin-bottom:16px}
.header h1{font-size:22px;margin:0}
.header p{margin:4px 0 0;color:#48587c}
.meta{width:100%;margin-bottom:16px}
.meta td{padding:2px 0;vertical-align:top}
table.items{width:100%;border-collapse:collapse;margin:12px 0}
table.items th{background:#16233f;color:#fff;padding:8px;text-align:left;font-size:11px}
table.items td{border:1px solid #e7e1d2;padding:8px}
.totals{width:40%;margin-left:auto;border-collapse:collapse}
.totals td{padding:5px 8px;border:1px solid #e7e1d2}
.grand{background:#16233f;color:#fff;font-weight:bold}
.stamp{display:inline-block;border:2px solid #166534;color:#166534;font-weight:bold;padding:4px 14px;text-transform:uppercase;letter-spacing:2px}
.footer{margin-top:24px;color:#48587c;font-size:11px;border-top:1px solid #e7e1d2;padding-top:8px}
</style>
</head>
<body>
<div class="header">
<h1>Global Consultancy Education</h1>
<p>House 12, Road 5, Dhaka, Bangladesh · info@globalconsultancy.com</p>
</div>
<table class="meta"><tr>
<td><strong>Invoice:</strong> {{ $invoice->invoice_number }}<br><strong>Issue date:</strong> {{ $invoice->issue_date?->format('d M Y') }}<br><strong>Due date:</strong> {{ $invoice->due_date?->format('d M Y') ?? '—' }}<br><strong>Status:</strong> {{ $invoice->status }}</td>
<td><strong>Bill to:</strong><br>{{ $invoice->university->name ?? '—' }}<br>Candidate: {{ trim(($invoice->candidate->first_name ?? '').' '.($invoice->candidate->last_name ?? '')) ?: '—' }}<br>Application: {{ $invoice->application->uid ?? '—' }}</td>
</tr></table>
<table class="items">
<thead><tr><th>Description</th><th>Qty</th><th>Unit Price</th><th>Amount</th></tr></thead>
<tbody>
@forelse($invoice->items as $item)<tr><td>{{ $item->description }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price, 2) }}</td><td>{{ number_format($item->total, 2) }}</td></tr>@empty<tr><td colspan="4">Consultancy services as agreed.</td></tr>@endforelse
</tbody></table>
<table class="totals">
<tr><td>Subtotal</td><td>{{ number_format($invoice->subtotal, 2) }}</td></tr>
<tr><td>Tax ({{ $invoice->tax_percent }}%)</td><td>{{ number_format($invoice->tax_amount, 2) }}</td></tr>
<tr class="grand"><td>Total</td><td>{{ number_format($invoice->total, 2) }}</td></tr>
</table>
<p style="margin-top:16px"><span class="stamp">{{ $invoice->status }}</span></p>
@if($invoice->notes)<p><strong>Notes:</strong> {{ $invoice->notes }}</p>@endif
<div class="footer">Generated {{ now()->format('d M Y H:i') }} · Global Consultancy Education</div>
</body>
</html>
