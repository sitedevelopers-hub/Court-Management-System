<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Bail & Bond | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        .container {
            max-width: 900px;
            margin: auto;
        }

        .header { margin-bottom: 25px; }

        h1 {
            margin: 0 0 8px;
            color: #0f172a;
            font-size: 28px;
        }

        .subtitle { color: #64748b; }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.06);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .full-width { grid-column: 1 / -1; }

        label {
            font-size: 14px;
            font-weight: bold;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
            background: white;
        }

        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }

        textarea {
            min-height: 100px;
            resize: vertical;
        }

        .help {
            color: #64748b;
            font-size: 12px;
        }

        .errors {
            padding: 14px 18px;
            margin-bottom: 20px;
            border-radius: 8px;
            background: #fee2e2;
            color: #991b1b;
        }

        .errors ul { margin-bottom: 0; }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 25px;
        }

        .btn {
            border: 0;
            padding: 12px 18px;
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
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>

<body>
<div class="container">

    <div class="header">
        <h1>Edit Bail & Bond</h1>
        <div class="subtitle">
            Reference: {{ $bailBond->reference_number }}
        </div>
    </div>

    <div class="card">

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

        <form action="{{ route('bail-bonds.update', $bailBond) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">

                <div class="form-group">
                    <label for="case_id">Court Case *</label>
                    <select id="case_id" name="case_id" required>
                        <option value="">Select a case</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}"
                                {{ old('case_id', $bailBond->case_id) == $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} — {{ $case->case_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="participant_id">Participant *</label>
                    <select id="participant_id" name="participant_id" required>
                        <option value="">Select a participant</option>
                        @foreach($participants as $participant)
                            <option value="{{ $participant->id }}"
                                {{ old('participant_id', $bailBond->participant_id) == $participant->id ? 'selected' : '' }}>
                                {{ $participant->full_name }} ({{ $participant->participant_number }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="bond_type">Bail / Bond Type *</label>
                    <select id="bond_type" name="bond_type" required>
                        @foreach([
                            'cash_bail' => 'Cash Bail',
                            'bond' => 'Bond',
                            'personal_bond' => 'Personal Bond',
                            'other' => 'Other'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('bond_type', $bailBond->bond_type) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label for="order_date">Order Date</label>
                    <input
                        type="date"
                        id="order_date"
                        name="order_date"
                        max="{{ date('Y-m-d') }}"
                        value="{{ old('order_date', $bailBond->order_date?->format('Y-m-d')) }}">
                </div>

                <div class="form-group">
                    <label for="ordered_amount">Ordered Amount (KSh) *</label>
                    <input
                        type="number"
                        id="ordered_amount"
                        name="ordered_amount"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('ordered_amount', $bailBond->ordered_amount) }}">
                </div>

                <div class="form-group">
                    <label for="deposited_amount">Deposited Amount (KSh) *</label>
                    <input
                        type="number"
                        id="deposited_amount"
                        name="deposited_amount"
                        min="0"
                        step="0.01"
                        required
                        value="{{ old('deposited_amount', $bailBond->deposited_amount) }}">
                    <span class="help">The deposited amount cannot exceed the ordered amount.</span>
                </div>

                <div class="form-group">
                    <label for="status">Status *</label>
                    <select id="status" name="status" required>
                        @foreach([
                            'ordered' => 'Ordered',
                            'partially_deposited' => 'Partially Deposited',
                            'deposited' => 'Deposited',
                            'released' => 'Released',
                            'forfeited' => 'Forfeited'
                        ] as $value => $label)
                            <option value="{{ $value }}"
                                {{ old('status', $bailBond->status) === $value ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group full-width">
                    <label for="conditions">Conditions</label>
                    <textarea id="conditions" name="conditions">{{ old('conditions', $bailBond->conditions) }}</textarea>
                </div>

                <div class="form-group full-width">
                    <label for="notes">Additional Notes</label>
                    <textarea id="notes" name="notes">{{ old('notes', $bailBond->notes) }}</textarea>
                </div>

            </div>

            <div class="actions">
                <button type="submit" class="btn btn-save">Save Changes</button>
                <a href="{{ route('bail-bonds.show', $bailBond) }}" class="btn btn-cancel">
                    Cancel
                </a>
                <form action="{{ route('bail-bonds.destroy', $bailBond) }}" method="POST"
                      onsubmit="return confirm('Delete this bail/bond record? This action cannot be undone.')"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Delete Record</button>
                </form>
            </div>
        </form>
    </div>
</div>
</body>
</html>
