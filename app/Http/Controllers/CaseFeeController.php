<?php

namespace App\Http\Controllers;

use App\Models\CaseFee;
use App\Models\CourtCase;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class CaseFeeController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $fees = CaseFee::with(['courtCase', 'participant'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('fee_reference', 'like', "%{$search}%")
                        ->orWhere('fee_type', 'like', "%{$search}%")
                        ->orWhereHas('courtCase', function ($caseQuery) use ($search) {
                            $caseQuery->where('case_number', 'like', "%{$search}%")
                                ->orWhere('case_title', 'like', "%{$search}%");
                        })
                        ->orWhereHas('participant', function ($participantQuery) use ($search) {
                            $participantQuery->where('first_name', 'like', "%{$search}%")
                                ->orWhere('last_name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('case-fees.index', compact('fees', 'search'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $participants = Participant::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('case-fees.create', compact('cases', 'participants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'participant_id' => [
                'nullable',
                'exists:participants,id',
            ],
            'fee_type' => ['required', Rule::in([
                'filing_fee',
                'service_fee',
                'fine',
                'court_fee',
                'bond_bail',
                'other',
            ])],
            'description' => ['required', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'due_date' => ['nullable', 'date'],
        ]);

        $validated['fee_reference'] = $this->generateReference();
        $validated['created_by'] = auth()->id();
        $validated['amount_paid'] = 0;
        $validated['status'] = 'unpaid';

        $fee = CaseFee::create($validated);

        return redirect()
            ->route('case-fees.show', $fee)
            ->with('success', 'Court fee registered successfully.');
    }

    public function show(CaseFee $case_fee)
    {
        $case_fee->load(['courtCase', 'participant', 'payments']);

        return view('case-fees.show', [
            'fee' => $case_fee,
        ]);
    }

    public function edit(CaseFee $case_fee)
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $participants = Participant::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('case-fees.edit', [
            'fee' => $case_fee,
            'cases' => $cases,
            'participants' => $participants,
        ]);
    }

    public function update(Request $request, CaseFee $case_fee)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'participant_id' => [
                'nullable',
                'exists:participants,id',
            ],
            'fee_type' => ['required', Rule::in([
                'filing_fee',
                'service_fee',
                'fine',
                'court_fee',
                'bond_bail',
                'other',
            ])],
            'description' => ['required', 'string', 'max:2000'],
            'amount' => ['required', 'numeric', 'gt:0', 'max:9999999999.99'],
            'due_date' => ['nullable', 'date'],
        ]);

        if ((float) $validated['amount'] < (float) $case_fee->amount_paid) {
            return back()->withInput()->withErrors([
                'amount' => 'The fee amount cannot be less than the amount already paid.',
            ]);
        }

        $validated['status'] = $this->calculateStatus(
            (float) $validated['amount'],
            (float) $case_fee->amount_paid
        );

        $case_fee->update($validated);

        return redirect()
            ->route('case-fees.show', $case_fee)
            ->with('success', 'Court fee updated successfully.');
    }

    public function destroy(CaseFee $case_fee)
    {
        if ($case_fee->payments()->exists()) {
            return redirect()
                ->route('case-fees.index')
                ->with('error', 'This fee has payment records and cannot be deleted. Keep the financial history intact.');
        }

        $case_fee->delete();

        return redirect()
            ->route('case-fees.index')
            ->with('success', 'Court fee deleted successfully.');
    }

    private function generateReference(): string
    {
        do {
            $reference = 'FEE-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (CaseFee::where('fee_reference', $reference)->exists());

        return $reference;
    }

    private function calculateStatus(float $amount, float $paid): string
    {
        if ($paid <= 0) {
            return 'unpaid';
        }

        if ($paid >= $amount) {
            return 'paid';
        }

        return 'partially_paid';
    }
}
