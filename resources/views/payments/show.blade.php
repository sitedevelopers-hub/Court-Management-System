<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Receipt | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 850px; margin: auto; }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 8px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            border: none;
            cursor: pointer;
        }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .alert {
            padding: 14px;
            background: #dcfce7;
            color: #166534;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .receipt {
            background: white;
            border-radius: 14px;
            padding: 35px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        .receipt-header {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            align-items: flex-start;
            border-bottom: 2px solid #e2e8f0;
            padding-bottom: 24px;
            margin-bottom: 25px;
        }
        .brand {
            font-size: 22px;
            font-weight: 800;
            color: #1f6f43;
            margin: 0 0 8px;
        }
        .muted { color: #64748b; }
        .receipt-label {
            display: inline-block;
            background: #dcfce7;
            color: #166534;
            padding: 7px 12px;
            border-radius: 20px;
            font-weight: 700;
            font-size: 12px;
            text-transform: uppercase;
        }
        h2 { font-size: 16px; margin: 25px 0 14px; }
        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .detail {
            padding-bottom: 13px;
            border-bottom: 1px solid #e2e8f0;
            min-width: 0;
        }
        .detail-label {
            display: block;
            font-size: 12px;
            color: #64748b;
            margin-bottom: 7px;
        }
        .detail-value {
            font-weight: 600;
            overflow-wrap: anywhere;
        }
        .amount-box {
            background: #eff6ff;
            border-radius: 12px;
            padding: 24px;
            margin-top: 25px;
        }
        .amount-box .label { color: #1e40af; font-size: 14px; }
        .amount {
            color: #1f6f43;
            font-size: 30px;
            font-weight: 800;
            margin-top: 8px;
        }
        .footer {
            border-top: 1px solid #e2e8f0;
            margin-top: 30px;
            padding-top: 20px;
            text-align: center;
            color: #64748b;
            font-size: 13px;
            line-height: 1.6;
        }
        @media (max-width: 600px) {
            .receipt { padding: 22px 17px; }
            .receipt-header { flex-direction: column; }
            .details { grid-template-columns: 1fr; gap: 14px; }
            .amount { font-size: 25px; }
        }
        @media print {
        body {
            background: transparent;
padding: 0; }
            .actions, .alert { display: none !important; }
            .container { max-width: none; }
            .receipt { box-shadow: none; padding: 15px; }
        }
    </style>
</head>
<body>
<div class="container">

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert" style="background:#fee2e2;color:#991b1b;">
            {{ session('error') }}
        </div>
    @endif

    @if (session('info'))
        <div class="alert" style="background:#dbeafe;color:#1e40af;">
            {{ session('info') }}
        </div>
    @endif

    <div class="actions">
        <button type="button" class="btn" onclick="window.print()">
            Print / Save Receipt
        </button>
        <a href="{{ route('payments.index') }}" class="btn btn-secondary">
            All Payments
        </a>
        <a href="{{ route('case-fees.show', $payment->fee) }}" class="btn btn-secondary">
            View Court Fee
        </a>
    </div>

    <div class="receipt">
        <div class="receipt-header">
            <div>
                <p class="brand">COURT MANAGEMENT SYSTEM</p>
                <div class="muted">Payment Receipt</div>
            </div>
            <div>
                <span class="receipt-label">{{ $payment->status }}</span>
                <p><strong>Receipt:</strong> {{ $payment->receipt_number }}</p>
                <p class="muted">
                    {{ $payment->paid_at?->format('d M Y, H:i') ?? 'Date not available' }}
                </p>
            </div>
        </div>

        <h2>Case Details</h2>
        <div class="details">
            <div class="detail">
                <span class="detail-label">Case Number</span>
                <span class="detail-value">
                    {{ $payment->fee?->courtCase?->case_number ?? 'N/A' }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Case Title</span>
                <span class="detail-value">
                    {{ $payment->fee?->courtCase?->case_title ?? 'N/A' }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Fee Reference</span>
                <span class="detail-value">
                    {{ $payment->fee?->fee_reference ?? 'N/A' }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Fee Type</span>
                <span class="detail-value">
                    {{ $payment->fee ? ucwords(str_replace('_', ' ', $payment->fee->fee_type)) : 'N/A' }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Participant</span>
                <span class="detail-value">
                    {{ $payment->fee?->participant?->full_name ?? 'Not specified' }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Received By</span>
                <span class="detail-value">
                    {{ $payment->receiver?->name ?? 'System' }}
                </span>
            </div>
        </div>

        <h2>Payment Details</h2>
        <div class="details">
            <div class="detail">
                <span class="detail-label">Payment Method</span>
                <span class="detail-value">
                    {{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}
                </span>
            </div>
            <div class="detail">
                <span class="detail-label">Transaction Reference</span>
                <span class="detail-value">
                    {{ $payment->transaction_reference ?: 'Not provided' }}
                </span>
            </div>
        </div>

        <div class="amount-box">
            <div class="label">Amount Received</div>
            <div class="amount">
                KES {{ number_format((float) $payment->amount, 2) }}
            </div>
        </div>

        @if ($payment->notes)
            <h2>Notes</h2>
            <p>{{ $payment->notes }}</p>
        @endif

        <div class="footer">
            This receipt records a payment entered into the Court Management System.
            <br>
            Keep this receipt for your records.
        </div>
    </div>
</div>
</body>
</html>
