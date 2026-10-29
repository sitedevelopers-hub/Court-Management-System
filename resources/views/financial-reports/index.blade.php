<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Reports | Court Management System</title>

    <style>
        * { box-sizing: border-box; }
        body {
            background: transparent;
color: #1e293b;
        }

        .header {
            background: linear-gradient(120deg, #0b3d2e 0%, #14532d 55%, #1d7a46 100%);
            color: white;
            padding: 24px 5%;
        }

        .header h1 { margin: 0 0 8px; font-size: 26px; }
        .header p { margin: 0; color: #cbd5e1; }

        .container {
            width: 90%;
            max-width: 1400px;
            margin: 28px auto;
        }

        .top-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            border: 0;
            border-radius: 7px;
            background: linear-gradient(90deg, #15803d, #1d9d50);
            color: white;
            text-decoration: none;
            cursor: pointer;
            font-size: 14px;
        }

        .button.secondary { background: #475569; }

        .filters, .panel {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
            margin-bottom: 24px;
        }

        .filters form {
            display: flex;
            flex-wrap: wrap;
            gap: 14px;
            align-items: end;
        }

        .field { flex: 1; min-width: 180px; }

        label {
            display: block;
            font-size: 13px;
            font-weight: bold;
            margin-bottom: 7px;
        }

        input[type="date"] {
            width: 100%;
            padding: 11px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 18px;
            margin-bottom: 24px;
        }

        .stat {
            background: linear-gradient(150deg, #f7fcf9 0%, #ffffff 45%, #eef8f2 100%);
            border-radius: 12px;
            padding: 22px;
            border-left: 5px solid #1d7a46;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .06);
        }

        .stat h3 {
            margin: 0 0 12px;
            font-size: 13px;
            color: #64748b;
        }

        .stat .amount {
            font-size: 25px;
            font-weight: bold;
            overflow-wrap: anywhere;
        }

        .stat p { color: #64748b; font-size: 12px; margin-bottom: 0; }

        .section-title { margin: 0 0 18px; font-size: 19px; }

        .table-wrap { overflow-x: auto; }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            text-align: left;
            padding: 13px 12px;
            border-bottom: 1px solid #e2e8f0;
            font-size: 13px;
        }

        th {
            background: #eef5ef;
            color: #475569;
        }

        .status {
            display: inline-block;
            padding: 5px 9px;
            border-radius: 20px;
            background: #e2e8f0;
            font-size: 12px;
        }

        .pagination { margin-top: 18px; }

        .empty {
            text-align: center;
            color: #64748b;
            padding: 25px;
        }

        .report-note {
            font-size: 13px;
            color: #64748b;
            line-height: 1.6;
        }

        @media print {
        body {
            background: transparent;
}
            .no-print, .pagination { display: none !important; }
            .container { width: 100%; margin: 0; }
            .panel, .stat { box-shadow: none; border: 1px solid #ddd; }
        }
    </style>
</head>
<body>
@include('partials.nav')

<header class="header">
    <h1>Financial Reports</h1>
    <p>Court Management System · Financial overview and transaction records</p>
</header>

<main class="container">

    <div class="top-actions no-print">
        <a href="{{ route('dashboard') }}" class="button secondary">
            ← Back to Dashboard
        </a>

        <a href="{{ route('reports.index') }}" class="button">📋 Generate Report</a>

        <button class="button" onclick="window.print()">
            Print Report
        </button>
    </div>

    <section class="filters no-print">
        <h2 class="section-title">Filter Financial Activity</h2>

        <form method="GET" action="{{ route('financial-reports.index') }}">
            <div class="field">
                <label for="start_date">Start date (payments and adjustments)</label>
                <input
                    type="date"
                    id="start_date"
                    name="start_date"
                    value="{{ $startDate }}"
                    max="{{ $endDate ?: now()->toDateString() }}"
                >
            </div>

            <div class="field">
                <label for="end_date">End date (payments and adjustments)</label>
                <input
                    type="date"
                    id="end_date"
                    name="end_date"
                    value="{{ $endDate }}"
                    min="{{ $startDate }}"
                    max="{{ now()->toDateString() }}"
                >
            </div>

            <button type="submit" class="button">Apply Filters</button>

            <a href="{{ route('financial-reports.index') }}"
               class="button secondary">Clear Filters</a>
        </form>
    </section>

    <section class="cards">
        <div class="stat">
            <h3>Total Fees Recorded</h3>
            <div class="amount">KES {{ number_format($totalFees, 2) }}</div>
            <p>All recorded court fees</p>
        </div>

        <div class="stat">
            <h3>Outstanding Fees</h3>
            <div class="amount">KES {{ number_format($totalOutstanding, 2) }}</div>
            <p>Unpaid or partially paid fee balances</p>
        </div>

        <div class="stat">
            <h3>Payments Received</h3>
            <div class="amount">KES {{ number_format($totalPayments, 2) }}</div>
            <p>Completed payments matching the selected dates</p>
        </div>

        <div class="stat">
            <h3>Bail and Bond Ordered</h3>
            <div class="amount">KES {{ number_format($totalOrderedBonds, 2) }}</div>
            <p>All recorded bail and bond orders</p>
        </div>

        <div class="stat">
            <h3>Bail and Bond Deposited</h3>
            <div class="amount">KES {{ number_format($totalDepositedBonds, 2) }}</div>
            <p>All recorded deposited amounts</p>
        </div>

        <div class="stat">
            <h3>Completed Refunds</h3>
            <div class="amount">KES {{ number_format($completedRefunds, 2) }}</div>
            <p>Refund adjustments matching the selected dates</p>
        </div>

        <div class="stat">
            <h3>Completed Expenses</h3>
            <div class="amount">KES {{ number_format($completedExpenses, 2) }}</div>
            <p>Expense adjustments matching the selected dates</p>
        </div>

        <div class="stat">
            <h3>Completed Forfeitures</h3>
            <div class="amount">KES {{ number_format($completedForfeitures, 2) }}</div>
            <p>Forfeiture adjustments matching the selected dates</p>
        </div>
    </section>

    <section class="panel">
        <h2 class="section-title">Fee Breakdown by Category</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Fee Category</th>
                        <th>Number of Records</th>
                        <th>Total Fees (KES)</th>
                        <th>Amount Paid (KES)</th>
                        <th>Outstanding (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($feeBreakdown as $fee)
                        <tr>
                            <td>{{ ucwords(str_replace('_', ' ', $fee->fee_type)) }}</td>
                            <td>{{ $fee->record_count }}</td>
                            <td>{{ number_format($fee->total_amount, 2) }}</td>
                            <td>{{ number_format($fee->total_paid, 2) }}</td>
                            <td>
                                {{ number_format(max(0, (float) $fee->total_amount - (float) $fee->total_paid), 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="empty">No fee records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <section class="panel">
        <h2 class="section-title">Payment Transactions</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Receipt Number</th>
                        <th>Case Number</th>
                        <th>Fee Reference</th>
                        <th>Payment Method</th>
                        <th>Transaction Date</th>
                        <th>Amount (KES)</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($payments as $payment)
                        <tr>
                            <td>{{ $payment->receipt_number }}</td>
                            <td>{{ $payment->fee?->courtCase?->case_number ?? 'N/A' }}</td>
                            <td>{{ $payment->fee?->fee_reference ?? 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $payment->payment_method)) }}</td>
                            <td>{{ $payment->paid_at?->format('d M Y H:i') ?? 'N/A' }}</td>
                            <td>{{ number_format((float) $payment->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="empty">No completed payments found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="pagination no-print">
            {{ $payments->links() }}
        </div>
    </section>

    <section class="panel">
        <h2 class="section-title">Financial Adjustments</h2>

        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Reference</th>
                        <th>Case Number</th>
                        <th>Adjustment Type</th>
                        <th>Reason</th>
                        <th>Date Recorded</th>
                        <th>Amount (KES)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($adjustments as $adjustment)
                        <tr>
                            <td>{{ $adjustment->reference_number }}</td>
                            <td>{{ $adjustment->courtCase?->case_number ?? 'N/A' }}</td>
                            <td>{{ ucwords(str_replace('_', ' ', $adjustment->adjustment_type)) }}</td>
                            <td>{{ $adjustment->reason }}</td>
                            <td>{{ $adjustment->created_at?->format('d M Y') ?? 'N/A' }}</td>
                            <td>{{ number_format((float) $adjustment->amount, 2) }}</td>
                            <td>
                                <span class="status">
                                    {{ ucwords(str_replace('_', ' ', $adjustment->status)) }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="empty">No financial adjustments found for this period.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>

    <p class="report-note">
        Financial summary generated by the Court Management System.
        Amounts are displayed in Kenyan shillings (KES). Fee and bond summary
        figures include all records, while payment and adjustment lists follow
        the selected date filters. Verify financial figures against official
        records before using this report for accounting or court decisions.
    </p>

</main>

</body>
</html>

