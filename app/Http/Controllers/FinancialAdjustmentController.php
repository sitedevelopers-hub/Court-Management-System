<?php

namespace App\Http\Controllers;

use App\Models\FinancialAdjustment;
use App\Models\CourtCase;
use App\Models\Payment;
use App\Models\BailBond;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class FinancialAdjustmentController extends Controller
{
    public function index(Request $request)
    {
        $query = FinancialAdjustment::with([
            'courtCase',
            'payment',
            'bailBond',
            'recorder',
        ]);

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('reference_number', 'like', "%{$search}%")
                    ->orWhere('adjustment_type', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhere('reason', 'like', "%{$search}%")
                    ->orWhereHas('courtCase', function ($caseQuery) use ($search) {
                        $caseQuery->where('case_number', 'like', "%{$search}%")
                            ->orWhere('case_title', 'like', "%{$search}%");
                    });
            });
        }

        $adjustments = $query->latest()->paginate(15)->withQueryString();

        return view('financial-adjustments.index', compact('adjustments'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $payments = Payment::with('fee.courtCase')->latest()->get();
        $bailBonds = BailBond::with('courtCase', 'participant')->latest()->get();

        return view('financial-adjustments.create', compact(
            'cases',
            'payments',
            'bailBonds'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'payment_id' => ['nullable', 'exists:payments,id', 'required_without:bail_bond_id'],
            'bail_bond_id' => ['nullable', 'exists:bail_bonds,id', 'required_without:payment_id'],
            'adjustment_type' => [
                'required',
                Rule::in([
                    'refund',
                    'forfeiture',
                    'expense',
                    'correction',
                    'other',
                ]),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:2000'],
            'processed_at' => ['nullable', 'date', 'before_or_equal:now'],
            'status' => [
                'required',
                Rule::in(['pending', 'approved', 'completed', 'rejected']),
            ],
        ]);

        $this->validateLinkedRecords($validated);

        $adjustment = FinancialAdjustment::create([
            ...$validated,
            'reference_number' => $this->generateReferenceNumber(),
            'recorded_by' => auth()->id(),
        ]);

        return redirect()
            ->route('financial-adjustments.show', $adjustment)
            ->with('success', 'Financial adjustment recorded successfully.');
    }

    public function show(FinancialAdjustment $financialAdjustment)
    {
        $financialAdjustment->load([
            'courtCase',
            'payment.fee',
            'payment.receiver',
            'bailBond.participant',
            'recorder',
        ]);

        return view('financial-adjustments.show', [
            'adjustment' => $financialAdjustment,
        ]);
    }

    public function edit(FinancialAdjustment $financialAdjustment)
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $payments = Payment::with('fee.courtCase')->latest()->get();
        $bailBonds = BailBond::with('courtCase', 'participant')->latest()->get();

        return view('financial-adjustments.edit', [
            'adjustment' => $financialAdjustment,
            'cases' => $cases,
            'payments' => $payments,
            'bailBonds' => $bailBonds,
        ]);
    }

    public function update(Request $request, FinancialAdjustment $financialAdjustment)
    {
        if (in_array($financialAdjustment->status, ['completed', 'rejected'], true)) {
            return redirect()
                ->route('financial-adjustments.show', $financialAdjustment)
                ->with('error', 'Completed or rejected adjustments cannot be edited.');
        }

        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'payment_id' => ['nullable', 'exists:payments,id', 'required_without:bail_bond_id'],
            'bail_bond_id' => ['nullable', 'exists:bail_bonds,id', 'required_without:payment_id'],
            'adjustment_type' => [
                'required',
                Rule::in([
                    'refund',
                    'forfeiture',
                    'expense',
                    'correction',
                    'other',
                ]),
            ],
            'amount' => ['required', 'numeric', 'gt:0'],
            'reason' => ['required', 'string', 'max:2000'],
            'processed_at' => ['nullable', 'date', 'before_or_equal:now'],
            'status' => [
                'required',
                Rule::in(['pending', 'approved', 'completed', 'rejected']),
            ],
        ]);

        $this->validateLinkedRecords($validated);

        $financialAdjustment->update($validated);

        return redirect()
            ->route('financial-adjustments.show', $financialAdjustment)
            ->with('success', 'Financial adjustment updated successfully.');
    }

    public function destroy(FinancialAdjustment $financialAdjustment)
    {
        if ($financialAdjustment->status === 'completed') {
            return redirect()
                ->route('financial-adjustments.index')
                ->with('error', 'Completed financial adjustments cannot be deleted.');
        }

        $financialAdjustment->delete();

        return redirect()
            ->route('financial-adjustments.index')
            ->with('success', 'Financial adjustment deleted successfully.');
    }

    private function validateLinkedRecords(array $data): void
    {
        if (!empty($data['payment_id']) && !empty($data['bail_bond_id'])) {
            abort(422, 'Link an adjustment to a payment or a bail/bond record, not both.');
        }

        if (!empty($data['payment_id'])) {
            $payment = Payment::with('fee')->findOrFail($data['payment_id']);

            if (!$payment->fee || (int) $payment->fee->case_id !== (int) $data['case_id']) {
                abort(422, 'The selected payment must belong to the selected case.');
            }
        }

        if (!empty($data['bail_bond_id'])) {
            $bond = BailBond::findOrFail($data['bail_bond_id']);

            if ((int) $bond->case_id !== (int) $data['case_id']) {
                abort(422, 'The selected bail/bond record must belong to the selected case.');
            }
        }
    }

    private function generateReferenceNumber(): string
    {
        do {
            $reference = 'ADJ-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (FinancialAdjustment::where('reference_number', $reference)->exists());

        return $reference;
    }
}
