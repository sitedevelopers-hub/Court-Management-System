<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Management | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        header {
            background: #172554;
            color: white;
            padding: 25px 5%;
        }

        header h1 { margin: 0 0 8px; }
        header p { margin: 0; color: #cbd5e1; }

        main {
            width: 90%;
            max-width: 1300px;
            margin: 30px auto;
        }

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .btn {
            display: inline-block;
            border: 0;
            border-radius: 7px;
            padding: 11px 15px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .btn.gray { background: #475569; }
        .btn.red { background: #b91c1c; }

        .search-form {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }

        input[type="search"] {
            padding: 11px;
            min-width: 230px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
        }

        .notice {
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 15px;
        }

        .success { background: #dcfce7; color: #166534; }
        .error { background: #fee2e2; color: #991b1b; }

        .panel {
            background: white;
            border-radius: 12px;
            padding: 22px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
        }

        .panel h2 { margin-top: 0; font-size: 19px; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 700px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
        }

        th {
            background: #eef5ef;
            color: #475569;
        }

        .role {
            display: inline-block;
            background: #dbeafe;
            color: #1e40af;
            padding: 5px 8px;
            margin: 2px;
            border-radius: 15px;
            font-size: 12px;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
        }

        .actions .btn { padding: 7px 10px; }

        .empty {
            padding: 25px;
            text-align: center;
            color: #64748b;
        }

        .pagination { margin-top: 20px; }

        @media (max-width: 600px) {
            main { width: 94%; }
            .panel { padding: 14px; }
            input[type="search"] { min-width: 0; width: 100%; }
        }
    </style>
</head>
<body>

@include('partials.nav')

<main>
    <div class="toolbar">
        <a href="{{ route('dashboard') }}" class="btn gray">← Dashboard</a>

        <a href="{{ route('user-management.create') }}" class="btn">
            + Add New User
        </a>
    </div>

    @if (session('success'))
        <div class="notice success">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="notice error">{{ session('error') }}</div>
    @endif

    @if ($errors->any())
        <div class="notice error">
            @foreach ($errors->all() as $error)
                <div>{{ $error }}</div>
            @endforeach
        </div>
    @endif

    <section class="panel">
        <h2>Registered Users</h2>

        <form action="{{ route('user-management.index') }}"
              method="GET" class="search-form">
            <input type="search"
                   name="search"
                   value="{{ request('search') }}"
                   placeholder="Search by name or email...">

            <button type="submit" class="btn">Search</button>

            @if (request('search'))
                <a href="{{ route('user-management.index') }}"
                   class="btn gray">Clear</a>
            @endif
        </form>

        <br>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Assigned Roles</th>
                        <th>Registered</th>
                        <th>Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($users as $user)
                        <tr>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @forelse ($user->roles as $role)
                                    <span class="role">
                                        {{ $role->display_name }}
                                    </span>
                                @empty
                                    <span>No role assigned</span>
                                @endforelse
                            </td>
                            <td>{{ $user->created_at?->format('d M Y') }}</td>
                            <td>
                                <div class="actions">
                                    <a href="{{ route('user-management.show', $user) }}"
                                       class="btn">View</a>

                                    <a href="{{ route('user-management.edit', $user) }}"
                                       class="btn gray">Edit</a>

                                    @if (auth()->id() !== $user->id)
                                        <form action="{{ route('user-management.destroy', $user) }}"
                                              method="POST"
                                              onsubmit="return confirm('Delete this user account? This action cannot be undone.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn red">
                                                Delete
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $users->links() }}
        </div>
    </section>
</main>

</body>
</html>
