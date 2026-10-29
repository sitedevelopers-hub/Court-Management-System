<?php

namespace App\Http\Controllers;

use App\Models\CourtCase;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CaseController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $cases = CourtCase::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                        ->orWhere('case_title', 'like', "%{$search}%")
                        ->orWhere('case_type', 'like', "%{$search}%")
                        ->orWhere('court_name', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('cases.index', compact('cases', 'search'));
    }

    public function create()
    {
        return view('cases.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_number' => ['required', 'string', 'max:255', 'unique:cases,case_number'],
            'case_title' => ['required', 'string', 'max:255'],
            'case_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'court_name' => ['nullable', 'string', 'max:255'],
            'filing_status' => ['required', Rule::in([
                'pending', 'active', 'closed', 'adjourned'
            ])],
            'filing_date' => ['nullable', 'date'],
            'closed_date' => ['nullable', 'date', 'after_or_equal:filing_date'],
            'case_type_other' => ['nullable', 'string', 'max:255', 'required_if:case_type,Other'],
        ]);

        // When "Other" is chosen, persist the user's own wording into the case type.
        if (($validated['case_type'] ?? null) === 'Other' && !empty($validated['case_type_other'])) {
            $validated['case_type'] = 'Other: ' . $validated['case_type_other'];
        }
        unset($validated['case_type_other']);

        CourtCase::create($validated);

        return redirect()
            ->route('cases.index')
            ->with('success', 'Case created successfully.');
    }

    public function show(CourtCase $case)
    {
        $case->load(['participants', 'hearings', 'documents', 'fees', 'bailBonds']);

        return view('cases.show', compact('case'));
    }

    public function edit(CourtCase $case)
    {
        return view('cases.edit', compact('case'));
    }

    public function update(Request $request, CourtCase $case)
    {
        $validated = $request->validate([
            'case_number' => [
                'required', 'string', 'max:255',
                Rule::unique('cases', 'case_number')->ignore($case->id),
            ],
            'case_title' => ['required', 'string', 'max:255'],
            'case_type' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'court_name' => ['nullable', 'string', 'max:255'],
            'filing_status' => ['required', Rule::in([
                'pending', 'active', 'closed', 'adjourned'
            ])],
            'filing_date' => ['nullable', 'date'],
            'closed_date' => ['nullable', 'date', 'after_or_equal:filing_date'],
            'case_type_other' => ['nullable', 'string', 'max:255', 'required_if:case_type,Other'],
        ]);

        if (($validated['case_type'] ?? null) === 'Other' && !empty($validated['case_type_other'])) {
            $validated['case_type'] = 'Other: ' . $validated['case_type_other'];
        }
        unset($validated['case_type_other']);

        $case->update($validated);

        return redirect()
            ->route('cases.show', $case)
            ->with('success', 'Case updated successfully.');
    }

    public function destroy(CourtCase $case)
    {
        // A case must not be deleted while dependent records still reference it.
        // The hearings, case_fees and bail_bonds foreign keys restrict on delete,
        // so deleting would either throw or silently leave orphaned data behind.
        $blocked = [];

        if ($case->hearings()->exists()) {
            $blocked[] = 'hearing records';
        }

        if ($case->fees()->exists()) {
            $blocked[] = 'court fees';
        }

        if ($case->bailBonds()->exists()) {
            $blocked[] = 'bail or bond records';
        }

        if ($case->documents()->exists()) {
            $blocked[] = 'documents';
        }

        if (!empty($blocked)) {
            return redirect()
                ->route('cases.show', $case)
                ->with(
                    'error',
                    'This case cannot be deleted because it still has ' .
                    implode(', ', $blocked) .
                    '. Remove or archive those records first.'
                );
        }

        try {
            $case->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Defensive: a record created between the checks and the delete.
            return redirect()
                ->route('cases.show', $case)
                ->with('error', 'The case could not be deleted because it is still linked to other records.');
        }

        return redirect()
            ->route('cases.index')
            ->with('success', 'Case deleted successfully.');
    }
}
