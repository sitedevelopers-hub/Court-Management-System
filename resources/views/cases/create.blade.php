<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Case | Court Management System</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        header {
            background: #0f3d2e;
            color: white;
            padding: 22px 5%;
        }

        header h1 { margin: 0 0 6px; font-size: 24px; }
        header p { margin: 0; color: #cbd5e1; }

        main { max-width: 900px; width: 92%; margin: 30px auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .topbar h2 { margin: 0; }

        a {
            color: #176b54;
            text-decoration: none;
            font-weight: bold;
        }

        .panel {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 4px 16px #0f172a0d;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .field {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .full { grid-column: 1 / -1; }

        label { font-weight: bold; font-size: 14px; }

        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font: inherit;
            background: white;
        }

        textarea { min-height: 110px; resize: vertical; }

        .required { color: #dc2626; }

        .error {
            color: #b91c1c;
            font-size: 13px;
        }

        .alert {
            background: #fee2e2;
            color: #991b1b;
            padding: 14px;
            border-radius: 7px;
            margin-bottom: 20px;
        }

        .form-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
            flex-wrap: wrap;
        }

        button, .cancel {
            border: 0;
            padding: 12px 18px;
            border-radius: 7px;
            font-weight: bold;
            cursor: pointer;
            font-size: 14px;
        }

        button {
            background: #176b54;
            color: white;
        }

        .cancel {
            background: #e2e8f0;
            color: #334155;
            text-decoration: none;
        }

        @media (max-width: 600px) {
            .form-grid { grid-template-columns: 1fr; }
            .full { grid-column: auto; }
            .panel { padding: 18px; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <div class="topbar">
            <div>
                <h2>Register New Case</h2>
                <p>Enter the case details below.</p>
            </div>
            <a href="{{ route('cases.index') }}">← Back to Cases</a>
        </div>

        @if ($errors->any())
            <div class="alert">
                <strong>Please correct the following errors:</strong>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <section class="panel">
            <form action="{{ route('cases.store') }}" method="POST">
                @csrf

                <div class="form-grid">
                    <div class="field">
                        <label for="case_number">Case Number <span class="required">*</span></label>
                        <input id="case_number" name="case_number" value="{{ old('case_number') }}"
                               placeholder="e.g. CM-2026-001" required maxlength="255">
                        @error('case_number') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="case_type">Case Type <span class="required">*</span></label>
                        <select id="case_type" name="case_type" required onchange="toggleOtherType()">
                            <option value="">Select case type</option>
                            @foreach (['Criminal', 'Civil', 'Family', 'Commercial', 'Land', 'Employment', 'Other'] as $type)
                                <option value="{{ $type }}" @selected(old('case_type') === $type)>
                                    {{ $type === 'Other' ? 'Other (describe below)' : $type }}
                                </option>
                            @endforeach
                        </select>
                        @error('case_type') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field" id="otherTypeWrap" style="display:none;">
                        <label for="case_type_other">Describe the case type <span class="required">*</span></label>
                        <input id="case_type_other" name="case_type_other" type="text"
                               value="{{ old('case_type_other') }}" maxlength="255"
                               placeholder="e.g. Constitutional, Small Claims...">
                        @error('case_type_other') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field full">
                        <label for="case_title">Case Title <span class="required">*</span></label>
                        <input id="case_title" name="case_title" value="{{ old('case_title') }}"
                               placeholder="Enter the case title" required maxlength="255">
                        @error('case_title') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="court_name">Court Name</label>
                        <input id="court_name" name="court_name" value="{{ old('court_name') }}"
                               placeholder="Enter court name" maxlength="255">
                        @error('court_name') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="filing_status">Case Status <span class="required">*</span></label>
                        <select id="filing_status" name="filing_status" required>
                            @foreach (['pending', 'active', 'adjourned', 'closed'] as $status)
                                <option value="{{ $status }}"
                                    @selected(old('filing_status', 'pending') === $status)>
                                    {{ ucfirst($status) }}
                                </option>
                            @endforeach
                        </select>
                        @error('filing_status') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="filing_date">Filing Date</label>
                        <input type="date" id="filing_date" name="filing_date"
                               value="{{ old('filing_date') }}">
                        @error('filing_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field">
                        <label for="closed_date">Closure Date</label>
                        <input type="date" id="closed_date" name="closed_date"
                               value="{{ old('closed_date') }}">
                        @error('closed_date') <span class="error">{{ $message }}</span> @enderror
                    </div>

                    <div class="field full">
                        <label for="description">Case Description</label>
                        <textarea id="description" name="description"
                                  placeholder="Enter relevant case details...">{{ old('description') }}</textarea>
                        @error('description') <span class="error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit">Register Case</button>
                    <a class="cancel" href="{{ route('cases.index') }}">Cancel</a>
                </div>
            </form>
        </section>
    </main>

    <script>
        function toggleOtherType() {
            const wrap = document.getElementById('otherTypeWrap');
            const input = document.getElementById('case_type_other');
            const show = document.getElementById('case_type').value === 'Other';
            wrap.style.display = show ? 'block' : 'none';
            input.required = show;
            if (!show) input.value = '';
        }
        document.addEventListener('DOMContentLoaded', toggleOtherType);
    </script>
</body>
</html>
