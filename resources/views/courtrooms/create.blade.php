<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Courtroom | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #243247;
        }

        .container { max-width: 800px; margin: auto; }

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

        .field { margin-bottom: 16px; }
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
            border: none;
            border-radius: 6px;
            padding: 12px 18px;
            text-decoration: none;
            cursor: pointer;
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
        <h1>Register Courtroom</h1>
        <p>Enter the courtroom details below.</p>

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

        <form action="{{ route('courtrooms.store') }}" method="POST">
            @csrf

            <div class="grid">
                <div class="field">
                    <label for="room_number">Room Number *</label>
                    <input type="text" name="room_number" id="room_number"
                           value="{{ old('room_number') }}" required maxlength="255">
                </div>

                <div class="field">
                    <label for="court_name">Court Name *</label>
                    <input type="text" name="court_name" id="court_name"
                           value="{{ old('court_name') }}" required maxlength="255">
                </div>

                <div class="field">
                    <label for="capacity">Room Capacity</label>
                    <input type="number" name="capacity" id="capacity"
                           value="{{ old('capacity') }}" min="1">
                </div>

                <div class="field">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location') }}" maxlength="255">
                </div>

                <div class="field full">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        <option value="available"
                            {{ old('status', 'available') === 'available' ? 'selected' : '' }}>
                            Available
                        </option>
                        <option value="unavailable"
                            {{ old('status') === 'unavailable' ? 'selected' : '' }}>
                            Unavailable
                        </option>
                        <option value="maintenance"
                            {{ old('status') === 'maintenance' ? 'selected' : '' }}>
                            Under Maintenance
                        </option>
                    </select>
                </div>

                <div class="field full">
                    <label for="notes">Additional Notes</label>
                    <textarea name="notes" id="notes">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn primary">Save Courtroom</button>
                <a href="{{ route('courtrooms.index') }}" class="btn secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
</body>
</html>
