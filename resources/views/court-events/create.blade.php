<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Court Event | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #243247;
        }

        .container { max-width: 850px; margin: auto; }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,.06);
        }

        h1 { color: #17365d; margin-top: 0; }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field { margin-bottom: 15px; }
        .full { grid-column: 1 / -1; }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ccd4df;
            border-radius: 6px;
            font-size: 14px;
        }

        textarea { min-height: 90px; resize: vertical; }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        .btn {
            display: inline-block;
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
            padding: 12px 20px;
            border-radius: 6px;
            margin-bottom: 18px;
        }

        @media (max-width: 600px) {
            body { padding: 15px; }
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Create Court Event</h1>
        <p>Record a court deadline, meeting, reminder, or other event.</p>

        @if($errors->any())
            <div class="errors">
                <strong>Please correct these errors:</strong>
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('court-events.store') }}" method="POST">
            @csrf

            <div class="grid">
                <div class="field full">
                    <label for="title">Event Title *</label>
                    <input type="text" name="title" id="title"
                           value="{{ old('title') }}" maxlength="255" required>
                </div>

                <div class="field full">
                    <label for="case_id">Related Case (optional)</label>
                    <select name="case_id" id="case_id">
                        <option value="">No case linked</option>
                        @foreach($cases as $case)
                            <option value="{{ $case->id }}"
                                {{ old('case_id') == $case->id ? 'selected' : '' }}>
                                {{ $case->case_number }} — {{ $case->case_title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="event_type">Event Type *</label>
                    <select name="event_type" id="event_type" required onchange="toggleOtherType()">
                        @foreach(['hearing', 'deadline', 'meeting', 'reminder', 'party', 'other'] as $type)
                            <option value="{{ $type }}"
                                {{ old('event_type', 'deadline') === $type ? 'selected' : '' }}>
                                {{ $type === 'other' ? 'Other (describe below)' : ucfirst($type) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field" id="otherTypeWrap" style="display:none;">
                    <label for="event_type_other">Describe the event type *</label>
                    <input type="text" name="event_type_other" id="event_type_other"
                           value="{{ old('event_type_other') }}" maxlength="255"
                           placeholder="e.g. Judgment delivery, Case review...">
                </div>

                <div class="field">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['planned', 'pending', 'completed', 'cancelled'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', 'pending') === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field">
                    <label for="starts_at">Start Date and Time *</label>
                    <input type="datetime-local" name="starts_at" id="starts_at"
                           value="{{ old('starts_at') }}" required>
                </div>

                <div class="field">
                    <label for="ends_at">End Date and Time</label>
                    <input type="datetime-local" name="ends_at" id="ends_at"
                           value="{{ old('ends_at') }}">
                </div>

                <div class="field full">
                    <label for="remind_at">Reminder Date and Time</label>
                    <input type="datetime-local" name="remind_at" id="remind_at"
                           value="{{ old('remind_at') }}">
                    <small>Choose a reminder time that is no later than the event start.</small>
                </div>

                <div class="field full">
                    <label for="description">Description</label>
                    <textarea name="description" id="description">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn primary">Save Event</button>
                <a href="{{ route('court-events.index') }}" class="btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<script>
    function toggleOtherType() {
        const wrap = document.getElementById('otherTypeWrap');
        const input = document.getElementById('event_type_other');
        const show = document.getElementById('event_type').value === 'other';
        wrap.style.display = show ? 'block' : 'none';
        input.required = show;
        if (!show) input.value = '';
    }
    document.addEventListener('DOMContentLoaded', toggleOtherType);
</script>
</body>
</html>
