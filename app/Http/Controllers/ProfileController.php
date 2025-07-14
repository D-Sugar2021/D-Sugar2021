<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Show the form for editing the user's profile (including password change).
     */
    public function edit()
    {
        return view('profile.edit');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'current_password' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', Password::defaults(), 'confirmed'],
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('status', 'password-updated');
    }

    // In a full profile implementation, you would also have a method
    // to update profile information (name, email, etc.)
    // public function update(Request $request) { ... }
}
