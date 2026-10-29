<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payments | Court Management System</title>
    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
            font-family: Arial, sans-serif;
        }
        .container { max-width: 1200px; margin: auto; }
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
            font-weight: 600;
            font-size: 14px;
        }
        .btn-secondary { background: #e2e8f0; color: #1e293b; }
        .alert {
            padding: 14px 16px;
            margin-bottom: 18px;
            border-radius: 8px;
            background: #dcfce7;
            color: #166534;
        }
        .alert-error { background: #fee2e2; color: #991b1b; }
        .alert-info { background: #dbeafe; color: #1e40af; }
        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 16px #0f172a0d;
            margin-bottom: 20px;
        }
        .search {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        input {
            flex: 1;
            min-width: 200px;
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
            white-space: nowrap;
            font-size: 14px;
        }
        th { background: #eef5ef; color: #475569; }
        .reference { font-weight: 700; color: #1f6f43; }
        .amount { font-weight: 700; }
        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #dcfce7;
            color: #166534;
            font-size: 12px;
            font-weight: 700;
            text-transform: capitalize;
        }
        .action-link {
            color: #1f6f43;
            font-weight: 600;
            text-decoration: none;
        }
        .empty { text-align: center; color: #64748b; padding: 35px; }
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
    <div class="backbar"><a href="{{ route('dashboard') }}" class="btn btn-secondary">← Back to Dashboard</a></div>
    <div class="header">
        <div>
            <h1>Payment Management</h1>
            <p class="subtitle">Review payment transactions and receipt records.</p>
        </div>
        <a href="{{ route('payments.create') }}" class="btn">+ Record Payment</a>
    </div>

    @if (session('success'))
        <div class="alert">{{ session('success') }}</div>
    @endif

    @if (session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif

    @if (session('info'))
        <div class="alert alert-info">{{ session('info') }}</div>
    @endif

    <div class="card">
        <form action="{{ route('payments.index') }}" method="GET" class="search">
            <input
                type="search"
                name="search"
                value="{{ $search }}"
                placeholder="Search receipt, transaction or case number..."
            >
            <button type="submit">Search</button>
            <a href="{{ route('payments.index') }}" class="btn btn-secondary">Clear</a>
        </form>
    </div>

    <div class="card">
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>Receipt Number</th>
                    <th>Case Number</th>
                    <th>Fee Reference</th>
                    <th>Amount (KES)</th>
                    <th>Payment Method</th>
                    <th>Transaction Reference</th>
                    <th>Payment Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @forelse ($payments as $payment)
                    <tr>
                        <td class="reference">{{ $payment->receipt_number }}</td>
                        <td>{{ $payment->fee?->courtCase?->case_number ?? 'N/A' }}</td>
                        <td>{{ $payment->fee?->fee_reference ?? 'N/A' }}</td>
                        <td class="amount">{{ number_format((float) $payment->amount, 2) }}</td>
                        <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                        <td>{{ $payment->transaction_reference ?: '—' }}</td>
                        <td>{{ $payment->paid_at?->format('d M Y, H:i') ?? '—' }}</td>
                        <td><span class="status">{{ $payment->status }}</span></td>
                        <td>
                            <a href="{{ route('payments.show', $payment) }}" class="action-link">View Receipt</a>
                            <form action="{{ route('payments.destroy', $payment) }}" method="POST"
                                  onsubmit="return confirm('Delete this payment record? This action cannot be undone.')"
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
                            No payment records found. Record a payment to get started.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination">
            {{ $payments->links() }}
        </div>
    </div>
</div>
</body>
</html>
