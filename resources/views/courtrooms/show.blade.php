<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courtroom Details | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #243247;
        }

        .container { max-width: 950px; margin: auto; }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 14px rgba(0,0,0,.05);
            margin-bottom: 22px;
        }

        h1, h2 { color: #17365d; }
        h1 { margin-top: 0; }

        .details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 16px;
        }

        .detail {
            background: #eef5ef;
            padding: 15px;
            border-radius: 7px;
            overflow-wrap: anywhere;
        }

        .detail strong {
            display: block;
            color: #526176;
            font-size: 12px;
            margin-bottom: 8px;
            text-transform: uppercase;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 6px;
            text-decoration: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }

        .primary { background: #17365d; color: white; }
        .secondary { background: #e5eaf1; color: #243247; }

        .alert {
            background: #e7f7ed;
            color: #176b38;
            padding: 12px;
            border-radius: 6px;
            margin-bottom: 16px;
        }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 600px;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #edf0f4;
        }

        th { background: #eef5ef; }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .card { padding: 17px; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="card">
        <h1>Courtroom Details</h1>
        <p>View courtroom information and its recent hearing schedule.</p>

        @if(session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        <div class="details">
            <div class="detail">
                <strong>Room Number</strong>
                {{ $courtroom->room_number }}
            </div>

            <div class="detail">
                <strong>Court Name</strong>
                {{ $courtroom->court_name }}
            </div>

            <div class="detail">
                <strong>Location</strong>
                {{ $courtroom->location ?: 'Not specified' }}
            </div>

            <div class="detail">
                <strong>Capacity</strong>
                {{ $courtroom->capacity ?? 'Not specified' }}
            </div>

            <div class="detail">
                <strong>Status</strong>
                {{ ucfirst($courtroom->status) }}
            </div>

            <div class="detail">
                <strong>Registered On</strong>
                {{ $courtroom->created_at?->format('d M Y') ?? '—' }}
            </div>

            <div class="detail" style="grid-column: 1 / -1;">
                <strong>Additional Notes</strong>
                {{ $courtroom->notes ?: 'No additional notes' }}
            </div>
        </div>

        <div class="actions">
            <a href="{{ route('courtrooms.index') }}" class="btn secondary">
                Back to Courtrooms
            </a>

            <a href="{{ route('courtrooms.edit', $courtroom) }}" class="btn primary">
                Edit Courtroom
            </a>
        </div>
    </div>

    <div class="card">
        <h2>Recent Hearings</h2>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Case Number</th>
                    <th>Case Title</th>
                    <th>Date and Time</th>
                    <th>Hearing Type</th>
                    <th>Status</th>
                </tr>
                </thead>

                <tbody>
                @forelse($courtroom->hearings as $hearing)
                    <tr>
                        <td>{{ $hearing->courtCase?->case_number ?? '—' }}</td>
                        <td>{{ $hearing->courtCase?->case_title ?? '—' }}</td>
                        <td>{{ $hearing->scheduled_start?->format('d M Y, H:i') ?? '—' }}</td>
                        <td>{{ ucfirst($hearing->hearing_type) }}</td>
                        <td>{{ ucfirst($hearing->status) }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">No hearings are recorded for this courtroom yet.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
</body>
</html>
