<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Participant | Court Management System</title>
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

        .form-card {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .07);
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .field { margin-bottom: 18px; }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 7px;
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

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 10px;
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 12px 18px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 15px;
        }

        .btn-secondary { background: #64748b; }
        .errors { color: #b91c1c; margin-bottom: 18px; }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .form-card { padding: 20px; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <div class="form-card">
            <h2>Register New Participant</h2>
            <p>Enter the participant's details below.</p>

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

            <form method="POST" action="{{ route('participants.store') }}">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="participant_number">Participant Number *</label>
                        <input id="participant_number" name="participant_number"
                               value="{{ old('participant_number') }}"
                               required maxlength="255" placeholder="e.g. P-2026-001">
                    </div>

                    <div class="field">
                        <label for="participant_type">Participant Type *</label>
                        <select id="participant_type" name="participant_type" required onchange="toggleOtherType()">
                            <option value="">Select type</option>
                            @foreach (['judge', 'magistrate', 'advocate', 'clerk', 'party', 'witness', 'other'] as $type)
                                <option value="{{ $type }}" @selected(old('participant_type') === $type)>
                                    {{ $type === 'other' ? 'Other (describe below)' : ucfirst($type) }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="field" id="otherTypeWrap" style="display:none;">
                        <label for="participant_type_other">Describe the participant type *</label>
                        <input id="participant_type_other" name="participant_type_other" type="text"
                               value="{{ old('participant_type_other') }}" maxlength="255"
                               placeholder="e.g. Court interpreter, Probation officer...">
                    </div>

                    <div class="field">
                        <label for="first_name">First Name *</label>
                        <input id="first_name" name="first_name"
                               value="{{ old('first_name') }}" required maxlength="255">
                    </div>

                    <div class="field">
                        <label for="middle_name">Middle Name</label>
                        <input id="middle_name" name="middle_name"
                               value="{{ old('middle_name') }}" maxlength="255">
                    </div>

                    <div class="field">
                        <label for="last_name">Last Name *</label>
                        <input id="last_name" name="last_name"
                               value="{{ old('last_name') }}" required maxlength="255">
                    </div>

                    <div class="field">
                        <label for="email">Email Address</label>
                        <input id="email" type="email" name="email"
                               value="{{ old('email') }}" maxlength="255">
                    </div>

                    <div class="field">
                        <label for="phone">Phone Number</label>
                        <input id="phone" name="phone" type="tel"
                               value="{{ old('phone') }}" maxlength="30">
                    </div>

                    <div class="field">
                        <label for="national_id">National ID / Identifier</label>
                        <input id="national_id" name="national_id"
                               value="{{ old('national_id') }}" maxlength="255">
                    </div>

                    <div class="field">
                        <label for="organization">Organization / Law Firm</label>
                        <input id="organization" name="organization"
                               value="{{ old('organization') }}" maxlength="255">
                    </div>

                    <div class="field">
                        <label for="status">Status *</label>
                        <select id="status" name="status" required>
                            <option value="active" @selected(old('status', 'active') === 'active')>Active</option>
                            <option value="inactive" @selected(old('status') === 'inactive')>Inactive</option>
                        </select>
                    </div>

                    <div class="field full">
                        <label for="address">Address</label>
                        <textarea id="address" name="address">{{ old('address') }}</textarea>
                    </div>
                </div>

                <div class="actions">
                    <button class="btn" type="submit">Register Participant</button>
                    <a class="btn btn-secondary" href="{{ route('participants.index') }}">Cancel</a>
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
