<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bail & Bond Details | Court Management System</title>

    <style>
        * {
            box-sizing: border-box;
        }
        body {
            background: transparent;
color: #1e293b;
        }

        .container {
            max-width: 1000px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
            color: #0f172a;
            font-size: 28px;
        }

        .subtitle {
            color: #64748b;
            margin: 0;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .btn {
            display: inline-block;
            padding: 11px 17px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-back {
            background: #e2e8f0;
            color: #334155;
        }

        .btn-edit {
            background: #1f6f43;
            color: white;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 22px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
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
            color: #64748b;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .detail-value {
            font-weight: 600;
            overflow-wrap: anywhere;
        }

        .amount {
            color: #0f766e;
            font-size: 20px;
        }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1f6f43;
            font-size: 12px;
            font-weight: bold;
        }

        .text-block {
            line-height: 1.7;
            white-space: pre-wrap;
            overflow-wrap: anywhere;
        }

        .empty {
            color: #94a3b8;
            font-style: italic;
        }

        @media (max-width: 600px) {
            body {
                padding: 15px;
            }

            .details-grid {
                grid-template-columns: 1fr;
                gap: 18px;
            }

            .card {
                padding: 18px;
            }

            h1 {
                font-size: 23px;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <div>
            <h1>Bail & Bond Details</h1>
            <p class="subtitle">
                Reference: {{ $bailBond->reference_number }}
            </p>
        </div>

        <div class="actions">
            <a href="{{ route('bail-bonds.index') }}" class="btn btn-back">
                Back to List
            </a>

            <a href="{{ route('bail-bonds.edit', $bailBond) }}" class="btn btn-edit">
                Edit Record
            </a>
        </div>
    </div>

    <div class="card">
        <h2>Bond Information</h2>

        <div class="details-grid">
            <div>
                <div class="detail-label">Reference Number</div>
                <div class="detail-value">{{ $bailBond->reference_number }}</div>
            </div>

            <div>
                <div class="detail-label">Bond Type</div>
                <div class="detail-value">
                    {{ ucwords(str_replace('_', ' ', $bailBond->bond_type)) }}
                </div>
            </div>

            <div>
                <div class="detail-label">Status</div>
                <div>
                    <span class="status">
                        {{ ucwords(str_replace('_', ' ', $bailBond->status)) }}
                    </span>
                </div>
            </div>

            <div>
                <div class="detail-label">Order Date</div>
                <div class="detail-value">
                    {{ $bailBond->order_date ? $bailBond->order_date->format('d M Y') : 'Not recorded' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Financial Summary</h2>

        <div class="details-grid">
            <div>
                <div class="detail-label">Ordered Amount</div>
                <div class="detail-value amount">
                    KSh {{ number_format((float) $bailBond->ordered_amount, 2) }}
                </div>
            </div>

            <div>
                <div class="detail-label">Deposited Amount</div>
                <div class="detail-value amount">
                    KSh {{ number_format((float) $bailBond->deposited_amount, 2) }}
                </div>
            </div>

            <div>
                <div class="detail-label">Outstanding Amount</div>
                <div class="detail-value amount">
                    KSh {{ number_format(max(0, (float) $bailBond->ordered_amount - (float) $bailBond->deposited_amount), 2) }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Linked Case & Participant</h2>

        <div class="details-grid">
            <div>
                <div class="detail-label">Case Number</div>
                <div class="detail-value">
                    {{ $bailBond->courtCase->case_number ?? 'Not available' }}
                </div>
            </div>

            <div>
                <div class="detail-label">Case Title</div>
                <div class="detail-value">
                    {{ $bailBond->courtCase->case_title ?? 'Not available' }}
                </div>
            </div>

            <div>
                <div class="detail-label">Participant</div>
                <div class="detail-value">
                    @if($bailBond->participant)
                        {{ $bailBond->participant->full_name }}
                    @else
                        Not available
                    @endif
                </div>
            </div>

            <div>
                <div class="detail-label">Participant Type</div>
                <div class="detail-value">
                    {{ $bailBond->participant
                        ? ucwords(str_replace('_', ' ', $bailBond->participant->participant_type))
                        : 'Not available' }}
                </div>
            </div>
        </div>
    </div>

    <div class="card">
        <h2>Conditions & Notes</h2>

        <div style="margin-bottom: 22px;">
            <div class="detail-label">Bond Conditions</div>
            <div class="text-block">
                @if($bailBond->conditions)
                    {{ $bailBond->conditions }}
                @else
                    <span class="empty">No conditions recorded.</span>
                @endif
            </div>
        </div>

        <div>
            <div class="detail-label">Additional Notes</div>
            <div class="text-block">
                @if($bailBond->notes)
                    {{ $bailBond->notes }}
                @else
                    <span class="empty">No additional notes.</span>
                @endif
            </div>
        </div>
    </div>

</div>
</body>
</html>
