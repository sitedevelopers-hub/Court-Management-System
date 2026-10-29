<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Bail or Bond | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 850px; margin: auto; }
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
            font-weight: 600;
            font-size: 14px;
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
        .help { color: #64748b; font-size: 12px; margin-top: 6px; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
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
        .actions { display: flex; gap: 10px; flex-wrap: wrap; margin-top: 25px; }
        button, .btn {
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
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
        <h1>Register Bail or Bond</h1>
        <p class="subtitle">Record a court order and any amount already deposited.</p>

        <div class="notice">
            Enter amounts and conditions exactly as stated in the relevant court order.
            This form records information; it does not verify the order or confirm receipt of funds.
        </div>

        @if ($errors->any())
            <div class="alert">
                <strong>Please correct these errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($cases->isEmpty() || $participants->isEmpty())
            <div class="notice">
                You need at least one registered court case and one active participant
                before creating a bail or bond record.
                <p>
                    <a href="{{ route('cases.create') }}">Register a case</a>
                    |
                    <a href="{{ route('participants.create') }}">Register a participant</a>
                </p>
            </div>
        @else
            <form action="{{ route('bail-bonds.store') }}" method="POST">
                @csrf

                <div class="grid">
                    <div class="field full">
                        <label for="case_id">Court Case *</label>
                        <select id="case_id" name="case_id" required>
                            <option value="">Select a case</option>
                            @foreach ($cases as $case)
                                <option value="{{ $case->id }}"
                                    {{ (string) old('case_id') === (string) $case->id ? 'selected' : '' }}>
                                    {{ $case->case_number }} — {{ $case->case_title }}
                                </option>
                            @endforeach
                        </select>
                        @error('case_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="participant_id">Person Named in the Order *</label>
                        <select id="participant_id" name="participant_id" required>
                            <option value="">Select a participant</option>
                            @foreach ($participants as $participant)
                                <option value="{{ $participant->id }}"
                                    {{ (string) old('participant_id') === (string) $participant->id ? 'selected' : '' }}>
                                    {{ $participant->full_name }} — {{ $participant->participant_number }}
                                </option>
                            @endforeach
                        </select>
                        @error('participant_id')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="bond_type">Bail / Bond Type *</label>
                        <select id="bond_type" name="bond_type" required>
                            <option value="">Select type</option>
                            <option value="cash_bail" {{ old('bond_type') === 'cash_bail' ? 'selected' : '' }}>Cash Bail</option>
                            <option value="bond" {{ old('bond_type') === 'bond' ? 'selected' : '' }}>Bond</option>
                            <option value="personal_bond" {{ old('bond_type') === 'personal_bond' ? 'selected' : '' }}>Personal Bond</option>
                            <option value="other" {{ old('bond_type') === 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        @error('bond_type')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="order_date">Order Date</label>
                        <input
                            type="date"
                            id="order_date"
                            name="order_date"
                            max="{{ now()->toDateString() }}"
                            value="{{ old('order_date') }}"
                        >
                        @error('order_date')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="ordered_amount">Ordered Amount (KES) *</label>
                        <input
                            type="number"
                            id="ordered_amount"
                            name="ordered_amount"
                            min="0"
                            max="9999999999.99"
                            step="0.01"
                            value="{{ old('ordered_amount', '0.00') }}"
                            required
                        >
                        @error('ordered_amount')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field">
                        <label for="deposited_amount">Amount Deposited (KES) *</label>
                        <input
                            type="number"
                            id="deposited_amount"
                            name="deposited_amount"
                            min="0"
                            max="9999999999.99"
                            step="0.01"
                            value="{{ old('deposited_amount', '0.00') }}"
                            required
                        >
                        @error('deposited_amount')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="status">Initial Status *</label>
                        <select id="status" name="status" required>
                            <option value="ordered" {{ old('status', 'ordered') === 'ordered' ? 'selected' : '' }}>Ordered</option>
                            <option value="partially_deposited" {{ old('status') === 'partially_deposited' ? 'selected' : '' }}>Partially Deposited</option>
                            <option value="deposited" {{ old('status') === 'deposited' ? 'selected' : '' }}>Deposited in Full</option>
                            <option value="released" {{ old('status') === 'released' ? 'selected' : '' }}>Released</option>
                            <option value="forfeited" {{ old('status') === 'forfeited' ? 'selected' : '' }}>Forfeited</option>
                        </select>
                        <div class="help">
                            Deposit-related statuses are recalculated from the amounts entered.
                            Released and forfeited statuses are preserved.
                        </div>
                        @error('status')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="conditions">Court-Ordered Conditions</label>
                        <textarea
                            id="conditions"
                            name="conditions"
                            maxlength="5000"
                            placeholder="Enter conditions exactly as recorded in the order..."
                        >{{ old('conditions') }}</textarea>
                        @error('conditions')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="field full">
                        <label for="notes">Additional Notes</label>
                        <textarea
                            id="notes"
                            name="notes"
                            maxlength="5000"
                            placeholder="Optional administrative notes..."
                        >{{ old('notes') }}</textarea>
                        @error('notes')
                            <div class="error">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <div class="actions">
                    <button type="submit">Save Bail / Bond Record</button>
                    <a href="{{ route('bail-bonds.index') }}" class="btn">Cancel</a>
                </div>
            </form>
        @endif
    </div>
</div>
</body>
</html>
