<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hearings | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b; }
        header { background: #0f3d2e; color: white; padding: 22px 6%; }
        main { max-width: 1200px; margin: 30px auto; padding: 0 20px; }
        .toolbar { display: flex; flex-wrap: wrap; justify-content: space-between; align-items: center; gap: 15px; margin-bottom: 22px; }
        .search { display: flex; flex-wrap: wrap; gap: 8px; }
        input { padding: 11px; border: 1px solid #cbd5e1; border-radius: 7px; min-width: 240px; }
        .btn { display: inline-block; padding: 10px 14px; border: 0; border-radius: 7px; background: #1f6f43; color: white; text-decoration: none; cursor: pointer; font-size: 14px; }
        .secondary { background: #64748b; }
        .edit { background: #0f766e; }
        .archive { background: #b91c1c; }
        .panel { background: white; padding: 20px; border-radius: 12px; box-shadow: 0 4px 18px #0f172a0d; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; min-width: 850px; }
        th, td { text-align: left; padding: 14px 12px; border-bottom: 1px solid #e2e8f0; }
        th { background: #eef5ef; }
        .status { display: inline-block; padding: 5px 9px; border-radius: 20px; background: #dbeafe; color: #1e40af; font-size: 12px; }
        .completed { background: #dcfce7; color: #166534; }
        .adjourned { background: #fef3c7; color: #92400e; }
        .cancelled { background: #fee2e2; color: #991b1b; }
        .success { background: #dcfce7; color: #166534; padding: 12px; border-radius: 7px; margin-bottom: 18px; }
        .actions { display: flex; flex-wrap: wrap; gap: 7px; }
        .pagination { margin-top: 20px; }
        @media (max-width: 600px) { input { min-width: 0; width: 100%; } .search { width: 100%; } }
    </style>
</head>
<body>
@include('partials.nav')

<main>
    <p style="margin:0 0 14px;"><a class="btn back-btn" href="{{ route('dashboard') }}" style="padding:9px 16px;">← Back to Dashboard</a></p>
    <div class="toolbar">
        <div>
            <h2>Hearings</h2>
            <p>Schedule and manage court hearings.</p>
        </div>
        <a class="btn" href="{{ route('hearings.create') }}">+ Schedule Hearing</a>
    </div>

    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <div class="panel">
        <form method="GET" action="{{ route('hearings.index') }}" class="toolbar">
            <div class="search">
                <input type="search" name="search" value="{{ $search }}"
                       placeholder="Search case number, title or hearing type">
                <button class="btn" type="submit">Search</button>
                <a class="btn secondary" href="{{ route('hearings.index') }}">Clear</a>
            </div>
        </form>

        <table>
            <thead>
                <tr>
                    <th>Case Number</th>
                    <th>Case Title</th>
                    <th>Date & Time</th>
                    <th>Courtroom</th>
                    <th>Hearing Type</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($hearings as $hearing)
                    <tr>
                        <td>{{ $hearing->courtCase?->case_number ?? '—' }}</td>
                        <td>{{ $hearing->courtCase?->case_title ?? '—' }}</td>
                        <td>{{ $hearing->scheduled_start?->format('d M Y, H:i') ?? '—' }}</td>
                        <td>{{ $hearing->courtroom?->room_number ?? 'Not assigned' }}</td>
                        <td>{{ ucfirst($hearing->hearing_type) }}</td>
                        <td>
                            <span class="status {{ $hearing->status }}">
                                {{ ucfirst($hearing->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a class="btn" href="{{ route('hearings.show', $hearing) }}">View</a>
                                <a class="btn edit" href="{{ route('hearings.edit', $hearing) }}">Edit</a>
                                <form method="POST" action="{{ route('hearings.destroy', $hearing) }}"
                                      onsubmit="return confirm('Delete this hearing?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn archive" type="submit">Delete</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7">No hearings found. Schedule a hearing to get started.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <div class="pagination">{{ $hearings->links() }}</div>
    </div>
</main>
</body>
</html>
