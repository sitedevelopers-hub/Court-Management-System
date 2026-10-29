<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Hearing | Court Management System</title>

    <style>
        body {
            background: transparent;
margin: 0;
            padding: 30px;
            color: #243247;
        }

        .container {
            max-width: 800px;
            margin: auto;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 10px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        h1 { color: #17365d; margin-top: 0; }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field { margin-bottom: 16px; }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input, select, textarea {
            width: 100%;
            box-sizing: border-box;
            padding: 11px;
            border: 1px solid #ccd4df;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea {
            min-height: 90px;
            resize: vertical;
        }

        .full { grid-column: 1 / -1; }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .btn {
            padding: 11px 18px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .primary { background: #17365d; color: white; }
        .secondary { background: #e5eaf1; color: #243247; }

        .errors {
            background: #fdecec;
            color: #a11b1b;
            border-radius: 6px;
            padding: 12px 20px;
            margin-bottom: 18px;
        }

        .errors li { margin: 5px 0; }

        @media (max-width: 600px) {
            body { padding: 15px; }
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>

<body>
<div class="container">
    <div class="card">
        <h1>Edit Hearing</h1>
        <p>Update the hearing information below.</p>

        @if($errors->any())
            <div class="errors">
                <strong>Please correct the following:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('hearings.update', $hearing) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="form-grid">
                <div class="field full">
                    <label for="case_id">Case *</label>
                    <select name="case_id" id="case_id" required>
                        <option value="">Select a case</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}"
                                {{ old('case_id', $hearing->case_id) == $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} — {{ $case->case_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="courtroom_id">Courtroom</label>
                    <select name="courtroom_id" id="courtroom_id">
                        <option value="">Not assigned</option>
                        @foreach($courtrooms as $courtroom)
                            <option value="{{ $courtroom->id }}"
                                {{ old('courtroom_id', $hearing->courtroom_id) == $courtroom->id ? 'selected' : '' }}>
                                {{ $courtroom->room_number }}
                                @if($courtroom->court_name)
                                    — {{ $courtroom->court_name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="hearing_type">Hearing Type *</label>
                    <input type="text" name="hearing_type" id="hearing_type"
                           value="{{ old('hearing_type', $hearing->hearing_type) }}"
                           required maxlength="255">
                </div>

                <div class="field">
                    <label for="scheduled_start">Start Date and Time *</label>
                    <input type="datetime-local" name="scheduled_start" id="scheduled_start"
                           value="{{ old('scheduled_start', $hearing->scheduled_start?->format('Y-m-d\TH:i')) }}"
                           required>
                </div>

                <div class="field">
                    <label for="scheduled_end">End Date and Time</label>
                    <input type="datetime-local" name="scheduled_end" id="scheduled_end"
                           value="{{ old('scheduled_end', $hearing->scheduled_end?->format('Y-m-d\TH:i')) }}">
                </div>

                <div class="field">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['scheduled', 'completed', 'adjourned', 'cancelled'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $hearing->status) === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field full">
                    <label for="outcome">Outcome</label>
                    <textarea name="outcome" id="outcome">{{ old('outcome', $hearing->outcome) }}</textarea>
                </div>

                <div class="field full">
                    <label for="adjournment_reason">Adjournment Reason</label>
                    <textarea name="adjournment_reason" id="adjournment_reason">{{ old('adjournment_reason', $hearing->adjournment_reason) }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn primary">Save Changes</button>
                <a href="{{ route('hearings.show', $hearing) }}" class="btn secondary">
                    Cancel
                </a>
                <form action="{{ route('hearings.destroy', $hearing) }}" method="POST"
                      onsubmit="return confirm('Delete this hearing? This action cannot be undone.')"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Delete Hearing</button>
                </form>
            </div>
        </form>
    </div>
</div>
</body>
</html>
