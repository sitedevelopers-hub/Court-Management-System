<?php

namespace App\Http\Controllers;

use App\Models\Hearing;
use App\Models\CourtCase;
use App\Models\Courtroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class HearingController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $hearings = Hearing::with(['courtCase', 'courtroom'])
            ->when($search, function ($query, $search) {
                $query->whereHas('courtCase', function ($q) use ($search) {
                    $q->where('case_number', 'like', "%{$search}%")
                      ->orWhere('case_title', 'like', "%{$search}%");
                })->orWhere('hearing_type', 'like', "%{$search}%");
            })
            ->orderBy('scheduled_start')
            ->paginate(10)
            ->withQueryString();

        return view('hearings.index', compact('hearings', 'search'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $courtrooms = Courtroom::where('status', 'available')
            ->orderBy('room_number')
            ->get();

        return view('hearings.create', compact('cases', 'courtrooms'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'courtroom_id' => ['nullable', 'exists:courtrooms,id'],
            'scheduled_start' => ['required', 'date', 'after_or_equal:now'],
            'scheduled_end' => ['nullable', 'date', 'after:scheduled_start'],
            'hearing_type' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([
                'scheduled', 'completed', 'adjourned', 'cancelled',
            ])],
        ]);

        $this->ensureNoConflict($validated);

        Hearing::create($validated);

        return redirect()
            ->route('hearings.index')
            ->with('success', 'Hearing scheduled successfully.');
    }

    public function show(Hearing $hearing)
    {
        $hearing->load(['courtCase', 'courtroom']);

        return view('hearings.show', compact('hearing'));
    }

    public function edit(Hearing $hearing)
    {
        $cases = CourtCase::orderBy('case_number')->get();
        $courtrooms = Courtroom::orderBy('room_number')->get();

        return view('hearings.edit', compact('hearing', 'cases', 'courtrooms'));
    }

    public function update(Request $request, Hearing $hearing)
    {
        $validated = $request->validate([
            'case_id' => ['required', 'exists:cases,id'],
            'courtroom_id' => ['nullable', 'exists:courtrooms,id'],
            'scheduled_start' => ['required', 'date'],
            'scheduled_end' => ['nullable', 'date', 'after:scheduled_start'],
            'hearing_type' => ['required', 'string', 'max:255'],
            'status' => ['required', Rule::in([
                'scheduled', 'completed', 'adjourned', 'cancelled',
            ])],
            'outcome' => ['nullable', 'string'],
            'adjournment_reason' => ['nullable', 'string'],
        ]);

        $this->ensureNoConflict($validated, $hearing->id);

        if (
            $hearing->scheduled_start->toDateTimeString()
            !== date('Y-m-d H:i:s', strtotime($validated['scheduled_start']))
        ) {
            $validated['rescheduled_from'] = $hearing->scheduled_start;
        }

        $hearing->update($validated);

        return redirect()
            ->route('hearings.show', $hearing)
            ->with('success', 'Hearing updated successfully.');
    }

    public function destroy(Hearing $hearing)
    {
        try {
            $hearing->delete();
        } catch (\Illuminate\Database\QueryException $e) {
            // Hearings soft-delete, so an FK error should be rare, but never
            // claim success when nothing was deleted.
            return redirect()
                ->route('hearings.show', $hearing)
                ->with('error', 'The hearing could not be deleted because it is still linked to other records.');
        }

        return redirect()
            ->route('hearings.index')
            ->with('success', 'Hearing deleted successfully.');
    }

    private function ensureNoConflict(array $data, ?int $ignoreId = null): void
    {
        if (empty($data['courtroom_id'])) {
            return;
        }

        $start = $data['scheduled_start'];
        $end = $data['scheduled_end'] ?? date(
            'Y-m-d H:i:s',
            strtotime($start . ' +1 hour')
        );

        $conflict = Hearing::query()
            ->where('courtroom_id', $data['courtroom_id'])
            ->where('status', 'scheduled')
            ->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))
            ->where('scheduled_start', '<', $end)
            ->where(function ($q) use ($start) {
                $q->where('scheduled_end', '>', $start)
                  ->orWhere(function ($inner) use ($start) {
                      $inner->whereNull('scheduled_end')
                            ->where('scheduled_start', '>', date(
                                'Y-m-d H:i:s',
                                strtotime($start . ' -1 hour')
                            ));
                  });
            })
            ->exists();

        if ($conflict) {
            throw ValidationException::withMessages([
                'courtroom_id' => 'This courtroom may already have a hearing scheduled during that time. Please choose another room or time.',
            ]);
        }
    }
}
