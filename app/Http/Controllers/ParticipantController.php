<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ParticipantController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $participants = Participant::query()
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('participant_number', 'like', "%{$search}%")
                        ->orWhere('first_name', 'like', "%{$search}%")
                        ->orWhere('middle_name', 'like', "%{$search}%")
                        ->orWhere('last_name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('organization', 'like', "%{$search}%");
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('participants.index', compact('participants', 'search'));
    }

    public function create()
    {
        return view('participants.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'participant_number' => [
                'required', 'string', 'max:255',
                'unique:participants,participant_number',
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'participant_type' => ['required', Rule::in([
                'judge', 'magistrate', 'advocate', 'clerk',
                'party', 'witness', 'other',
            ])],
            'email' => ['nullable', 'email', 'max:255', 'unique:participants,email'],
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => ['nullable', 'string', 'max:255', 'unique:participants,national_id'],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'participant_type_other' => ['nullable', 'string', 'max:255', 'required_if:participant_type,other'],
        ]);

        // Free-text detail for "Other" is appended to the address so the
        // user's own wording is always captured.
        if (($validated['participant_type'] ?? null) === 'other' && !empty($validated['participant_type_other'])) {
            $validated['address'] = trim('Type: ' . $validated['participant_type_other'] . ($validated['address'] ? "
" . $validated['address'] : ''));
        }
        unset($validated['participant_type_other']);

        Participant::create($validated);

        return redirect()
            ->route('participants.index')
            ->with('success', 'Participant registered successfully.');
    }

    public function show(Participant $participant)
    {
        $participant->load(['cases', 'bailBonds', 'fees']);

        return view('participants.show', compact('participant'));
    }

    public function edit(Participant $participant)
    {
        return view('participants.edit', compact('participant'));
    }

    public function update(Request $request, Participant $participant)
    {
        $validated = $request->validate([
            'participant_number' => [
                'required', 'string', 'max:255',
                Rule::unique('participants', 'participant_number')->ignore($participant->id),
            ],
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'participant_type' => ['required', Rule::in([
                'judge', 'magistrate', 'advocate', 'clerk',
                'party', 'witness', 'other',
            ])],
            'email' => [
                'nullable', 'email', 'max:255',
                Rule::unique('participants', 'email')->ignore($participant->id),
            ],
            'phone' => ['nullable', 'string', 'max:30'],
            'national_id' => [
                'nullable', 'string', 'max:255',
                Rule::unique('participants', 'national_id')->ignore($participant->id),
            ],
            'organization' => ['nullable', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'status' => ['required', Rule::in(['active', 'inactive'])],
            'participant_type_other' => ['nullable', 'string', 'max:255', 'required_if:participant_type,other'],
        ]);

        if (($validated['participant_type'] ?? null) === 'other' && !empty($validated['participant_type_other'])) {
            $validated['address'] = trim('Type: ' . $validated['participant_type_other'] . ($validated['address'] ? "
" . $validated['address'] : ''));
        }
        unset($validated['participant_type_other']);

        $participant->update($validated);

        return redirect()
            ->route('participants.show', $participant)
            ->with('success', 'Participant updated successfully.');
    }

    public function destroy(Participant $participant)
    {
        // bail_bonds.participant_id and case_fees.participant_id restrict on
        // delete, so a linked participant cannot be removed — tell the user
        // instead of showing a success message that did not happen.
        $blocked = [];

        if ($participant->bailBonds()->exists()) {
            $blocked[] = 'bail or bond records';
        }

        if ($participant->fees()->exists()) {
            $blocked[] = 'court fee records';
        }

        if ($participant->caseAssignments()->exists()) {
            $blocked[] = 'case assignments';
        }

        if (!empty($blocked)) {
            return redirect()
                ->route('participants.show', $participant)
                ->with(
                    'error',
                    'This participant cannot be deleted because they are linked to ' .
                    implode(', ', $blocked) .
                    '. Remove those links first or mark the participant inactive.'
                );
        }

        try {
            $participant->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            return redirect()
                ->route('participants.show', $participant)
                ->with('error', 'The participant could not be deleted because they are still linked to other records.');
        }

        return redirect()
            ->route('participants.index')
            ->with('success', 'Participant deleted successfully.');
    }
}
