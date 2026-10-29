<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule Hearing | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b; }
        header { background: #0f3d2e; color: white; padding: 22px 6%; }
        main { max-width: 850px; margin: 35px auto; padding: 0 20px; }
        .card { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 18px #0f172a0d; }
        .grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 18px; }
        .field { margin-bottom: 16px; }
        label { display: block; font-weight: 600; margin-bottom: 7px; }
        input, select { width: 100%; padding: 12px; border: 1px solid #cbd5e1; border-radius: 7px; font: inherit; }
        .full { grid-column: 1 / -1; }
        .btn { display: inline-block; padding: 12px 18px; border: 0; border-radius: 7px; background: #1f6f43; color: white; text-decoration: none; cursor: pointer; font-size: 15px; }
        .secondary { background: #64748b; }
        .errors { color: #b91c1c; margin-bottom: 18px; }
        .actions { display: flex; gap: 12px; flex-wrap: wrap; margin-top: 12px; }
        .hint { color: #64748b; font-size: 13px; margin-top: 6px; }
        @media (max-width: 600px) { .grid { grid-template-columns: 1fr; } .full { grid-column: auto; } .card { padding: 20px; } }
    </style>
</head>
<body>
@include('partials.nav')

<main>
    <div class="card">
        <h2>Schedule a Hearing</h2>

        @if ($errors->any())
            <div class="errors">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if ($cases->isEmpty())
            <p>No cases are registered yet. Register a case before scheduling a hearing.</p>
            <a class="btn" href="{{ route('cases.create') }}">Register a Case</a>
        @else
            <form method="POST" action="{{ route('hearings.store') }}">
                @csrf

                <div class="grid">
                    <div class="field full">
                        <label for="case_id">Court Case *</label>
                        <select id="case_id" name="case_id" required>
                            <option value="">Select a case</option>
                            @foreach ($cases as $case)
                                <option value="{{ $case->id }}" @selected(old('case_id') == $case->id)>
                                    {{ $case->case_number }} — {{ $case->case_title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field full">
                        <label for="courtroom_id">Courtroom</label>
                        <select id="courtroom_id" name="courtroom_id">
                            <option value="">Not assigned</option>
                            @foreach ($courtrooms as $room)
                                <option value="{{ $room->id }}" @selected(old('courtroom_id') == $room->id)>
                                    {{ $room->room_number }} — {{ $room->court_name }}
                                </option>
                            @endforeach
                        </select>
                        <p class="hint">Only courtrooms marked available appear here.</p>
                    </div>

                    <div class="field">
                        <label for="scheduled_start">Start Date and Time *</label>
                        <input id="scheduled_start" type="datetime-local" name="scheduled_start"
                               value="{{ old('scheduled_start') }}" required>
                    </div>

                    <div class="field">
                        <label for="scheduled_end">End Date and Time</label>
                        <input id="scheduled_end" type="datetime-local" name="scheduled_end"
                               value="{{ old('scheduled_end') }}">
                    </div>

                    <div class="field">
                        <label for="hearing_type">Hearing Type *</label>
                        <select id="hearing_type" name="hearing_type" required>
                            @foreach (['hearing', 'mention', 'trial', 'ruling', 'judgment', 'directions', 'other'] as $type)
                                <option value="{{ $type }}" @selected(old('hearing_type', 'hearing') === $type)>
                                    {{ ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            @foreach (['scheduled', 'completed', 'adjourned', 'cancelled'] as $status)
                                <option value="{{ $status }}" @selected(old('status', 'scheduled') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn" type="submit">Schedule Hearing</button>
                    <a class="btn secondary" href="{{ route('hearings.index') }}">Cancel</a>
                </div>
            </form>
        @endif
    </div>
</main>
</body>
</html>
