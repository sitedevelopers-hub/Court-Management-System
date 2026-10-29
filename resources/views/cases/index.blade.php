<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Court Management System | Cases</title>
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

        main {
            width: 90%;
            max-width: 1200px;
            margin: 32px auto;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .topbar h2 { margin: 0; }

        a.button {
            display: inline-block;
            padding: 11px 16px;
            background: #176b54;
            color: white;
            text-decoration: none;
            border-radius: 7px;
            font-weight: bold;
        }

        .panel {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 16px #0f172a0d;
            overflow-x: auto;
        }

        .search {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        input {
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            width: 100%;
            max-width: 360px;
        }

        button {
            padding: 11px 17px;
            background: #0f3d2e;
            color: white;
            border: 0;
            border-radius: 7px;
            cursor: pointer;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 750px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
        }

        th {
            background: #eef5ef;
            color: #475569;
            font-size: 13px;
            text-transform: uppercase;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            background: #e0f2fe;
            color: #075985;
            border-radius: 20px;
            font-size: 12px;
            text-transform: capitalize;
        }

        .actions a {
            color: #176b54;
            margin-right: 10px;
            font-weight: bold;
            text-decoration: none;
        }

        .alert {
            padding: 12px;
            margin-bottom: 16px;
            border-radius: 7px;
            background: #dcfce7;
            color: #166534;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #64748b;
        }

        .pagination {
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            main { width: 94%; margin: 20px auto; }
            .panel { padding: 14px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 8px 14px; border-radius: 7px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>
<body>
    @include('partials.nav')

<main>
        <div class="topbar">
            <div>
                <h2>Court Cases</h2>
                <p>View and manage registered cases.</p>
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap;">
                <a class="button" href="{{ route('cases.create') }}">+ Register New Case</a>
                <a class="btn back-btn" href="{{ route('dashboard') }}" style="padding:11px 16px;">← Back to Dashboard</a>
            </div>
        </div>

        @if (session('success'))
            <div class="alert">{{ session('success') }}</div>
        @endif

        @if (session('error'))
            <div class="alert" style="background:#fee2e2;color:#991b1b;">
                {{ session('error') }}
            </div>
        @endif

        <section class="panel">
            <form class="search" action="{{ route('cases.index') }}" method="GET">
                <input
                    type="search"
                    name="search"
                    value="{{ $search }}"
                    placeholder="Search case number, title, type or court..."
                >
                <button type="submit">Search</button>
            </form>

            <table>
                <thead>
                    <tr>
                        <th>Case Number</th>
                        <th>Case Title</th>
                        <th>Case Type</th>
                        <th>Court</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($cases as $case)
                        <tr>
                            <td>{{ $case->case_number }}</td>
                            <td>{{ $case->case_title }}</td>
                            <td>{{ $case->case_type }}</td>
                            <td>{{ $case->court_name ?: 'Not assigned' }}</td>
                            <td><span class="status">{{ $case->filing_status }}</span></td>
                            <td class="actions">
                                <a href="{{ route('cases.show', $case) }}">View</a>
                                <a href="{{ route('cases.edit', $case) }}">Edit</a>
                                <form action="{{ route('cases.destroy', $case) }}" method="POST"
                                      onsubmit="return confirm('Delete this case? This action cannot be undone.')"
                                      style="display:inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="delete-btn">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">
                                No cases found. Register your first case to get started.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="pagination">
                {{ $cases->links() }}
            </div>
        </section>
    </main>
</body>
</html>
