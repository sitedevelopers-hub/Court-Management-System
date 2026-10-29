<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Adjustment Details | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        .container { max-width: 1000px; margin: auto; }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 { margin: 0 0 8px; font-size: 28px; color: #0f172a; }

        .subtitle { margin: 0; color: #64748b; }

        .actions { display: flex; flex-wrap: wrap; gap: 10px; }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .btn-back { background: #e2e8f0; color: #334155; }
        .btn-edit { background: #1f6f43; color: white; }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .06);
        }

        .card h2 {
            margin: 0 0 22px;
            padding-bottom: 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 19px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .detail-label {
            margin-bottom: 7px;
            color: #64748b;
            font-size: 13px;
        }

        .detail-value {
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .amount { color: #0f766e; font-size: 23px; }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            background: #e2e8f0;
            color: #334155;
            font-size: 12px;
            font-weight: bold;
        }

        .status-pending { background: #fef3c7; color: #92400e; }
        .status-approved { background: #dbeafe; color: #1f6f43; }
        .status-completed { background: #dcfce7; color: #166534; }
        .status-rejected { background: #fee2e2; color: #991b1b; }

        .reason {
            line-height: 1.8;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .alert-success { background: #dcfce7; color: #166534; }
        .alert-error { background: #fee2e2; color: #991b1b; }

        @media (max-width: 600px) {
            body { padding: 15px; }
            .details-grid { grid-template-columns: 1fr; gap: 18px; }
            .card { padding: 18px; }
            h1 { font-size: 23px; }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <div>
            <h1>Financial Adjustment Details</h1>
            <p class="subtitle">Reference: {{ $adjustment->reference_number }}</p>
        </div>

        <div class="actions">
            <a href="{{ route('financial-adjustments.index') }}" class="btn btn-back">
                Back to List
            </a>

            @if(!in_array($adjustment->status, ['completed', 'rejected'], true))
                <a href="{{ route('financial-adjustments.edit', $adjustment) }}" class="btn btn-edit">
                    Edit Adjustment
                </a>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <h2>Adjustment Summary</h2>

        <div class="details-grid">
            <div>
                <div class="detail-label">Reference Number</div>
                <div class="detail-value">{{ $adjustment->reference_number }}</div>
            </div>

            <div>
                <div class="detail-label">Adjustment Type</div>
                <div class="detail-value">
                    {{ ucwords(str_replace('_', ' ', $adjustment->adjustment_type)) }}
                </div>
            </div>

            <div>
                <div class="detail-label">Amount</div>
                <div class="detail-value amount">
                    KSh {{ number_format((float) $adjustment->amount, 2) }}
                </div>
            </div>

            <div>
                <div class="detail-label">Current Status</div>
                <div>
                    <span class="status status-{{ $adjustment->status }}">
                        {{ ucwords(str_replace('_', ' ', $adjustment->status)) }}
                    </span>
                </div>
            </div>

            <div>
                <div class="detail-label">Date Recorded</div>
                <div class="detail-value">
                    {{ $adjustment->created_at?->format('d M Y, H:i') ?? 'Not available' }}
                </div>
            </div>

            <div>
                <div class="detail-label">Date Processed</div>
                <div class="detail-value">
                    {{ $adjustment->processed_at?->format('d M Y, H:i') ?? 'Not recorded' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Linked Court Case</h2>

        <div class="details-grid">
            <div>
                <div class="detail-label">Case Number</div>
                <div class="detail-value">
                    {{ $adjustment->courtCase?->case_number ?? 'Not available' }}
                </div>
            </div>

            <div>
                <div class="detail-label">Case Title</div>
                <div class="detail-value">
                    {{ $adjustment->courtCase?->case_title ?? 'Not available' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Related Financial Record</h2>

        @if($adjustment->payment)
            <div class="details-grid">
                <div>
                    <div class="detail-label">Record Type</div>
                    <div class="detail-value">Payment</div>
                </div>

                <div>
                    <div class="detail-label">Receipt Number</div>
                    <div class="detail-value">
                        {{ $adjustment->payment->receipt_number }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Original Payment Amount</div>
                    <div class="detail-value">
                        KSh {{ number_format((float) $adjustment->payment->amount, 2) }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Payment Method</div>
                    <div class="detail-value">
                        {{ ucwords(str_replace('_', ' ', $adjustment->payment->payment_method)) }}
                    </div>
                </div>
            </div>

        @elseif($adjustment->bailBond)
            <div class="details-grid">
                <div>
                    <div class="detail-label">Record Type</div>
                    <div class="detail-value">Bail / Bond</div>
                </div>

                <div>
                    <div class="detail-label">Bond Reference</div>
                    <div class="detail-value">
                        {{ $adjustment->bailBond->reference_number }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Bond Type</div>
                    <div class="detail-value">
                        {{ ucwords(str_replace('_', ' ', $adjustment->bailBond->bond_type)) }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Deposited Amount</div>
                    <div class="detail-value">
                        KSh {{ number_format((float) $adjustment->bailBond->deposited_amount, 2) }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Participant</div>
                    <div class="detail-value">
                        {{ $adjustment->bailBond->participant?->full_name ?? 'Not available' }}
                    </div>
                </div>
            </div>

        @else
            <p>No related payment or bail/bond record is linked to this adjustment.</p>
        @endif
    </div>

    <div class="card">
        <h2>Reason & Audit Information</h2>

        <div style="margin-bottom: 22px;">
            <div class="detail-label">Reason / Explanation</div>
            <div class="reason">{{ $adjustment->reason }}</div>
        </div>

        <div>
            <div class="detail-label">Recorded By</div>
            <div class="detail-value">
                {{ $adjustment->recorder?->name ?? 'System / User not available' }}
            </div>
        </div>
    </div>

</div>
</body>
</html>
