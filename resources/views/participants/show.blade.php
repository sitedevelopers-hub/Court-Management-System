<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participant Details | Court Management System</title>
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
            max-width: 1000px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 20px;
        }

        .label {
            color: #64748b;
            font-size: 13px;
            margin-bottom: 6px;
        }

        .value { font-weight: 600; overflow-wrap: anywhere; }

        .btn {
            display: inline-block;
            padding: 10px 15px;
            border-radius: 7px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            border: 0;
            cursor: pointer;
        }

        .secondary { background: #64748b; }
        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        .table-wrap { overflow-x: auto; }

        @media (max-width: 600px) {
            .details { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="success" style="background:#fee2e2;color:#991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <section class="card">
            <h2>{{ $participant->full_name }}</h2>
            <p>Participant Number: {{ $participant->participant_number }}</p>

            <div class="details">
                <div>
                    <div class="label">Participant Type</div>
                    <div class="value">{{ ucfirst($participant->participant_type) }}</div>
                </div>

                <div>
                    <div class="label">Status</div>
                    <div class="value">{{ ucfirst($participant->status) }}</div>
                </div>

                <div>
                    <div class="label">Email</div>
                    <div class="value">{{ $participant->email ?: 'Not provided' }}</div>
                </div>

                <div>
                    <div class="label">Phone</div>
                    <div class="value">{{ $participant->phone ?: 'Not provided' }}</div>
                </div>

                <div>
                    <div class="label">National ID / Identifier</div>
                    <div class="value">{{ $participant->national_id ?: 'Not provided' }}</div>
                </div>

                <div>
                    <div class="label">Organization / Law Firm</div>
                    <div class="value">{{ $participant->organization ?: 'Not provided' }}</div>
                </div>

                <div>
                    <div class="label">Address</div>
                    <div class="value">{{ $participant->address ?: 'Not provided' }}</div>
                </div>

                <div>
                    <div class="label">Registered On</div>
                    <div class="value">{{ $participant->created_at?->format('d M Y') }}</div>
                </div>
            </div>

            <p style="margin-top:25px;">
                <a class="btn" href="{{ route('participants.edit', $participant) }}">Edit Participant</a>
                <a class="btn secondary" href="{{ route('participants.index') }}">Back to Participants</a>
            </p>
        </section>

        <section class="card">
            <h2>Linked Court Cases</h2>

            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>Case Number</th>
                            <th>Case Title</th>
                            <th>Participant's Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($participant->cases as $case)
                            <tr>
                                <td>{{ $case->case_number }}</td>
                                <td>
                                    <a href="{{ route('cases.show', $case) }}">
                                        {{ $case->case_title }}
                                    </a>
                                </td>
                                <td>{{ $case->pivot->role }}</td>
                                <td>{{ ucfirst($case->filing_status) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4">No court cases linked to this participant yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <section class="card">
            <h2>Financial Records</h2>
            <p>Linked fee records: {{ $participant->fees->count() }}</p>
            <p>Linked bail/bond records: {{ $participant->bailBonds->count() }}</p>
            <p>Detailed financial management will be available in the Finance module.</p>
        </section>
    </main>
</body>
</html>
