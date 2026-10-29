<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Financial Adjustment | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        .container { max-width: 900px; margin: auto; }

        h1 { margin-bottom: 8px; color: #0f172a; font-size: 28px; }

        .subtitle { color: #64748b; margin-bottom: 25px; }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, .06);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group { display: flex; flex-direction: column; gap: 8px; }

        .full-width { grid-column: 1 / -1; }

        label { font-weight: bold; font-size: 14px; }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
            background: white;
        }

        textarea { min-height: 100px; resize: vertical; }

        .help { color: #64748b; font-size: 12px; line-height: 1.5; }

        .errors {
            padding: 14px 18px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #fee2e2;
            color: #991b1b;
        }

        .notice {
            background: #eff6ff;
            color: #1e40af;
            border-radius: 8px;
            padding: 14px;
            margin-bottom: 22px;
            font-size: 13px;
            line-height: 1.6;
        }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 7px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-save { background: #1f6f43; color: white; }
        .btn-cancel { background: #e2e8f0; color: #334155; }

        @media (max-width: 600px) {
            body { padding: 15px; }
            .form-grid { grid-template-columns: 1fr; }
            .full-width { grid-column: auto; }
            .card { padding: 18px; }
            h1 { font-size: 23px; }
        }
    </style>
</head>

<body>
<div class="container">

    <h1>Record Financial Adjustment</h1>
    <p class="subtitle">Register a refund, forfeiture, expense or other adjustment.</p>

    <div class="card">

        <div class="notice">
            <strong>Important:</strong> This form records an adjustment in the system.
            It does not automatically transfer money, issue refunds or execute forfeitures.
            Select a payment OR a bail/bond record, not both.
        </div>

        @if($errors->any())
            <div class="errors">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('financial-adjustments.store') }}" method="POST">
            @csrf

            <div class="form-grid">

                <div class="form-group full-width">
                    <label for="case_id">Court Case *</label>
                    <select id="case_id" name="case_id" required>
                        <option value="">Select a case</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}"
                                {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} — {{ $case->case_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="payment_id">Related Payment</label>
                    <select id="payment_id" name="payment_id">
                        <option value="">No payment selected</option>
                        @foreach($payments as $payment)
                            <option value="{{ $payment->id }}"
                                data-case="{{ $payment->fee?->case_id }}"
                                {{ old('payment_id') == $payment->id ? 'selected' : '' }}>
                                {{ $payment->receipt_number }}
                                — KSh {{ number_format((float) $payment->amount, 2) }}
                                ({{ $payment->fee?->courtCase?->case_number ?? 'Unknown case' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="help">Choose this for an adjustment linked to a payment.</span>
                </div>

                <div class="form-group">
                    <label for="bail_bond_id">Related Bail / Bond</label>
                    <select id="bail_bond_id" name="bail_bond_id">
                        <option value="">No bail/bond selected</option>
                        @foreach($bailBonds as $bond)
                            <option value="{{ $bond->id }}"
                                data-case="{{ $bond->case_id }}"
                                {{ old('bail_bond_id') == $bond->id ? 'selected' : '' }}>
                                {{ $bond->reference_number }}
                                — KSh {{ number_format((float) $bond->deposited_amount, 2) }} deposited
                                ({{ $bond->courtCase?->case_number ?? 'Unknown case' }})
                            </option>
                        @endforeach
                    </select>
                    <span class="help">Choose this for an adjustment linked to bail or bond.</span>
                </div>

                <div class="form-group">
                    <label for="adjustment_type">Adjustment Type *</label>
                    <select id="adjustment_type" name="adjustment_type" required>
                        @foreach([
                            'refund' => 'Refund',
                            'forfeiture' => 'Forfeiture',
                            'expense' => 'Expense',
                            'correction' => 'Correction',
                            'other' => 'Other'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('adjustment_type') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="amount">Amount (KSh) *</label>
                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        min="0.01"
                        step="0.01"
                        value="{{ old('amount') }}"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        @foreach([
                            'pending' => 'Pending',
                            'approved' => 'Approved',
                            'completed' => 'Completed',
                            'rejected' => 'Rejected'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('status', 'pending') === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="processed_at">Date Processed</label>
                    <input
                        type="datetime-local"
                        id="processed_at"
                        name="processed_at"
                        max="{{ now()->format('Y-m-d\TH:i') }}"
                        value="{{ old('processed_at') }}"
                    >
                    <span class="help">Leave blank if the adjustment has not been processed.</span>
                </div>

                <div class="form-group full-width">
                    <label for="reason">Reason / Explanation *</label>
                    <textarea
                        id="reason"
                        name="reason"
                        maxlength="2000"
                        required
                        placeholder="Explain why this adjustment is being recorded..."
                    >{{ old('reason') }}</textarea>
                </div>

            </div>

            <div class="actions">
                <button type="submit" class="btn btn-save">Save Adjustment</button>
                <a href="{{ route('financial-adjustments.index') }}" class="btn btn-cancel">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>

<script>
    const caseSelect = document.getElementById('case_id');
    const paymentSelect = document.getElementById('payment_id');
    const bondSelect = document.getElementById('bail_bond_id');

    function filterLinkedRecords() {
        const selectedCase = caseSelect.value;

        [paymentSelect, bondSelect].forEach(select => {
            Array.from(select.options).forEach(option => {
                if (!option.value) {
                    option.hidden = false;
                    return;
                }

                option.hidden = selectedCase !== '' &&
                    option.dataset.case !== selectedCase;
            });

            if (select.selectedOptions[0]?.hidden) {
                select.value = '';
            }
        });
    }

    paymentSelect.addEventListener('change', function () {
        if (this.value) {
            bondSelect.value = '';
        }
    });

    bondSelect.addEventListener('change', function () {
        if (this.value) {
            paymentSelect.value = '';
        }
    });

    caseSelect.addEventListener('change', filterLinkedRecords);
    filterLinkedRecords();
</script>

</body>
</html>
