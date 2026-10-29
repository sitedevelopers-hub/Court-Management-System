<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Participants | Court Management System</title>
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
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .toolbar {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            margin-bottom: 22px;
        }

        .toolbar form {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        input {
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            min-width: 240px;
        }

        .btn {
            display: inline-block;
            padding: 10px 14px;
            border: 0;
            border-radius: 7px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .secondary { background: #64748b; }
        .edit { background: #0f766e; }
        .archive { background: #b91c1c; }

        .panel {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 18px rgba(15, 23, 42, .06);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
        }

        th { background: #eef5ef; }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
        }

        .inactive { background: #fee2e2; color: #991b1b; }
        .success {
            background: #dcfce7;
            color: #166534;
            padding: 12px;
            border-radius: 7px;
            margin-bottom: 18px;
        }

        .actions { display: flex; flex-wrap: wrap; gap: 7px; }
        .pagination { margin-top: 20px; }

        @media (max-width: 600px) {
            input { min-width: 0; width: 100%; }
            .toolbar form { width: 100%; }
        }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <p style="margin:0 0 14px;"><a class="btn back-btn" href="{{ route('dashboard') }}" style="padding:9px 16px;">← Back to Dashboard</a></p>
        <div class="toolbar">
            <div>
                <h2>Participants</h2>
                <p>Manage judges, magistrates, advocates, clerks, parties and witnesses.</p>
            </div>

            <a class="btn" href="{{ route('participants.create') }}">+ Register Participant</a>
        </div>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="success" style="background:#fee2e2;color:#991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <div class="panel">
            <form method="GET" action="{{ route('participants.index') }}" class="toolbar">
                <input type="search" name="search" value="{{ $search }}"
                       placeholder="Search number, name, email or organization">
                <button class="btn" type="submit">Search</button>
                <a class="btn secondary" href="{{ route('participants.index') }}">Clear</a>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Participant No.</th>
                        <th>Name</th>
                        <th>Type</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($participants as $participant)
                        <tr>
                            <td>{{ $participant->participant_number }}</td>
                            <td>{{ $participant->full_name }}</td>
                            <td>{{ ucfirst($participant->participant_type) }}</td>
                            <td>{{ $participant->email ?: '—' }}</td>
                            <td>{{ $participant->phone ?: '—' }}</td>
                            <td>
                                <span class="status {{ $participant->status === 'inactive' ? 'inactive' : '' }}">
                                    {{ ucfirst($participant->status) }}
                                </span>
                            </td>
                            <td>
                                <div class="actions">
                                    <a class="btn" href="{{ route('participants.show', $participant) }}">View</a>
                                    <a class="btn edit" href="{{ route('participants.edit', $participant) }}">Edit</a>
                                    <form method="POST" action="{{ route('participants.destroy', $participant) }}"
                                          onsubmit="return confirm('Delete this participant?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn archive" type="submit">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">No participants found. Register your first participant to get started.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $participants->links() }}
            </div>
        </div>
    </main>
</body>
</html>
