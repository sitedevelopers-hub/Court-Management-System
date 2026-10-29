<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Court Fee | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 850px;
            margin: auto;
            padding: 30px;
            background: white;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; color: #0f172a; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        .notice {
            padding: 13px;
            margin-bottom: 22px;
            border-radius: 8px;
            background: #eff6ff;
            color: #1e40af;
            line-height: 1.5;
            font-size: 14px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
        }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }
        textarea { min-height: 100px; resize: vertical; }
        .full { grid-column: 1 / -1; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 5px; }
        .hint { color: #64748b; font-size: 13px; margin-top: 6px; }
        .actions { display: flex; gap: 12px; margin-top: 10px; flex-wrap: wrap; }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            cursor: pointer;
            font-size: 14px;
        }
        .primary { background: #1f6f43; color: white; }
        .secondary { background: #e2e8f0; color: #1e293b; }
        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .container { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Register Court Fee</h1>
    <p class="subtitle">Create a fee record linked to a court case.</p>

    <div class="notice">
        A unique fee reference will be generated automatically.
        Registering a fee does not record a payment or issue a receipt.
    </div>

    @if ($errors->any())
        <div class="error" style="margin-bottom: 18px;">
            Please correct the errors below and try again.
        </div>
    @endif

    <form action="{{ route('case-fees.store') }}" method="POST">
        @csrf

        <div class="grid">
            <div class="field full">
                <label for="case_id">Court Case *</label>
                <select id="case_id" name="case_id" required>
                    <option value="">Select a case</option>
                    @foreach ($cases as $case)
                        <option value="{{ $case->id }}"
                            @selected(old('case_id') == $case->id)>
                            {{ $case->case_number }} — {{ $case->case_title }}
                        </option>
                    @endforeach
                </select>
                @error('case_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="participant_id">Related Participant (Optional)</label>
                <select id="participant_id" name="participant_id">
                    <option value="">No specific participant</option>
                    @foreach ($participants as $participant)
                        <option value="{{ $participant->id }}"
                            @selected(old('participant_id') == $participant->id)>
                            {{ $participant->first_name }}
                            {{ $participant->middle_name }}
                            {{ $participant->last_name }}
                            — {{ ucwords(str_replace('_', ' ', $participant->participant_type)) }}
                        </option>
                    @endforeach
                </select>
                @error('participant_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="fee_type">Fee Type *</label>
                <select id="fee_type" name="fee_type" required>
                    <option value="">Select fee type</option>
                    <option value="filing_fee" @selected(old('fee_type') === 'filing_fee')>Filing Fee</option>
                    <option value="service_fee" @selected(old('fee_type') === 'service_fee')>Service Fee</option>
                    <option value="fine" @selected(old('fee_type') === 'fine')>Fine</option>
                    <option value="court_fee" @selected(old('fee_type') === 'court_fee')>Court Fee</option>
                    <option value="bond_bail" @selected(old('fee_type') === 'bond_bail')>Bond / Bail</option>
                    <option value="other" @selected(old('fee_type') === 'other')>Other</option>
                </select>
                @error('fee_type') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="amount">Amount (KES) *</label>
                <input id="amount" name="amount" type="number"
                       min="0.01" max="9999999999.99" step="0.01"
                       value="{{ old('amount') }}" required placeholder="0.00">
                @error('amount') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="due_date">Payment Due Date (Optional)</label>
                <input id="due_date" name="due_date" type="date"
                       value="{{ old('due_date') }}">
                @error('due_date') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="description">Description *</label>
                <textarea id="description" name="description" required maxlength="2000"
                          placeholder="Explain the purpose of this fee...">{{ old('description') }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">Register Fee</button>
            <a class="btn secondary" href="{{ route('case-fees.index') }}">Cancel</a>
        </div>
    </form>
</div>
</body>
</html>
