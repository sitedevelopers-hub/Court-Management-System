<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Case Details | Court Management System</title>
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

        main { max-width: 1100px; width: 92%; margin: 30px auto; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 14px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .topbar h2 { margin: 0 0 6px; }
        .muted { color: #64748b; }

        .actions { display: flex; gap: 10px; flex-wrap: wrap; }

        .button {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
            background: #176b54;
            color: white;
        }

        .secondary { background: #e2e8f0; color: #334155; }

        .panel {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 16px #0f172a0d;
            margin-bottom: 22px;
        }

        .panel h3 { margin-top: 0; }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .detail-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            margin-bottom: 7px;
            font-weight: bold;
        }

        .detail-value { font-size: 16px; overflow-wrap: anywhere; }

        .status {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1f6f43;
            text-transform: capitalize;
        }

        .description {
            white-space: pre-wrap;
            line-height: 1.6;
            overflow-wrap: anywhere;
        }

        .empty { color: #64748b; padding: 12px 0; }

        table { width: 100%; border-collapse: collapse; }

        th, td {
            text-align: left;
            padding: 12px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        th { color: #64748b; font-size: 12px; text-transform: uppercase; }

        .table-wrap { overflow-x: auto; }

        @media (max-width: 600px) {
            .details { grid-template-columns: 1fr; }
            .panel { padding: 18px; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <div class="topbar">
            <div>
                <h2>{{ $case->case_title }}</h2>
                <div class="muted">Case Number: {{ $case->case_number }}</div>
            </div>
            <div class="actions">
                <a class="button secondary" href="{{ route('cases.index') }}">← All Cases</a>
                <a class="button" href="{{ route('cases.edit', $case) }}">Edit Case</a>
            </div>
        </div>

        @if (session('success'))
            <div class="panel">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="panel" style="background:#fee2e2;color:#991b1b;border-left:4px solid #dc2626;">
                {{ session('error') }}
            </div>
        @endif

        <section class="panel">
            <h3>Case Information</h3>

            <div class="details">
                <div>
                    <div class="detail-label">Case Number</div>
                    <div class="detail-value">{{ $case->case_number }}</div>
                </div>

                <div>
                    <div class="detail-label">Case Type</div>
                    <div class="detail-value">{{ $case->case_type }}</div>
                </div>

                <div>
                    <div class="detail-label">Court</div>
                    <div class="detail-value">{{ $case->court_name ?: 'Not assigned' }}</div>
                </div>

                <div>
                    <div class="detail-label">Status</div>
                    <div class="detail-value">
                        <span class="status">{{ $case->filing_status }}</span>
                    </div>
                </div>

                <div>
                    <div class="detail-label">Filing Date</div>
                    <div class="detail-value">
                        {{ $case->filing_date?->format('d M Y') ?? 'Not recorded' }}
                    </div>
                </div>

                <div>
                    <div class="detail-label">Closure Date</div>
                    <div class="detail-value">
                        {{ $case->closed_date?->format('d M Y') ?? 'Not closed' }}
                    </div>
                </div>
            </div>

            <hr style="border:0;border-top:1px solid #e2e8f0;margin:24px 0;">

            <div class="detail-label">Case Description</div>
            <div class="description">{{ $case->description ?: 'No description recorded.' }}</div>
        </section>

        <section class="panel">
            <h3>Participants</h3>

            @if ($case->participants->isEmpty())
                <p class="empty">No participants have been linked to this case yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Role in Case</th>
                                <th>Participant Type</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($case->participants as $participant)
                                <tr>
                                    <td>{{ $participant->full_name }}</td>
                                    <td>{{ $participant->pivot->role }}</td>
                                    <td>{{ $participant->participant_type }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="panel">
            <h3>Scheduled Hearings</h3>

            @if ($case->hearings->isEmpty())
                <p class="empty">No hearings have been scheduled for this case yet.</p>
            @else
                <div class="table-wrap">
                    <table>
                        <thead>
                            <tr>
                                <th>Date and Time</th>
                                <th>Hearing Type</th>
                                <th>Courtroom</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($case->hearings as $hearing)
                                <tr>
                                    <td>{{ $hearing->scheduled_start?->format('d M Y, H:i') ?? 'Not scheduled' }}</td>
                                    <td>{{ $hearing->hearing_type }}</td>
                                    <td>{{ $hearing->courtroom?->room_number ?? 'Not assigned' }}</td>
                                    <td>{{ ucfirst($hearing->status) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </section>

        <section class="panel">
            <h3>Documents</h3>
            <p class="empty">
                {{ $case->documents->count() }} document(s) currently linked to this case.
            </p>
        </section>

        <section class="panel">
            <h3>Fees and Payments</h3>
            <p class="empty">
                {{ $case->fees->count() }} fee record(s) currently linked to this case.
            </p>
        </section>

        <section class="panel">
            <h3>Bail and Bonds</h3>
            <p class="empty">
                {{ $case->bailBonds->count() }} bail/bond record(s) currently linked to this case.
            </p>
        </section>
    </main>
</body>
</html>
