<?php

namespace App\Http\Controllers;

use App\Models\Courtroom;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CourtroomController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->string('search')->toString();

        $courtrooms = Courtroom::query()
            ->when($search, function ($query, $search) {
                $query->where('room_number', 'like', "%{$search}%")
                    ->orWhere('court_name', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            })
            ->orderBy('room_number')
            ->paginate(10)
            ->withQueryString();

        return view('courtrooms.index', compact('courtrooms', 'search'));
    }

    public function create()
    {
        return view('courtrooms.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'room_number' => ['required', 'string', 'max:255', 'unique:courtrooms,room_number'],
            'court_name' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['available', 'unavailable', 'maintenance'])],
            'notes' => ['nullable', 'string'],
        ]);

        Courtroom::create($validated);

        return redirect()
            ->route('courtrooms.index')
            ->with('success', 'Courtroom registered successfully.');
    }

    public function show(Courtroom $courtroom)
    {
        $courtroom->load(['hearings' => function ($query) {
            $query->with('courtCase')
                ->orderByDesc('scheduled_start')
                ->limit(10);
        }]);

        return view('courtrooms.show', compact('courtroom'));
    }

    public function edit(Courtroom $courtroom)
    {
        return view('courtrooms.edit', compact('courtroom'));
    }

    public function update(Request $request, Courtroom $courtroom)
    {
        $validated = $request->validate([
            'room_number' => [
                'required', 'string', 'max:255',
                Rule::unique('courtrooms', 'room_number')->ignore($courtroom->id),
            ],
            'court_name' => ['required', 'string', 'max:255'],
            'capacity' => ['nullable', 'integer', 'min:1'],
            'location' => ['nullable', 'string', 'max:255'],
            'status' => ['required', Rule::in(['available', 'unavailable', 'maintenance'])],
            'notes' => ['nullable', 'string'],
        ]);

        $courtroom->update($validated);

        return redirect()
            ->route('courtrooms.show', $courtroom)
            ->with('success', 'Courtroom updated successfully.');
    }

    public function destroy(Courtroom $courtroom)
    {
        if ($courtroom->hearings()->exists()) {
            return redirect()
                ->route('courtrooms.index')
                ->with('error', 'This courtroom has hearing records. Mark it unavailable instead of archiving it.');
        }

        $courtroom->delete();

        return redirect()
            ->route('courtrooms.index')
            ->with('success', 'Courtroom deleted successfully.');
    }
}
