<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Court Event | Court Management System</title>
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
            background: white;
            padding: 30px;
            border-radius: 14px;
            box-shadow: 0 5px 20px #0f172a12;
        }
        h1 { margin-top: 0; color: #0f172a; }
        .subtitle { color: #64748b; margin-bottom: 25px; }
        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }
        .field { margin-bottom: 18px; }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
        }
        textarea { min-height: 110px; resize: vertical; }
        input:focus, select:focus, textarea:focus {
            outline: 2px solid #93c5fd;
            border-color: #2563eb;
        }
        .full { grid-column: 1 / -1; }
        .error { color: #b91c1c; font-size: 14px; margin-top: 5px; }
        .actions { display: flex; gap: 12px; margin-top: 10px; }
        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: 0;
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
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <h1>Edit Court Event</h1>
    <p class="subtitle">Update the event details, schedule, and reminder.</p>

    @if ($errors->any())
        <div class="error" style="margin-bottom: 18px;">
            Please correct the errors below and try again.
        </div>
    @endif

    <form action="{{ route('court-events.update', $event) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid">
            <div class="field full">
                <label for="title">Event Title *</label>
                <input id="title" name="title" type="text"
                       value="{{ old('title', $event->title) }}" required>
                @error('title') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="case_id">Related Case</label>
                <select id="case_id" name="case_id">
                    <option value="">General event (no case)</option>
                    @foreach ($cases as $case)
                        <option value="{{ $case->id }}"
                            @selected(old('case_id', $event->case_id) == $case->id)>
                            {{ $case->case_number }} — {{ $case->case_title }}
                        </option>
                    @endforeach
                </select>
                @error('case_id') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="event_type">Event Type *</label>
                <select id="event_type" name="event_type" required onchange="toggleOtherType()">
                    @foreach (['hearing' => 'Hearing', 'deadline' => 'Deadline', 'meeting' => 'Meeting', 'reminder' => 'Reminder', 'party' => 'Party', 'other' => 'Other (describe below)'] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('event_type', $event->event_type) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('event_type') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field" id="otherTypeWrap" style="display:none;">
                <label for="event_type_other">Describe the event type *</label>
                <input id="event_type_other" name="event_type_other" type="text"
                       value="{{ old('event_type_other') }}" maxlength="255"
                       placeholder="e.g. Judgment delivery, Case review...">
                @error('event_type_other') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="status">Status *</label>
                <select id="status" name="status" required>
                    @foreach (['planned' => 'Planned', 'pending' => 'Pending', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
                        <option value="{{ $value }}"
                            @selected(old('status', $event->status) === $value)>
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
                @error('status') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="starts_at">Start Date & Time *</label>
                <input id="starts_at" name="starts_at" type="datetime-local"
                       value="{{ old('starts_at', $event->starts_at?->format('Y-m-d\TH:i')) }}"
                       required>
                @error('starts_at') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field">
                <label for="ends_at">End Date & Time</label>
                <input id="ends_at" name="ends_at" type="datetime-local"
                       value="{{ old('ends_at', $event->ends_at?->format('Y-m-d\TH:i')) }}">
                @error('ends_at') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="remind_at">Reminder Date & Time</label>
                <input id="remind_at" name="remind_at" type="datetime-local"
                       value="{{ old('remind_at', $event->remind_at?->format('Y-m-d\TH:i')) }}">
                @error('remind_at') <div class="error">{{ $message }}</div> @enderror
            </div>

            <div class="field full">
                <label for="description">Description / Notes</label>
                <textarea id="description" name="description">{{ old('description', $event->description) }}</textarea>
                @error('description') <div class="error">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="actions">
            <button class="btn primary" type="submit">Save Changes</button>
            <a class="btn secondary" href="{{ route('court-events.show', $event) }}">Cancel</a>
            <form action="{{ route('court-events.destroy', $event) }}" method="POST"
                  onsubmit="return confirm('Delete this event? This action cannot be undone.')"
                  style="display:inline">
                @csrf
                @method('DELETE')
                <button class="delete-btn" type="submit">Delete Event</button>
            </form>
        </div>
    </form>
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
