<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    // Show the "Forgot password" form
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    // Change the password directly using the email (patients only)
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();

        // Only patient accounts can be reset here. Admin accounts are protected.
        if (! $user || $user->role !== 'patient') {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'We could not reset the password for this email.']);
        }

        $user->forceFill([
            'password' => Hash::make($request->password),
            'remember_token' => Str::random(60),   // logs the account out of other devices
        ])->save();

        return redirect()->route('login')->with('status', 'Your password has been changed. You can now log in.');
    }
}