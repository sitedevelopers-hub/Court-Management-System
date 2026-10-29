<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserManagementController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('roles');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $users = $query->latest()->paginate(15)->withQueryString();

        return response()
            ->view('user-management.index', compact('users'))
            // Stop the browser from showing a cached (stale) user list after
            // a delete — this is why deleted accounts sometimes "reappear".
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    public function create()
    {
        $roles = Role::orderBy('display_name')->get();

        return view('user-management.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()
            ->route('user-management.show', $user)
            ->with('success', 'User account created successfully.');
    }

    public function show(User $user)
    {
        $user->load('roles');

        return view('user-management.show', compact('user'));
    }

    public function edit(User $user)
    {
        $roles = Role::orderBy('display_name')->get();
        $user->load('roles');

        return view('user-management.edit', compact('user', 'roles'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email')->ignore($user->id),
            ],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'roles' => ['nullable', 'array'],
            'roles.*' => ['integer', 'exists:roles,id'],
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        $user->roles()->sync($validated['roles'] ?? []);

        return redirect()
            ->route('user-management.show', $user)
            ->with('success', 'User account updated successfully.');
    }

    public function destroy(User $user)
    {
        \Illuminate\Support\Facades\Log::debug('DESTROY HIT: id='.(string)$user->id.' email='.(string)$user->email.' exists='.(string)($user->exists ? 'yes':'no'));

        if (auth()->id() === $user->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        try {
            // Permanently delete the account AND every record it created.
            // Nothing is reassigned or kept — deletion is total.
            \Illuminate\Support\Facades\DB::transaction(function () use ($user) {
                $user->roles()->detach();

                // Shares, logs and notifications that reference this user.
                \App\Models\TemplateShare::where('shared_with', $user->id)->delete();
                \App\Models\DocumentAccessLog::where('user_id', $user->id)->delete();
                \Illuminate\Support\Facades\DB::table('report_shares')->where('shared_with', $user->id)->delete();
                \Illuminate\Support\Facades\DB::table('notifications')->where('notifiable_id', $user->id)->delete();

                // Every record the user created is deleted with them.
                \App\Models\WorkTemplate::where('created_by', $user->id)->delete();
                \App\Models\CaseDocument::where('uploaded_by', $user->id)->delete();
                \App\Models\CourtEvent::where('created_by', $user->id)->delete();
                \App\Models\GeneratedReport::where('created_by', $user->id)->delete();
                \App\Models\BailBond::where('recorded_by', $user->id)->delete();
                \App\Models\CaseFee::where('created_by', $user->id)->delete();
                \App\Models\FinancialAdjustment::where('recorded_by', $user->id)->delete();
                \App\Models\Payment::where('received_by', $user->id)->delete();

                // Invalidate any sessions still open for this account.
                \Illuminate\Support\Facades\DB::table('sessions')->where('user_id', $user->id)->delete();

                $user->delete();
            });
        } catch (\Illuminate\Database\QueryException $e) {
            \Illuminate\Support\Facades\Log::warning('User deletion failed: ' . $e->getMessage());

            return back()->with(
                'error',
                'The account could not be deleted because it is still linked to existing records. ' .
                'Remove or reassign its records first. (' . class_basename($e) . ')'
            );
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('User deletion failed: ' . $e->getMessage());

            return back()->with('error', 'The account could not be deleted: ' . $e->getMessage());
        }

        return redirect()
            ->route('user-management.index')
            ->with('success', 'User account deleted successfully.');
    }
}
