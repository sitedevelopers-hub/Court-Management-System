<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Record Payment | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 800px; margin: auto; }
        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }
        .field { min-width: 0; }
        .full { grid-column: 1 / -1; }
        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            background: white;
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }
        textarea { min-height: 100px; resize: vertical; }
        .help { font-size: 12px; color: #64748b; margin-top: 6px; }
        .error { font-size: 13px; color: #b91c1c; margin-top: 6px; }
        .alert {
            padding: 14px;
            background: #fef2f2;
            color: #991b1b;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .notice {
            padding: 14px;
            background: #eff6ff;
            color: #1e40af;
            border-radius: 8px;
            margin-bottom: 22px;
            line-height: 1.5;
        }
        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 25px;
        }
        button, .btn {
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        button { background: #1f6f43; color: white; }
        .btn { background: #e2e8f0; color: #1e293b; }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .card { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Record Payment</h1>
        <p class="subtitle">Register a payment against an outstanding court fee.</p>

        <div class="notice">
            A receipt number will be generated automatically after a successful
            payment. Only record payments that have actually been received.
        </div>

        @if ($errors->any())
            <div class="alert">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($fees->isEmpty())
            <div class="notice">
                There are no outstanding fees available for payment.
                Register a court fee first if necessary.
            </div>
            <a href="{{ route('case-fees.index') }}" class="btn">View Court Fees</a>
        @else
            <form action="{{ route('payments.store') }}" method="POST">
                @csrf

                <div class="grid">
                    <div class="field full">
                        <label for="case_fee_id">Court Fee *</label>
                        <select id="case_fee_id" name="case_fee_id" required>
                            <option value="">Select an outstanding fee</option>
                            @foreach ($fees as $fee)
                                @php
                                    $outstanding = max(
                                        0,
                                        (float) $fee->amount - (float) $fee->amount_paid
                                    );
                                @endphp
                                <option
                                    value="{{ $fee->id }}"
                                    data-outstanding="{{ number_format($outstanding, 2, '.', '') }}"
                                    {{ (string) old('case_fee_id', $selectedFeeId) === (string) $fee->id ? 'selected' : '' }}
                                >
                                    {{ $fee->fee_reference }} —
                                    {{ $fee->courtCase?->case_number ?? 'No case number' }}
                                    — Balance: KES {{ number_format($outstanding, 2) }}
                                </option>
                            @endforeach
                        </select>
                        @error('case_fee_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                        <div class="help" id="balance-help">Choose a fee to see its outstanding balance.</div>
                    </div>

                    <div class="field">
                        <label for="amount">Payment Amount (KES) *</label>
                        <input
                            type="number"
                            id="amount"
                            name="amount"
                            min="0.01"
                            max="9999999999.99"
                            step="0.01"
                            value="{{ old('amount') }}"
                            required
                        >
                        @error('amount')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="payment_method">Payment Method *</label>
                        <select id="payment_method" name="payment_method" required>
                            <option value="">Select method</option>
                            @foreach ([
                                'cash' => 'Cash',
                                'mpesa' => 'M-Pesa',
                                'bank_transfer' => 'Bank Transfer',
                                'card' => 'Card',
                                'cheque' => 'Cheque',
                                'other' => 'Other',
                            ] as $value => $label)
                                <option value="{{ $value }}"
                                    {{ old('payment_method') === $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                        @error('payment_method')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="transaction_reference">Transaction Reference</label>
                        <input
                            type="text"
                            id="transaction_reference"
                            name="transaction_reference"
                            maxlength="255"
                            value="{{ old('transaction_reference') }}"
                            placeholder="e.g. M-Pesa receipt code"
                        >
                        <div class="help">Enter the reference supplied by the payment provider, if applicable.</div>
                        @error('transaction_reference')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="paid_at">Payment Date and Time *</label>
                        <input
                            type="datetime-local"
                            id="paid_at"
                            name="paid_at"
                            value="{{ old('paid_at', now()->format('Y-m-d\TH:i')) }}"
                            max="{{ now()->format('Y-m-d\TH:i') }}"
                            required
                        >
                        @error('paid_at')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="notes">Notes</label>
                        <textarea
                            id="notes"
                            name="notes"
                            maxlength="2000"
                            placeholder="Optional payment details..."
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="actions">
                    <button type="submit">Record Payment</button>
                    <a href="{{ route('payments.index') }}" class="btn">Cancel</a>
                </div>
            </form>
        @endif
    </div>
</div>

<script>
    const feeSelect = document.getElementById('case_fee_id');
    const amountInput = document.getElementById('amount');
    const balanceHelp = document.getElementById('balance-help');

    function updateOutstandingBalance() {
        const option = feeSelect.options[feeSelect.selectedIndex];

        if (!option || !option.dataset.outstanding) {
            balanceHelp.textContent = 'Choose a fee to see its outstanding balance.';
            amountInput.removeAttribute('max');
            return;
        }

        const balance = Number(option.dataset.outstanding);
        balanceHelp.textContent =
            'Outstanding balance: KES ' +
            balance.toLocaleString('en-KE', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

        amountInput.max = balance.toFixed(2);
    }

    feeSelect.addEventListener('change', updateOutstandingBalance);
    updateOutstandingBalance();
</script>
</body>
</html>
