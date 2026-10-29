<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Courtrooms | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #243247;
        }

        .container { max-width: 1200px; margin: auto; }

        .backbar { margin-bottom: 14px; }

        .backtop {
            display: inline-block;
            padding: 10px 18px;
            border-radius: 10px;
            background: linear-gradient(90deg, #f8fdfa, #e3f4ea);
            border: 1px solid #b7ddc9;
            color: #14532d;
            font-weight: 700;
            font-size: 13.5px;
            text-decoration: none;
            box-shadow: 0 3px 10px rgba(20, 83, 45, .10);
            transition: background .18s ease, color .18s ease, transform .13s ease;
        }

        .backtop:hover {
            background: linear-gradient(90deg, #14532d, #1d7a46);
            color: #fff;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(20, 83, 45, .25);
        }

        .header, .table-card {
            background: linear-gradient(150deg, #d7f0e2 0%, #eefaf3 40%, #c9e8d8 100%);
            padding: 24px;
            border-radius: 14px;
            border: 1px solid #b7ddc9;
            box-shadow: 0 8px 22px rgba(20, 83, 45, .12);
            margin-bottom: 22px;
        }

        h1 { color: #14532d; margin-top: 0; }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .search {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        input {
            padding: 11px;
            border: 1px solid #ccd4df;
            border-radius: 6px;
            min-width: 220px;
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border: none;
            border-radius: 9px;
            text-decoration: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            transition: transform .13s ease, box-shadow .15s ease, background .18s ease;
        }

        .btn:hover { transform: translateY(-1px); box-shadow: 0 5px 14px rgba(20, 83, 45, .18); }

        .primary { background: #14532d; color: white; }
        .primary:hover { background: #0b3d2e; }
        .secondary { background: #e5eaf1; color: #243247; }
        .danger { background: #b42318; color: white; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #edf0f4;
        }

        th {
            background: #eef5ef;
            color: #526176;
            font-size: 12px;
            text-transform: uppercase;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            font-size: 12px;
            background: #e7f7ed;
            color: #176b38;
        }

        .unavailable, .maintenance {
            background: #fff0d9;
            color: #8a5100;
        }

        .alert {
            padding: 12px 15px;
            border-radius: 6px;
            margin-top: 15px;
        }

        .success { background: #e7f7ed; color: #176b38; }
        .error { background: #fdecec; color: #a11b1b; }

        .actions { display: flex; gap: 6px; flex-wrap: wrap; }

        .pagination { margin-top: 20px; }

        @media (max-width: 600px) {
            body { padding: 14px; }
            .header, .table-card { padding: 16px; }
            input { min-width: 0; width: 100%; }
        }
    </style>
</head>
<body>
<div class="container">

    <div class="backbar">
        <a href="{{ route('dashboard') }}" class="backtop">← Back to Dashboard</a>
    </div>

    <div class="header">
        <div class="topbar">
            <div>
                <h1>Courtroom Management</h1>
                <p>Register and manage courtrooms and their availability.</p>
            </div>

            <a href="{{ route('courtrooms.create') }}" class="btn primary">
                + Register Courtroom
            </a>
        </div>

        @if(session('success'))
            <div class="alert success">{{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="alert error">{{ session('error') }}</div>
        @endif

        <form action="{{ route('courtrooms.index') }}" method="GET" class="search">
            <input type="text" name="search" value="{{ $search }}"
                   placeholder="Search room, court or location">

            <button type="submit" class="btn primary">Search</button>

            @if($search)
                <a href="{{ route('courtrooms.index') }}" class="btn secondary">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="table-card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Room Number</th>
                    <th>Court Name</th>
                    <th>Location</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($courtrooms as $courtroom)
                    <tr>
                        <td>{{ $courtroom->room_number }}</td>
                        <td>{{ $courtroom->court_name }}</td>
                        <td>{{ $courtroom->location ?: '—' }}</td>
                        <td>{{ $courtroom->capacity ?? '—' }}</td>
                        <td>
                            <span class="status {{ $courtroom->status }}">
                                {{ ucfirst($courtroom->status) }}
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <a href="{{ route('courtrooms.show', $courtroom) }}"
                                   class="btn secondary">View</a>

                                <a href="{{ route('courtrooms.edit', $courtroom) }}"
                                   class="btn primary">Edit</a>

                                <form action="{{ route('courtrooms.destroy', $courtroom) }}"
                                      method="POST"
                                      onsubmit="return confirm('Delete this courtroom?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn danger">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6">No courtrooms found. Register a courtroom to get started.</td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $courtrooms->links() }}
        </div>
    </div>

</div>
</body>
</html>