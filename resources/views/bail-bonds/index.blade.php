<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bail and Bond Management | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 1250px; margin: auto; }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }
        h1 { margin: 0 0 8px; }
        .subtitle { margin: 0; color: #64748b; }
        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 8px;
            background: #1f6f43;
            color: white;
            text-decoration: none;
            font-size: 14px;
            font-weight: 600;
        }
        .secondary { background: #e2e8f0; color: #1e293b; }
        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
            margin-bottom: 18px;
        }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 16px #0f172a0d;
            margin-bottom: 20px;
        }
        .search { display: flex; gap: 10px; flex-wrap: wrap; }
        input {
            flex: 1;
            min-width: 220px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 8px;
            font: inherit;
        }
        button {
            padding: 12px 18px;
            border: none;
            border-radius: 8px;
            background: #0f172a;
            color: white;
            cursor: pointer;
            font-weight: 600;
        }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            text-align: left;
            padding: 14px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 14px;
            white-space: nowrap;
        }
        th { background: #eef5ef; color: #475569; }
        .reference { color: #1f6f43; font-weight: 700; }
        .amount { font-weight: 700; }
        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #dbeafe;
            color: #1e40af;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .status-deposited { background: #dcfce7; color: #166534; }
        .status-released { background: transparent; color: #334155; }
        .status-forfeited { background: #fee2e2; color: #991b1b; }
        .action-link {
            color: #1f6f43;
            text-decoration: none;
            font-weight: 600;
        }
        .empty { text-align: center; padding: 35px; color: #64748b; }
        .pagination { margin-top: 20px; }
        @media (max-width: 600px) {
            body { padding: 18px 10px; }
            .card { padding: 14px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 8px 14px; border-radius: 7px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
        .backbar { margin-bottom: 14px; }
    </style>
</head>
<body>
<div class="container">
    <div class="backbar"><a href="{{ route('dashboard') }}" class="btn secondary">← Back to Dashboard</a></div>
    <div class="header">
        <div>
            <h1>Bail and Bond Management</h1>
            <p class="subtitle">Track court-ordered amounts, deposits and legal status.</p>
        </div>
        <a href="{{ route('bail-bonds.create') }}" class="btn">+ Register Bail / Bond</a>
    </div>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="card">
        <form action="{{ route('bail-bonds.index') }}" method="GET" class="search">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search reference, case, participant or status..."
            >
            <button type="submit">Search</button>
            <a href="{{ route('bail-bonds.index') }}" class="btn secondary">Clear</a>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Case Number</th>
                    <th>Participant</th>
                    <th>Type</th>
                    <th>Ordered (KES)</th>
                    <th>Deposited (KES)</th>
                    <th>Outstanding (KES)</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($bailBonds as $bond)
                    @php
                        $outstanding = max(
                            0,
                            (float) $bond->ordered_amount - (float) $bond->deposited_amount
                        );
                    @endphp
                    <tr>
                        <td class="reference">{{ $bond->reference_number }}</td>
                        <td>{{ $bond->courtCase?->case_number ?? 'N/A' }}</td>
                        <td>{{ $bond->participant?->full_name ?? 'N/A' }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $bond->bond_type)) }}</td>
                        <td class="amount">{{ number_format((float) $bond->ordered_amount, 2) }}</td>
                        <td>{{ number_format((float) $bond->deposited_amount, 2) }}</td>
                        <td>{{ number_format($outstanding, 2) }}</td>
                        <td>
                            <span class="status
                                {{ $bond->status === 'deposited' ? 'status-deposited' : '' }}
                                {{ $bond->status === 'released' ? 'status-released' : '' }}
                                {{ $bond->status === 'forfeited' ? 'status-forfeited' : '' }}">
                                {{ ucwords(str_replace('_', ' ', $bond->status)) }}
                            </span>
                        </td>
                        <td>
                            <a href="{{ route('bail-bonds.show', $bond) }}" class="action-link">View Details</a>
                            <form action="{{ route('bail-bonds.destroy', $bond) }}" method="POST"
                                  onsubmit="return confirm('Delete this bail/bond record? This action cannot be undone.')"
                                  style="display:inline; margin-left:10px">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty">
                            No bail or bond records found. Register a record to get started.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $bailBonds->links() }}
        </div>
    </div>
</div>
</body>
</html>
