<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Receipt #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #2c3e50;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            padding: 30px 20px;
            min-height: 100vh;
        }

        .print-button-container {
            text-align: center;
            margin-bottom: 30px;
        }

        .no-print {
            /* shown on screen, hidden when printing */
        }

        .btn-print {
            padding: 12px 32px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 700;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(102, 126, 234, 0.4);
        }

        .btn-print:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(102, 126, 234, 0.6);
        }

        .receipt-wrapper {
            max-width: 850px;
            margin: 0 auto;
        }

        .receipt-container {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
            overflow: hidden;
        }

        /* Header Section with Gradient */
        .receipt-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 40px;
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .receipt-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .receipt-header h1 {
            font-size: 48px;
            font-weight: 900;
            letter-spacing: 2px;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            position: relative;
            z-index: 1;
        }

        .order-number {
            font-size: 24px;
            font-weight: 600;
            letter-spacing: 1px;
            opacity: 0.95;
            position: relative;
            z-index: 1;
        }

        /* Company Info Section */
        .company-section {
            padding: 30px 40px;
            background: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            align-items: center;
        }

        .company-info h3 {
            font-size: 18px;
            font-weight: 900;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .company-info p {
            font-size: 12px;
            color: #6c757d;
            line-height: 1.8;
        }

        .receipt-meta {
            display: flex;
            gap: 20px;
            justify-content: flex-end;
        }

        .receipt-meta-item {
            text-align: center;
        }

        .receipt-meta-label {
            display: block;
            font-size: 11px;
            font-weight: 700;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 6px;
        }

        .receipt-meta-value {
            display: block;
            font-size: 15px;
            font-weight: 900;
            color: #2c3e50;
        }

        /* Main Content */
        .receipt-content {
            padding: 40px;
        }

        .order-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 40px;
            padding-bottom: 30px;
            border-bottom: 2px solid #e9ecef;
        }

        .info-section h4 {
            font-size: 11px;
            font-weight: 900;
            color: #667eea;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 12px;
        }

        .info-section p {
            font-size: 13px;
            color: #2c3e50;
            line-height: 1.8;
            margin-bottom: 6px;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            margin-bottom: 30px;
            border-collapse: collapse;
        }

        .items-table thead {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .items-table thead th {
            padding: 16px 12px;
            text-align: left;
            font-size: 11px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .items-table thead th:last-child {
            text-align: right;
        }

        .items-table tbody td {
            padding: 16px 12px;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }

        .items-table tbody tr:last-child td {
            border-bottom: none;
        }

        .item-name {
            font-weight: 700;
            color: #2c3e50;
        }

        .item-category {
            font-size: 11px;
            color: #999;
            margin-top: 3px;
        }

        .qty-badge {
            background: #f0f4ff;
            color: #667eea;
            padding: 4px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            text-align: center;
            display: inline-block;
        }

        .text-right {
            text-align: right;
        }

        /* Totals Section */
        .totals-section {
            margin: 30px 0;
            padding: 30px;
            background: linear-gradient(135deg, #f5f7fa 0%, #f8f9fa 100%);
            border-radius: 12px;
            border-left: 4px solid #667eea;
        }

        .totals-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 16px;
            font-size: 13px;
        }

        .totals-row.grand-total {
            font-size: 20px;
            font-weight: 900;
            color: white;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 16px 20px;
            margin: -30px -30px -30px -30px;
            margin-top: 20px;
            border-radius: 0 0 12px 12px;
            padding-left: 30px;
            padding-right: 30px;
        }

        .totals-label {
            color: #6c757d;
            font-weight: 500;
        }

        .totals-value {
            font-weight: 700;
            color: #2c3e50;
        }

        .totals-row.grand-total .totals-label,
        .totals-row.grand-total .totals-value {
            color: white;
        }

        /* Payment Status Badge */
        .payment-status {
            display: inline-block;
            background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 14px;
            margin-top: 20px;
            box-shadow: 0 4px 15px rgba(17, 153, 142, 0.4);
        }

        .payment-status.qr {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        }

        .payment-status.cash {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }

        .payment-status.card {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
        }

        /* Footer Section */
        .receipt-footer {
            background: #f8f9fa;
            padding: 30px 40px;
            border-top: 2px solid #e9ecef;
            text-align: center;
            color: #6c757d;
            font-size: 12px;
            line-height: 1.8;
        }

        .footer-divider {
            width: 60%;
            height: 2px;
            background: linear-gradient(90deg, transparent, #667eea, transparent);
            margin: 20px auto;
        }

        .footer-note {
            font-style: italic;
            margin-bottom: 15px;
            color: #999;
        }

        .footer-contact {
            color: #667eea;
            font-weight: 600;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .no-print,
            .print-button-container {
                display: none !important;
            }

            .receipt-wrapper {
                max-width: 100%;
            }

            .receipt-container {
                box-shadow: none;
                border-radius: 0;
            }

            a {
                text-decoration: none;
                color: inherit;
            }
        }
    </style>
</head>
<body>
    <div class="print-button-container no-print">
        <div style="display: flex; align-items: center; justify-content: center; gap: 12px; flex-wrap: wrap;">
            <a href="{{ route('orders.index') }}"
               style="padding: 10px 24px; background: #6c757d; color: white; border: none; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                ← {{ __('Back to Orders') }}
            </a>
            <a href="{{ route('orders.show', $payment->order->id) }}"
               style="padding: 10px 24px; background: #0d6efd; color: white; border: none; border-radius: 8px; text-decoration: none; font-size: 14px; font-weight: 600; display: inline-flex; align-items: center; gap: 8px;">
                🔍 {{ __('View Order') }} #{{ $payment->order->order_no }}
            </a>
            <button class="btn-print" onclick="window.print()">🖨️ {{ __('Print Receipt') }}</button>
        </div>
    </div>

    <div class="receipt-wrapper">
        <div class="receipt-container">
            <!-- Header -->
            <div class="receipt-header">
                <h1>RECEIPT</h1>
                <div class="order-number">Order: {{ $payment->order->order_no }}</div>
            </div>

            <!-- Company & Receipt Info -->
            <div class="company-section">
                <div class="company-info">
                    <h3>{{ $appSettings['name'] ?? 'Restaurant Name' }}</h3>
                    <p>📍 {{ $appSettings['address'] ?? '1234 Restaurant St.' }}<br>
                       📞 {{ $appSettings['phone'] ?? '+855 012 345 678' }}<br>
                       ✉️ {{ $appSettings['email'] ?? 'info@restaurant.com' }}</p>
                </div>
                <div class="receipt-meta">
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-label">Receipt #</span>
                        <span class="receipt-meta-value">{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }}</span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-label">Date</span>
                        <span class="receipt-meta-value">{{ $payment->paid_at?->format('m/d/Y') ?? now()->format('m/d/Y') }}</span>
                    </div>
                    <div class="receipt-meta-item">
                        <span class="receipt-meta-label">Time</span>
                        <span class="receipt-meta-value">{{ $payment->paid_at?->format('h:i A') ?? now()->format('h:i A') }}</span>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="receipt-content">
                <!-- Order Info -->
                <div class="order-info-grid">
                    <div class="info-section">
                        <h4>👤 Customer</h4>
                        <p>{{ $payment->order->customer->name ?? 'Walk-in Customer' }}</p>
                        @if($payment->order->customer?->phone)
                        <p>{{ $payment->order->customer->phone }}</p>
                        @endif
                    </div>
                    <div class="info-section">
                        <h4>📦 Order Type</h4>
                        <p>{{ __(ucfirst(str_replace('_', ' ', $payment->order->order_type))) }}</p>
                        @if($payment->order->diningTable)
                        <p>Table: {{ $payment->order->diningTable->name }}</p>
                        @else
                        <p>No Table Assigned</p>
                        @endif
                    </div>
                </div>

                <!-- Items Table -->
                <table class="items-table">
                    <thead>
                        <tr>
                            <th>QTY</th>
                            <th style="width: 50%;">Description</th>
                            <th class="text-right">Unit Price</th>
                            <th class="text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($payment->order->items as $item)
                        <tr>
                            <td>
                                <span class="qty-badge">{{ $item->quantity }}x</span>
                            </td>
                            <td>
                                <div class="item-name">{{ $item->menuItem->name }}</div>
                                <div class="item-category">{{ $item->menuItem->category->name ?? '' }}</div>
                            </td>
                            <td class="text-right">{{ $appSettings['currency'] ?? '$' }}{{ number_format($item->price, 2) }}</td>
                            <td class="text-right"><strong>{{ $appSettings['currency'] ?? '$' }}{{ number_format($item->subtotal, 2) }}</strong></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>

                <!-- Totals -->
                <div class="totals-section">
                    <div class="totals-row">
                        <span class="totals-label">Subtotal</span>
                        <span class="totals-value">{{ $appSettings['currency'] ?? '$' }}{{ number_format($payment->order->subtotal, 2) }}</span>
                    </div>
                    <div class="totals-row">
                        <span class="totals-label">Tax ({{ $appSettings['tax_percentage'] ?? 10 }}%)</span>
                        <span class="totals-value">{{ $appSettings['currency'] ?? '$' }}{{ number_format($payment->order->tax, 2) }}</span>
                    </div>
                    <div class="totals-row grand-total">
                        <span class="totals-label">Total ({{ $appSettings['currency'] ?? 'USD' }})</span>
                        <span class="totals-value">{{ $appSettings['currency'] ?? '$' }}{{ number_format($payment->order->total_amount, 2) }}</span>
                    </div>
                </div>

                <!-- Payment Info -->
                <div style="margin-bottom: 30px;">
                    <h4 style="font-size: 11px; font-weight: 900; color: #667eea; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 12px;">Payment Information</h4>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                        <div>
                            <p style="font-size: 12px; color: #6c757d; margin-bottom: 4px;">Method</p>
                            <p style="font-size: 15px; font-weight: 700; color: #2c3e50;">
                                @php
                                    $methods = [
                                        'cash' => '💵 Cash',
                                        'card' => '💳 Card',
                                        'qr' => '📱 QR Pay',
                                        'khqr' => '📱 KHQR'
                                    ];
                                    echo $methods[$payment->payment_method] ?? ucfirst($payment->payment_method);
                                @endphp
                            </p>
                        </div>
                        <div>
                            <p style="font-size: 12px; color: #6c757d; margin-bottom: 4px;">Amount Paid</p>
                            <p style="font-size: 15px; font-weight: 700; color: #2c3e50;">{{ $appSettings['currency'] ?? '$' }}{{ number_format($payment->paid_amount, 2) }}</p>
                        </div>
                    </div>
                    @if($payment->payer_name || $payment->payer_account)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;">
                        <p style="font-size: 12px; color: #6c757d; margin-bottom: 4px;">Paid By</p>
                        <p style="font-size: 15px; font-weight: 700; color: #2c3e50; margin-bottom: 0;">
                            {{ $payment->payer_name ?? 'Mobile account' }}
                            @if($payment->payer_account)
                                <span style="font-size: 12px; color: #6c757d;">({{ $payment->payer_account }})</span>
                            @endif
                        </p>
                    </div>
                    @endif
                    @if($payment->change_amount > 0)
                    <div style="margin-top: 15px; padding-top: 15px; border-top: 1px solid #e9ecef;">
                        <p style="font-size: 12px; color: #6c757d; margin-bottom: 4px;">Change</p>
                        <p style="font-size: 18px; font-weight: 900; color: #11998e;">{{ $appSettings['currency'] ?? '$' }}{{ number_format($payment->change_amount, 2) }}</p>
                    </div>
                    @endif
                </div>

                <!-- Status Badge -->
                <div style="text-align: center;">
                    @php
                        $statusClass = match($payment->payment_method) {
                            'qr' => 'qr',
                            'cash' => 'cash',
                            'card' => 'card',
                            default => ''
                        };
                    @endphp
                    <span class="payment-status {{ $statusClass }}">✓ PAID VIA {{ strtoupper(str_replace('_', ' ', $payment->payment_method)) }}</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="receipt-footer">
                <div class="footer-note">Thank you for your purchase! Please retain this receipt for your records.</div>
                <div class="footer-divider"></div>
                <p style="margin-bottom: 10px;">Managed by: <strong>{{ $payment->order->user->name ?? 'System' }}</strong></p>
                <p class="footer-contact">{{ $appSettings['email'] ?? 'info@restaurant.com' }} | {{ $appSettings['phone'] ?? '+855 012 345 678' }}</p>
                <p style="margin-top: 15px; font-size: 11px; color: #999;">Receipt #{{ str_pad($payment->id, 8, '0', STR_PAD_LEFT) }} | 1/1</p>
            </div>
        </div>
    </div>
</body>
</html>
