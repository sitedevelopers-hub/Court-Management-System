<?php

namespace App\Http\Controllers;

use App\Models\CourtCase;
use App\Models\CourtEvent;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourtEventController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $events = CourtEvent::with('courtCase')
            ->when($search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('event_type', 'like', "%{$search}%")
                        ->orWhereHas('courtCase', function ($caseQuery) use ($search) {
                            $caseQuery->where('case_number', 'like', "%{$search}%")
                                ->orWhere('case_title', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy('starts_at')
            ->paginate(10)
            ->withQueryString();

        // Events that have passed their start date but are still pending —
        // shown as "expired" on the index page until updated or deleted.
        $expiredCount = CourtEvent::where('status', 'pending')
            ->where('starts_at', '<', now())
            ->count();

        return view('court-events.index', compact('events', 'search', 'expiredCount'));
    }

    public function create()
    {
        $cases = CourtCase::orderBy('case_number')->get();

        return view('court-events.create', compact('cases'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'case_id' => ['nullable', 'exists:cases,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['required', Rule::in([
                'hearing', 'deadline', 'meeting', 'reminder', 'party', 'other',
            ])],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'remind_at' => ['nullable', 'date', 'before_or_equal:starts_at'],
            'status' => ['required', Rule::in([
                'planned', 'pending', 'completed', 'cancelled',
            ])],
            'event_type_other' => ['nullable', 'string', 'max:255', 'required_if:event_type,other'],
        ]);

        // Free-text detail for "Other" is appended to the description.
        if (($validated['event_type'] ?? null) === 'other' && !empty($validated['event_type_other'])) {
            $validated['description'] = trim('[' . $validated['event_type_other'] . '] ' . ($validated['description'] ?? ''));
        }
        unset($validated['event_type_other']);

        $validated['created_by'] = auth()->id();

        CourtEvent::create($validated);

        return redirect()
            ->route('court-events.index')
            ->with('success', 'Court event created successfully.');
    }

    public function show(CourtEvent $court_event)
    {
        $court_event->load('courtCase', 'creator');

        return view('court-events.show', ['event' => $court_event]);
    }

    public function edit(CourtEvent $court_event)
    {
        $cases = CourtCase::orderBy('case_number')->get();

        return view('court-events.edit', [
            'event' => $court_event,
            'cases' => $cases,
        ]);
    }

    public function update(Request $request, CourtEvent $court_event)
    {
        $validated = $request->validate([
            'case_id' => ['nullable', 'exists:cases,id'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'event_type' => ['required', Rule::in([
                'hearing', 'deadline', 'meeting', 'reminder', 'party', 'other',
            ])],
            'starts_at' => ['required', 'date'],
            'ends_at' => ['nullable', 'date', 'after:starts_at'],
            'remind_at' => ['nullable', 'date', 'before_or_equal:starts_at'],
            'status' => ['required', Rule::in([
                'planned', 'pending', 'completed', 'cancelled',
            ])],
            'event_type_other' => ['nullable', 'string', 'max:255', 'required_if:event_type,other'],
        ]);

        if (($validated['event_type'] ?? null) === 'other' && !empty($validated['event_type_other'])) {
            $validated['description'] = trim('[' . $validated['event_type_other'] . '] ' . ($validated['description'] ?? ''));
        }
        unset($validated['event_type_other']);

        $court_event->update($validated);

        return redirect()
            ->route('court-events.show', $court_event)
            ->with('success', 'Court event updated successfully.');
    }

    public function destroy(CourtEvent $court_event)
    {
        $court_event->delete();

        return redirect()
            ->route('court-events.index')
            ->with('success', 'Court event deleted successfully.');
    }
}
