<?php

namespace App\Http\Controllers;

use App\Models\CaseFee;
use App\Models\Payment;
use App\Models\BailBond;
use App\Models\FinancialAdjustment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FinancialReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $paymentsQuery = Payment::query()
            ->where('status', 'completed');

        if ($startDate) {
            $paymentsQuery->whereDate('paid_at', '>=', $startDate);
        }

        if ($endDate) {
            $paymentsQuery->whereDate('paid_at', '<=', $endDate);
        }

        $payments = (clone $paymentsQuery)
            ->with('fee.courtCase')
            ->orderByDesc('paid_at')
            ->paginate(15)
            ->withQueryString();

        $totalPayments = (clone $paymentsQuery)->sum('amount');

        $feesQuery = CaseFee::query();

        $totalFees = (clone $feesQuery)->sum('amount');
        $totalOutstanding = $feesQuery->get()
            ->sum(fn ($fee) => max(
                0,
                (float) $fee->amount - (float) $fee->amount_paid
            ));

        $bondsQuery = BailBond::query();

        $totalOrderedBonds = (clone $bondsQuery)->sum('ordered_amount');
        $totalDepositedBonds = (clone $bondsQuery)->sum('deposited_amount');

        $adjustmentsQuery = FinancialAdjustment::query();

        if ($startDate) {
            $adjustmentsQuery->whereDate('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $adjustmentsQuery->whereDate('created_at', '<=', $endDate);
        }

        $adjustments = (clone $adjustmentsQuery)
            ->with('courtCase')
            ->orderByDesc('created_at')
            ->get();

        $completedRefunds = (clone $adjustmentsQuery)
            ->where('adjustment_type', 'refund')
            ->where('status', 'completed')
            ->sum('amount');

        $completedExpenses = (clone $adjustmentsQuery)
            ->where('adjustment_type', 'expense')
            ->where('status', 'completed')
            ->sum('amount');

        $completedForfeitures = (clone $adjustmentsQuery)
            ->where('adjustment_type', 'forfeiture')
            ->where('status', 'completed')
            ->sum('amount');

        $feeBreakdown = CaseFee::select(
                'fee_type',
                DB::raw('COUNT(*) as record_count'),
                DB::raw('SUM(amount) as total_amount'),
                DB::raw('SUM(amount_paid) as total_paid')
            )
            ->groupBy('fee_type')
            ->orderBy('fee_type')
            ->get();

        return view('financial-reports.index', compact(
            'startDate',
            'endDate',
            'payments',
            'totalPayments',
            'totalFees',
            'totalOutstanding',
            'totalOrderedBonds',
            'totalDepositedBonds',
            'adjustments',
            'completedRefunds',
            'completedExpenses',
            'completedForfeitures',
            'feeBreakdown'
        ));
    }
}
