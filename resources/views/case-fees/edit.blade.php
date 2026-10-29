<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Court Fee | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 780px; margin: auto; }
        .card {
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        .form-grid {
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
            font: inherit;
            background: white;
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }
        textarea { min-height: 110px; resize: vertical; }
        .error { color: #b91c1c; font-size: 13px; margin-top: 6px; }
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
            border: 0;
            border-radius: 8px;
            padding: 12px 18px;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
        }
        button { background: #1f6f43; color: white; }
        .btn { background: #e2e8f0; color: #1e293b; }
        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .card { padding: 20px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Edit Court Fee</h1>
        <p class="subtitle">
            Reference: {{ $fee->fee_reference }}
        </p>

        <div class="notice">
            Amount already paid:
            <strong>KES {{ number_format((float) $fee->amount_paid, 2) }}</strong>.
            The total fee cannot be reduced below this amount.
        </div>

        @if ($errors->any())
            <div class="notice" style="background:#fef2f2;color:#991b1b;">
                Please correct the following errors:
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('case-fees.update', $fee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field full">
                    <label for="case_id">Court Case *</label>
                    <select id="case_id" name="case_id" required>
                        <option value="">Select a case</option>
                        @foreach ($cases as $case)
                            <option value="{{ $case->id }}"
                                {{ (string) old('case_id', $fee->case_id) === (string) $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} — {{ $case->case_title }}
                            </option>
                        @endforeach
                    </select>
                    @error('case_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field full">
                    <label for="participant_id">Related Participant</label>
                    <select id="participant_id" name="participant_id">
                        <option value="">None / Not specified</option>
                        @foreach ($participants as $participant)
                            <option value="{{ $participant->id }}"
                                {{ (string) old('participant_id', $fee->participant_id) === (string) $participant->id ? 'selected' : '' }}>
                                {{ $participant->full_name }} — {{ $participant->participant_number }}
                            </option>
                        @endforeach
                    </select>
                    @error('participant_id')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="fee_type">Fee Type *</label>
                    <select id="fee_type" name="fee_type" required>
                        @foreach ([
                            'filing_fee' => 'Filing Fee',
                            'service_fee' => 'Service Fee',
                            'fine' => 'Fine',
                            'court_fee' => 'Court Fee',
                            'bond_bail' => 'Bond / Bail',
                            'other' => 'Other',
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('fee_type', $fee->fee_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                    @error('fee_type')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field">
                    <label for="amount">Total Amount (KES) *</label>
                    <input
                        type="number"
                        id="amount"
                        name="amount"
                        min="0.01"
                        max="9999999999.99"
                        step="0.01"
                        value="{{ old('amount', $fee->amount) }}"
                        required
                    >
                    @error('amount')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field full">
                    <label for="due_date">Due Date</label>
                    <input
                        type="date"
                        id="due_date"
                        name="due_date"
                        value="{{ old('due_date', $fee->due_date?->format('Y-m-d')) }}"
                    >
                    @error('due_date')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="field full">
                    <label for="description">Description *</label>
                    <textarea
                        id="description"
                        name="description"
                        maxlength="2000"
                        required
                    >{{ old('description', $fee->description) }}</textarea>
                    @error('description')
                        <div class="error">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="actions">
                <button type="submit">Save Changes</button>
                <a href="{{ route('case-fees.show', $fee) }}" class="btn">
                    Cancel
                </a>
                <form action="{{ route('case-fees.destroy', $fee) }}" method="POST"
                      onsubmit="return confirm('Delete this fee record? This action cannot be undone.')"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button class="delete-btn" type="submit">Delete Fee</button>
                </form>
            </div>
        </form>
    </div>
</div>
</body>
</html>
