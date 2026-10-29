<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Adjustments | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        .container {
            max-width: 1200px;
            margin: auto;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
            color: #0f172a;
        }

        .subtitle {
            margin: 0;
            color: #64748b;
        }

        .btn {
            display: inline-block;
            padding: 11px 16px;
            border-radius: 7px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
            border: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #1f6f43;
            color: white;
        }

        .btn-secondary {
            background: #e2e8f0;
            color: #334155;
        }

        .panel {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(15, 23, 42, 0.05);
            margin-bottom: 22px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .search-form input {
            flex: 1;
            min-width: 200px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
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
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        tbody tr:hover {
            background: #eef5ef;
        }

        .reference {
            font-weight: bold;
            color: #1f6f43;
        }

        .amount {
            font-weight: bold;
            white-space: nowrap;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            background: #e2e8f0;
            color: #334155;
            white-space: nowrap;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-approved {
            background: #dbeafe;
            color: #1f6f43;
        }

        .status-completed {
            background: #dcfce7;
            color: #166534;
        }

        .status-rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .action-link {
            color: #1f6f43;
            text-decoration: none;
            font-weight: bold;
        }

        .backbar { margin-bottom: 14px; }

        .alert {
            padding: 14px 18px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .alert-success {
            background: #dcfce7;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            color: #991b1b;
        }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 35px;
        }

        .pagination {
            margin-top: 20px;
        }

        @media (max-width: 600px) {
            body { padding: 15px; }
            h1 { font-size: 23px; }
            .panel { padding: 15px; }
        }
    
        .delete-btn { background: #dc2626; color: #fff; border: none; padding: 8px 14px; border-radius: 7px; cursor: pointer; font-size: 13px; font-weight: 600; }
        .delete-btn:hover { background: #b91c1c; }
    </style>
</head>

<body>
<div class="container">

    <div class="backbar"><a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a></div>
    <div class="header">
        <div>
            <h1>Financial Adjustments</h1>
            <p class="subtitle">
                Manage refunds, forfeitures, expenses and other financial adjustments.
            </p>
        </div>

        <a href="{{ route('financial-adjustments.create') }}" class="btn btn-primary">
            + Record Adjustment
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    <div class="panel">
        <form action="{{ route('financial-adjustments.index') }}" method="GET" class="search-form">
            <input
                type="text"
                name="search"
                placeholder="Search reference, case, type or reason..."
                value="{{ request('search') }}"
            >

            <button type="submit" class="btn btn-primary">Search</button>

            @if(request('search'))
                <a href="{{ route('financial-adjustments.index') }}" class="btn btn-secondary">
                    Clear
                </a>
            @endif
        </form>
    </div>

    <div class="panel">
        <div class="table-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Case</th>
                    <th>Adjustment Type</th>
                    <th>Amount</th>
                    <th>Status</th>
                    <th>Date Recorded</th>
                    <th>Action</th>
                </tr>
                </thead>

                <tbody>
                @forelse($adjustments as $adjustment)
                    <tr>
                        <td class="reference">
                            {{ $adjustment->reference_number }}
                        </td>

                        <td>
                            @if($adjustment->courtCase)
                                <strong>{{ $adjustment->courtCase->case_number }}</strong>
                                <br>
                                {{ $adjustment->courtCase->case_title }}
                            @else
                                Not available
                            @endif
                        </td>

                        <td>
                            {{ ucwords(str_replace('_', ' ', $adjustment->adjustment_type)) }}
                        </td>

                        <td class="amount">
                            KSh {{ number_format((float) $adjustment->amount, 2) }}
                        </td>

                        <td>
                            <span class="status status-{{ $adjustment->status }}">
                                {{ ucwords(str_replace('_', ' ', $adjustment->status)) }}
                            </span>
                        </td>

                        <td>{{ $adjustment->created_at?->format('d M Y') }}</td>

                        <td>
                            <a href="{{ route('financial-adjustments.show', $adjustment) }}" class="action-link">View Details</a>
                            <form action="{{ route('financial-adjustments.destroy', $adjustment) }}" method="POST"
                                  onsubmit="return confirm('Delete this adjustment record? This action cannot be undone.')"
                                  style="display:inline; margin-left:10px">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="delete-btn">Delete</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty">
                            No financial adjustments found.
                            Click “Record Adjustment” to add the first record.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $adjustments->links() }}
        </div>
    </div>

</div>
</body>
</html>
