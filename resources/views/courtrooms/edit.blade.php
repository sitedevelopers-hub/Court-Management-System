<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Courtroom | Court Management System</title>

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
            margin-top: 10px;
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
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
<div class="container">
    <div class="card">
        <h1>Edit Courtroom</h1>
        <p>Update the courtroom information below.</p>

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

        <form action="{{ route('courtrooms.update', $courtroom) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="grid">
                <div class="field">
                    <label for="room_number">Room Number *</label>
                    <input type="text" name="room_number" id="room_number"
                           value="{{ old('room_number', $courtroom->room_number) }}"
                           maxlength="255" required>
                </div>

                <div class="field">
                    <label for="court_name">Court Name *</label>
                    <input type="text" name="court_name" id="court_name"
                           value="{{ old('court_name', $courtroom->court_name) }}"
                           maxlength="255" required>
                </div>

                <div class="field">
                    <label for="capacity">Room Capacity</label>
                    <input type="number" name="capacity" id="capacity"
                           value="{{ old('capacity', $courtroom->capacity) }}" min="1">
                </div>

                <div class="field">
                    <label for="location">Location</label>
                    <input type="text" name="location" id="location"
                           value="{{ old('location', $courtroom->location) }}"
                           maxlength="255">
                </div>

                <div class="field full">
                    <label for="status">Status *</label>
                    <select name="status" id="status" required>
                        @foreach(['available', 'unavailable', 'maintenance'] as $status)
                            <option value="{{ $status }}"
                                {{ old('status', $courtroom->status) === $status ? 'selected' : '' }}>
                                {{ ucfirst($status) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="field full">
                    <label for="notes">Additional Notes</label>
                    <textarea name="notes" id="notes">{{ old('notes', $courtroom->notes) }}</textarea>
                </div>
            </div>

            <div class="actions">
                <button type="submit" class="btn primary">Save Changes</button>
                <a href="{{ route('courtrooms.show', $courtroom) }}"
                   class="btn secondary">Cancel</a>
                <form action="{{ route('courtrooms.destroy', $courtroom) }}" method="POST"
                      onsubmit="return confirm('Delete this courtroom? This action cannot be undone.')"
                      style="display:inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="delete-btn">Delete Courtroom</button>
                </form>
            </div>
        </form>
    </div>
</div>
</body>
</html>
