<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        @page {
            margin: 25pt 30pt;
        }
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #09090b;
            font-size: 10px;
            line-height: 1.4;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .header-table td {
            vertical-align: top;
        }
        .business-title {
            font-size: 15px;
            font-weight: bold;
            color: #09090b;
        }
        .invoice-title {
            font-size: 22px;
            font-weight: 900;
            color: #09090b;
            text-align: right;
            margin: 0;
            letter-spacing: 1px;
        }
        .invoice-number {
            font-size: 13px;
            font-weight: bold;
            color: #09090b;
            text-align: right;
            margin-top: 4px;
        }
        .status-badge {
            display: inline-block;
            padding: 3px 8px;
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            border-radius: 10px;
            margin-top: 6px;
        }
        .status-draft { background-color: #f4f4f5; color: #3f3f46; border: 1px solid #d4d4d8; }
        .status-unpaid { background-color: #f4f4f5; color: #09090b; border: 1px solid #09090b; font-weight: bold; }
        .status-paid { background-color: #09090b; color: #ffffff; }
        .status-overdue { background-color: #fff1f2; color: #9f1239; border: 1px solid #fecdd3; }

        .meta-table {
            margin-top: 20px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e4e4e7;
        }
        .meta-title {
            font-size: 8px;
            font-weight: bold;
            text-transform: uppercase;
            color: #71717a;
            letter-spacing: 0.5px;
        }
        .customer-name {
            font-size: 12px;
            font-weight: bold;
            color: #09090b;
            margin-top: 2px;
        }

        .items-table {
            margin-top: 20px;
        }
        .items-table th {
            border-bottom: 2px solid #09090b;
            padding: 8px 4px;
            font-size: 9px;
            font-weight: 900;
            text-transform: uppercase;
            color: #09090b;
            text-align: left;
        }
        .items-table td {
            padding: 8px 4px;
            border-bottom: 1px solid #f4f4f5;
        }
        .item-name {
            font-weight: bold;
            color: #09090b;
            font-size: 11px;
        }
        .item-desc {
            font-size: 9px;
            color: #71717a;
        }

        .summary-table {
            margin-top: 20px;
        }
        .summary-table td {
            vertical-align: top;
        }
        .total-row {
            font-size: 13px;
            font-weight: 900;
            color: #09090b;
            border-top: 2px solid #09090b;
            padding-top: 6px;
        }
        .bank-box {
            background-color: #fafafa;
            border: 1px solid #e4e4e7;
            padding: 10px;
            border-radius: 6px;
            font-size: 9px;
            color: #27272a;
        }
        .notes-section {
            margin-top: 25px;
            padding-top: 12px;
            border-top: 1px solid #e4e4e7;
        }

        .watermark-container {
            position: fixed;
            top: 16%;
            left: 2%;
            width: 96%;
            text-align: center;
            z-index: 1000;
        }
        .watermark-img {
            width: 94%;
            max-width: 680px;
            height: auto;
            opacity: 0.45;
        }
    </style>
</head>
<body>

    @if(!empty($stampBase64))
        <!-- Transparent PNG Watermark Stamp (45% Opacity) -->
        <div class="watermark-container">
            <img src="{{ $stampBase64 }}" class="watermark-img">
        </div>
    @endif

    <!-- Header Table -->
    <table class="header-table">
        <tr>
            <td style="width: 60%;">
                @if(!empty($logoBase64))
                    <img src="{{ $logoBase64 }}" style="max-height: 45px; margin-bottom: 8px;">
                @endif
                <div class="business-title">{{ $settings['business_name'] ?? 'Nama Bisnis' }}</div>
                <div style="color: #71717a; margin-top: 3px; white-space: pre-line;">{{ $settings['address'] ?? '' }}</div>
                <div style="color: #71717a; margin-top: 2px;">{{ $settings['phone'] ?? '' }} {{ !empty($settings['email']) ? '• ' . $settings['email'] : '' }}</div>
            </td>
            <td style="width: 40%; text-align: right;">
                <div class="invoice-title">INVOICE</div>
                <div class="invoice-number">{{ $invoice->invoice_number }}</div>
                <div>
                    <span class="status-badge status-{{ $invoice->status }}">
                        {{ $invoice->status_label }}
                    </span>
                </div>
            </td>
        </tr>
    </table>

    <!-- Customer & Dates Table -->
    <table class="meta-table">
        <tr>
            <td style="width: 55%; vertical-align: top;">
                <div class="meta-title">Bill To (Ditujukan Kepada):</div>
                @if($invoice->customer)
                    <div class="customer-name">{{ $invoice->customer->name }}</div>
                    <div style="color: #3f3f46; margin-top: 2px;">{{ $invoice->customer->email }}</div>
                    <div style="color: #3f3f46;">{{ $invoice->customer->phone }}</div>
                    <div style="color: #3f3f46; white-space: pre-line; margin-top: 2px;">{{ $invoice->customer->address }}</div>
                @else
                    <div style="color: #a1a1aa; italic;">Data Customer Terhapus</div>
                @endif
            </td>
            <td style="width: 45%; vertical-align: top; text-align: right;">
                <div style="margin-bottom: 6px;">
                    <div class="meta-title">Tanggal Invoice:</div>
                    <div style="font-weight: bold; color: #09090b;">{{ $invoice->invoice_date->format('d F Y') }}</div>
                </div>
                <div>
                    <div class="meta-title">Jatuh Tempo:</div>
                    <div style="font-weight: bold; color: #09090b;">{{ $invoice->due_date->format('d F Y') }}</div>
                </div>
            </td>
        </tr>
    </table>

    <!-- Items Table -->
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 45%;">Description</th>
                <th style="width: 10%; text-align: center;">Qty</th>
                <th style="width: 15%; text-align: right;">Price</th>
                <th style="width: 15%; text-align: right;">Discount</th>
                <th style="width: 15%; text-align: right;">Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>
                        <div class="item-name">{{ $item->item_name }}</div>
                        @if($item->description)
                            <div class="item-desc">{{ $item->description }}</div>
                        @endif
                    </td>
                    <td style="text-align: center;">{{ (float)$item->quantity }}</td>
                    <td style="text-align: right;">{{ $item->formatted_unit_price }}</td>
                    <td style="text-align: right; color: #71717a;">
                        {{ $item->discount > 0 ? 'Rp ' . number_format($item->discount, 0, ',', '.') : '-' }}
                    </td>
                    <td style="text-align: right; font-weight: bold;">{{ $item->formatted_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <!-- Summary & Bank Info Table -->
    <table class="summary-table">
        <tr>
            <td style="width: 55%;">
                @if(!empty($settings['bank_info']))
                    <div class="bank-box">
                        <div class="meta-title" style="margin-bottom: 4px;">Informasi Pembayaran:</div>
                        <div style="white-space: pre-line;">{{ $settings['bank_info'] }}</div>
                    </div>
                @endif
            </td>
            <td style="width: 45%; text-align: right;">
                <table style="width: 100%;">
                    <tr>
                        <td style="color: #71717a; padding: 3px 0;">Subtotal:</td>
                        <td style="text-align: right; font-weight: bold; padding: 3px 0;">{{ $invoice->formatted_subtotal }}</td>
                    </tr>
                    @if($invoice->discount > 0)
                        <tr>
                            <td style="color: #71717a; padding: 3px 0;">Diskon Invoice:</td>
                            <td style="text-align: right; font-weight: bold; color: #e11d48; padding: 3px 0;">- {{ $invoice->formatted_discount }}</td>
                        </tr>
                    @endif
                    @if($invoice->tax > 0)
                        <tr>
                            <td style="color: #71717a; padding: 3px 0;">Pajak (Tax):</td>
                            <td style="text-align: right; font-weight: bold; padding: 3px 0;">{{ $invoice->formatted_tax }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="total-row" style="padding-top: 8px;">TOTAL:</td>
                        <td class="total-row" style="text-align: right; padding-top: 8px;">{{ $invoice->formatted_total }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <!-- Footer Section with Notes & Vendor Stamp -->
    <table style="width: 100%; margin-top: 25px; border-top: 1px solid #e4e4e7; padding-top: 12px;">
        <tr>
            <td style="width: 60%; vertical-align: top;">
                @if($invoice->notes)
                    <div class="meta-title">Catatan / Terms & Conditions:</div>
                    <div style="color: #3f3f46; margin-top: 4px; white-space: pre-line;">{{ $invoice->notes }}</div>
                @endif
            </td>
            <td style="width: 40%; vertical-align: top; text-align: right;">
                @if(!empty($vendorStampBase64))
                    <div class="meta-title" style="margin-bottom: 4px;">Hormat Kami,</div>
                    <img src="{{ $vendorStampBase64 }}" style="max-height: 100px; max-width: 220px; margin-top: 2px; margin-bottom: 2px;">
                    <div style="font-weight: bold; color: #09090b; font-size: 9px;">{{ $settings['business_name'] ?? '' }}</div>
                @endif
            </td>
        </tr>
    </table>

</body>
</html>
