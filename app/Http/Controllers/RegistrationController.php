<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class RegistrationController extends Controller
{
    // Maximum number of administrator accounts allowed in the system.
    public const MAX_ADMINS = 2;

    public function showRegistrationForm()
    {
        $adminCount = $this->adminCount();

        return view('auth.register', [
            'adminSlotsAvailable' => $adminCount < self::MAX_ADMINS,
            'adminCount'          => $adminCount,
        ]);
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'name'        => ['required', 'string', 'max:255'],
            'email'       => ['required', 'email', 'max:255', 'unique:users,email'],
            'password'    => ['required', 'string', 'min:8', 'confirmed'],
            'as_admin'    => ['nullable', 'boolean'],
        ]);

        $wantsAdmin = $request->boolean('as_admin');

        if ($wantsAdmin && $this->adminCount() >= self::MAX_ADMINS) {
            return back()
                ->withInput($request->only('name', 'email'))
                ->withErrors(['as_admin' => 'The system already has ' . self::MAX_ADMINS . ' administrators. New accounts are created as standard users.']);
        }

        $user = DB::transaction(function () use ($validated, $wantsAdmin) {
            $user = User::create([
                'name'     => $validated['name'],
                'email'    => $validated['email'],
                'password' => Hash::make($validated['password']),
            ]);

            // Mark unverified — the user must verify their email via the
            // link we send before the dashboard becomes accessible.
            $user->forceFill(['email_verified_at' => null])->save();

            if ($wantsAdmin) {
                $adminRole = Role::firstOrCreate(
                    ['name' => 'admin'],
                    [
                        'display_name' => 'Administrator',
                        'description'  => 'Full access to every function of the system.',
                    ]
                );
                $user->roles()->sync([$adminRole->id]);
            } else {
                $basicRole = Role::firstOrCreate(
                    ['name' => 'registered_user'],
                    [
                        'display_name' => 'Registered User',
                        'description'  => 'Standard account created through public registration.',
                    ]
                );
                $user->roles()->sync([$basicRole->id]);
            }

            return $user;
        });

        event(new Registered($user));

        // Log the user in but land them on the verification notice page,
        // NOT the dashboard — the 'verified' middleware enforces that.
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()
            ->route('verification.notice')
            ->with('success', 'Welcome, ' . $user->name . '! We sent a verification link to ' . $user->email . '. Open it to activate your account.');
    }

    private function adminCount(): int
    {
        return User::whereHas('roles', fn ($q) => $q->where('name', 'admin'))->count();
    }
}
