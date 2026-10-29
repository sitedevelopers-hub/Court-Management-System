<?php

namespace App\Http\Controllers;

use App\Models\BailBond;
use App\Models\CourtCase;
use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;

class BailBondController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $bailBonds = BailBond::with(['courtCase', 'participant'])
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('reference_number', 'like', "%{$search}%")
                        ->orWhere('bond_type', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
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

        return view('bail-bonds.index', compact('bailBonds', 'search'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();

        $participants = Participant::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('bail-bonds.create', compact('cases', 'participants'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'bond_type' => [
                'required',
                Rule::in(['cash_bail', 'bond', 'personal_bond', 'other']),
            ],
            'ordered_amount' => [
                'required', 'numeric', 'min:0', 'max:9999999999.99',
            ],
            'deposited_amount' => [
                'required', 'numeric', 'min:0', 'max:9999999999.99',
            ],
            'order_date' => ['nullable', 'date', 'before_or_equal:today'],
            'status' => [
                'required',
                Rule::in([
                    'ordered', 'partially_deposited', 'deposited',
                    'released', 'forfeited',
                ]),
            ],
            'conditions' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if (
            (float) $validated['deposited_amount']
            > (float) $validated['ordered_amount']
        ) {
            return back()->withInput()->withErrors([
                'deposited_amount' => 'The deposited amount cannot exceed the ordered amount.',
            ]);
        }

        $validated['reference_number'] = $this->generateReference();
        $validated['recorded_by'] = auth()->id();
        $validated['status'] = $this->calculateStatus(
            (float) $validated['ordered_amount'],
            (float) $validated['deposited_amount'],
            $validated['status']
        );

        $bailBond = BailBond::create($validated);

        return redirect()
            ->route('bail-bonds.show', $bailBond)
            ->with('success', 'Bail or bond record created successfully.');
    }

    public function show(BailBond $bail_bond)
    {
        $bail_bond->load(['courtCase', 'participant', 'adjustments']);

        return view('bail-bonds.show', ['bailBond' => $bail_bond]);
    }

    public function edit(BailBond $bail_bond)
    {
        $cases = CourtCase::orderBy('case_number')->get();

        $participants = Participant::where('status', 'active')
            ->orderBy('first_name')
            ->orderBy('last_name')
            ->get();

        return view('bail-bonds.edit', [
            'bailBond' => $bail_bond,
            'cases' => $cases,
            'participants' => $participants,
        ]);
    }

    public function update(Request $request, BailBond $bail_bond)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'participant_id' => ['required', 'exists:participants,id'],
            'bond_type' => [
                'required',
                Rule::in(['cash_bail', 'bond', 'personal_bond', 'other']),
            ],
            'ordered_amount' => [
                'required', 'numeric', 'min:0', 'max:9999999999.99',
            ],
            'deposited_amount' => [
                'required', 'numeric', 'min:0', 'max:9999999999.99',
            ],
            'order_date' => ['nullable', 'date', 'before_or_equal:today'],
            'status' => [
                'required',
                Rule::in([
                    'ordered', 'partially_deposited', 'deposited',
                    'released', 'forfeited',
                ]),
            ],
            'conditions' => ['nullable', 'string', 'max:5000'],
            'notes' => ['nullable', 'string', 'max:5000'],
        ]);

        if (
            (float) $validated['deposited_amount']
            > (float) $validated['ordered_amount']
        ) {
            return back()->withInput()->withErrors([
                'deposited_amount' => 'The deposited amount cannot exceed the ordered amount.',
            ]);
        }

        $validated['status'] = $this->calculateStatus(
            (float) $validated['ordered_amount'],
            (float) $validated['deposited_amount'],
            $validated['status']
        );

        $bail_bond->update($validated);

        return redirect()
            ->route('bail-bonds.show', $bail_bond)
            ->with('success', 'Bail or bond record updated successfully.');
    }

    public function destroy(BailBond $bail_bond)
    {
        if ($bail_bond->adjustments()->exists()) {
            return redirect()
                ->route('bail-bonds.index')
                ->with('error', 'This record has financial adjustments and cannot be deleted.');
        }

        $bail_bond->delete();

        return redirect()
            ->route('bail-bonds.index')
            ->with('success', 'Bail or bond record deleted successfully.');
    }

    private function generateReference(): string
    {
        do {
            $reference = 'BB-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (BailBond::where('reference_number', $reference)->exists());

        return $reference;
    }

    private function calculateStatus(
        float $ordered,
        float $deposited,
        string $requestedStatus
    ): string {
        // Preserve legal outcomes rather than replacing them with deposit status.
        if (in_array($requestedStatus, ['released', 'forfeited'], true)) {
            return $requestedStatus;
        }

        if ($deposited <= 0) {
            return 'ordered';
        }

        if ($deposited >= $ordered) {
            return 'deposited';
        }

        return 'partially_deposited';
    }
}
