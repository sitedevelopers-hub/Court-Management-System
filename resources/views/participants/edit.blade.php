<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Participant | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        header {
            background: #0f3d2e;
            color: white;
            padding: 22px 6%;
        }

        main {
            max-width: 900px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field { margin-bottom: 16px; }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: 600;
        }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
        }

        textarea { min-height: 95px; resize: vertical; }
        .full { grid-column: 1 / -1; }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            border: 0;
            border-radius: 7px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        .secondary { background: #64748b; }
        .errors { color: #b91c1c; margin-bottom: 18px; }

        .actions {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            margin-top: 12px;
        }

        @media (max-width: 600px) {
            .grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .card { padding: 20px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 10px 16px; border-radius: 7px; cursor: pointer; font-size: 14px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <div class="card">
            <h2>Edit {{ $participant->full_name }}</h2>

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

            <form method="POST" action="{{ route('participants.update', $participant) }}">
                @csrf
                @method('PUT')

                <div class="grid">
                    <div class="field">
                        <label for="participant_number">Participant Number *</label>
                        <input id="participant_number" name="participant_number"
                               value="{{ old('participant_number', $participant->participant_number) }}"
                               maxlength="255" required>
                    </div>

                    <div class="field">
                        <label for="participant_type">Participant Type *</label>
                        <select id="participant_type" name="participant_type" required>
                            @foreach (['judge', 'magistrate', 'advocate', 'clerk', 'party', 'witness', 'other'] as $type)
                                <option value="{{ $type }}"
                                    @selected(old('participant_type', $participant->participant_type) === $type)>
                                    {{ $type === 'other' ? 'Other (describe below)' : ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div id="otherTypeWrap" style="display:none;">
                        <label for="participant_type_other">Describe the participant type *</label>
                        <input id="participant_type_other" name="participant_type_other" type="text"
                               value="{{ old('participant_type_other') }}" maxlength="255"
                               placeholder="e.g. Court interpreter, Probation officer...">
                    </div>

                    <div class="field">
                        <label for="first_name">First Name *</label>
                        <input id="first_name" name="first_name"
                               value="{{ old('first_name', $participant->first_name) }}"
                               maxlength="255" required>
                    </div>

                    <div class="field">
                        <label for="middle_name">Middle Name</label>
                        <input id="middle_name" name="middle_name"
                               value="{{ old('middle_name', $participant->middle_name) }}"
                               maxlength="255">
                    </div>

                    <div class="field">
                        <label for="last_name">Last Name *</label>
                        <input id="last_name" name="last_name"
                               value="{{ old('last_name', $participant->last_name) }}"
                               maxlength="255" required>
                    </div>

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email', $participant->email) }}"
                               maxlength="255">
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input id="phone" type="tel" name="phone"
                               value="{{ old('phone', $participant->phone) }}"
                               maxlength="30">
                    </div>

                    <div class="field">
                        <label for="national_id">National ID / Identifier</label>
                        <input id="national_id" name="national_id"
                               value="{{ old('national_id', $participant->national_id) }}"
                               maxlength="255">
                    </div>

                    <div class="field">
                        <label for="organization">Organization / Law Firm</label>
                        <input id="organization" name="organization"
                               value="{{ old('organization', $participant->organization) }}"
                               maxlength="255">
                    </div>

                    <div class="field">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="active"
                                @selected(old('status', $participant->status) === 'active')>
                                Active
                            </option>
                            <option value="inactive"
                                @selected(old('status', $participant->status) === 'inactive')>
                                Inactive
                            </option>
                        </select>
                    </div>

                    <div class="field full">
                        <label for="address">Address</label>
                        <textarea id="address" name="address">{{ old('address', $participant->address) }}</textarea>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn" type="submit">Save Changes</button>
                    <a class="btn secondary"
                       href="{{ route('participants.show', $participant) }}">Cancel</a>
                    <form action="{{ route('participants.destroy', $participant) }}" method="POST"
                          onsubmit="return confirm('Delete this participant?')"
                          style="display:inline">
                        @csrf
                        @method('DELETE')
                        <button class="delete-btn" type="submit">Delete</button>
                    </form>
                </div>
            </form>
        </div>
    </main>

    <script>
        function toggleOtherType() {
            const wrap = document.getElementById('otherTypeWrap');
            const input = document.getElementById('participant_type_other');
            const show = document.getElementById('participant_type').value === 'other';
            wrap.style.display = show ? 'block' : 'none';
            input.required = show;
            if (!show) input.value = '';
        }
        document.addEventListener('DOMContentLoaded', toggleOtherType);
    </script>
</body>
</html>
