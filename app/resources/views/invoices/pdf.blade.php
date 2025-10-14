<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice #{{ $invoice->number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        .header-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            text-align: right;
        }
        .logo {
            max-width: 200px;
            max-height: 80px;
        }
        .company-details {
            margin-top: 10px;
            font-size: 12px;
            line-height: 1.6;
        }
        .invoice-title {
            font-size: 32px;
            font-weight: bold;
            margin-bottom: 10px;
        }
        .invoice-details {
            font-size: 12px;
            line-height: 1.6;
        }
        .section-title {
            font-size: 16px;
            font-weight: bold;
            margin: 30px 0 15px 0;
            padding-bottom: 5px;
            border-bottom: 2px solid #333;
        }
        .bill-to {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        thead th {
            background-color: #f5f5f5;
            padding: 10px;
            text-align: left;
            font-weight: bold;
            border-bottom: 2px solid #ddd;
        }
        tbody td {
            padding: 10px;
            border-bottom: 1px solid #eee;
        }
        .text-right {
            text-align: right;
        }
        .totals {
            float: right;
            width: 300px;
        }
        .totals table {
            margin-bottom: 0;
        }
        .totals tbody td {
            border-bottom: none;
            padding: 5px 10px;
        }
        .total-row {
            font-weight: bold;
            font-size: 16px;
            background-color: #f5f5f5;
        }
        .footer {
            margin-top: 50px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            font-size: 12px;
            color: #666;
            text-align: center;
        }
        .qr-code {
            text-align: center;
            margin: 20px 0;
        }
        .status-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 3px;
            font-size: 12px;
            font-weight: bold;
        }
        .status-paid {
            background-color: #d4edda;
            color: #155724;
        }
        .status-open {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-overdue {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if($company['logo_url'])
                <img src="{{ $company['logo_url'] }}" alt="{{ $company['name'] }}" class="logo">
            @else
                <h1 style="margin: 0;">{{ $company['name'] }}</h1>
            @endif
            <div class="company-details">
                @if($company['address'])<div>{{ $company['address'] }}</div>@endif
                @if($company['phone'])<div>Phone: {{ $company['phone'] }}</div>@endif
                @if($company['email'])<div>Email: {{ $company['email'] }}</div>@endif
                @if($company['tax_id'])<div>Tax ID: {{ $company['tax_id'] }}</div>@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">INVOICE</div>
            <div class="invoice-details">
                <div><strong>Invoice #:</strong> {{ $invoice->number }}</div>
                <div><strong>Issue Date:</strong> {{ $invoice->issue_date->format('M d, Y') }}</div>
                <div><strong>Due Date:</strong> {{ $invoice->due_date->format('M d, Y') }}</div>
                <div>
                    <strong>Status:</strong> 
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ strtoupper($invoice->status) }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="section-title">Bill To</div>
    <div class="bill-to">
        <strong>{{ $invoice->client->first_name }} {{ $invoice->client->last_name }}</strong><br>
        @if($invoice->client->company_name)
            {{ $invoice->client->company_name }}<br>
        @endif
        @if($invoice->client->address)
            {{ $invoice->client->address }}<br>
        @endif
        @if($invoice->client->city || $invoice->client->state || $invoice->client->postal_code)
            {{ $invoice->client->city }}@if($invoice->client->state), {{ $invoice->client->state }}@endif {{ $invoice->client->postal_code }}<br>
        @endif
        @if($invoice->client->country)
            {{ $invoice->client->country }}<br>
        @endif
        @if($invoice->client->tax_id)
            Tax ID: {{ $invoice->client->tax_id }}<br>
        @endif
    </div>

    <div class="section-title">Invoice Items</div>
    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="text-right">Quantity</th>
                <th class="text-right">Unit Price</th>
                <th class="text-right">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->invoice_items as $item)
            <tr>
                <td>{{ $item->description }}</td>
                <td class="text-right">{{ $item->quantity }}</td>
                <td class="text-right">{{ $invoice->currency }} {{ number_format($item->unit_amount, 2) }}</td>
                <td class="text-right">{{ $invoice->currency }} {{ number_format($item->amount, 2) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals">
        <table>
            <tbody>
                <tr>
                    <td>Subtotal:</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->subtotal, 2) }}</td>
                </tr>
                @if($invoice->tax > 0)
                <tr>
                    <td>Tax:</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->tax, 2) }}</td>
                </tr>
                @endif
                @if($invoice->discount > 0)
                <tr>
                    <td>Discount:</td>
                    <td class="text-right">-{{ $invoice->currency }} {{ number_format($invoice->discount, 2) }}</td>
                </tr>
                @endif
                <tr class="total-row">
                    <td>Total:</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->total, 2) }}</td>
                </tr>
                @if($invoice->paid > 0)
                <tr>
                    <td>Paid:</td>
                    <td class="text-right">-{{ $invoice->currency }} {{ number_format($invoice->paid, 2) }}</td>
                </tr>
                <tr class="total-row">
                    <td>Balance Due:</td>
                    <td class="text-right">{{ $invoice->currency }} {{ number_format($invoice->total - $invoice->paid, 2) }}</td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <div style="clear: both;"></div>

    @if($invoice->notes)
    <div class="section-title">Notes</div>
    <div style="margin-bottom: 30px;">
        {{ $invoice->notes }}
    </div>
    @endif

    <div class="qr-code">
        <p style="margin: 10px 0; font-size: 12px; color: #666;">
            Scan QR code to view invoice online
        </p>
        <img src="https://api.qrserver.com/v1/create-qr-code/?size=150x150&data={{ urlencode(route('client.invoices.show', $invoice->id)) }}" 
             alt="QR Code" style="max-width: 150px;">
    </div>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>This is a computer-generated invoice and does not require a signature.</p>
    </div>
</body>
</html>
