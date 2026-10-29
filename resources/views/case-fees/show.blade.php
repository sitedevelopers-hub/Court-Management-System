<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fee Details | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 950px; margin: auto; }
        .card {
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; overflow-wrap: anywhere; }
        .subtitle { color: #64748b; margin-bottom: 24px; }
        .success {
            padding: 13px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
        }
        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .item {
            padding: 16px;
            border: 1px solid #e2e8f0;
            border-radius: 9px;
            min-width: 0;
        }
        .label {
            display: block;
            color: #64748b;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 700;
            margin-bottom: 8px;
        }
        .value { overflow-wrap: anywhere; line-height: 1.5; }
        .full { grid-column: 1 / -1; }
        .money { font-size: 20px; font-weight: 700; }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 25px;
        }
        .btn {
            display: inline-block;
            padding: 12px 16px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .primary { background: #1f6f43; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        .table-wrap { overflow-x: auto; margin-top: 26px; }
        table { width: 100%; border-collapse: collapse; min-width: 600px; }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #e2e8f0;
        }
        th { background: #eef5ef; font-size: 12px; text-transform: uppercase; }
        .empty { color: #64748b; text-align: center; padding: 20px; }
        @media (max-width: 600px) {
            .details { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Court Fee Details</h1>
        <p class="subtitle">Reference: {{ $fee->fee_reference }}</p>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        <div class="details">
            <div class="item">
                <span class="label">Fee Reference</span>
                <div class="value">{{ $fee->fee_reference }}</div>
            </div>

            <div class="item">
                <span class="label">Fee Type</span>
                <div class="value">
                    {{ ucwords(str_replace('_', ' ', $fee->fee_type)) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Court Case</span>
                <div class="value">
                    {{ $fee->courtCase?->case_number ?? 'Case unavailable' }}
                    — {{ $fee->courtCase?->case_title ?? '' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Related Participant</span>
                <div class="value">
                    {{ $fee->participant?->full_name ?? 'Not specified' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Total Fee</span>
                <div class="value money">
                    KES {{ number_format((float) $fee->amount, 2) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Amount Paid</span>
                <div class="value money">
                    KES {{ number_format((float) $fee->amount_paid, 2) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Outstanding Balance</span>
                <div class="value money">
                    KES {{ number_format(max(0, (float) $fee->amount - (float) $fee->amount_paid), 2) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Status</span>
                <div class="value">
                    {{ ucwords(str_replace('_', ' ', $fee->status)) }}
                </div>
            </div>

            <div class="item">
                <span class="label">Due Date</span>
                <div class="value">
                    {{ $fee->due_date?->format('d M Y') ?? 'Not set' }}
                </div>
            </div>

            <div class="item">
                <span class="label">Created At</span>
                <div class="value">
                    {{ $fee->created_at?->format('d M Y, h:i A') }}
                </div>
            </div>

            <div class="item full">
                <span class="label">Description</span>
                <div class="value">{{ $fee->description }}</div>
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('case-fees.edit', $fee) }}" class="btn primary">
                Edit Fee
            </a>
            <a href="{{ route('case-fees.index') }}" class="btn secondary">
                Back to Fees
            </a>
        </div>

        <h2>Payment History</h2>
        <p class="subtitle">Payments recorded against this fee will appear here.</p>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Receipt Number</th>
                    <th>Amount</th>
                    <th>Method</th>
                    <th>Transaction Reference</th>
                    <th>Date Paid</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($fee->payments as $payment)
                    <tr>
                        <td>{{ $payment->receipt_number }}</td>
                        <td>KES {{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->transaction_reference ?: '—' }}</td>
                        <td>{{ $payment->paid_at?->format('d M Y, h:i A') ?? '—' }}</td>
                        <td>{{ ucfirst($payment->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="empty">
                            No payments have been recorded for this fee yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>
