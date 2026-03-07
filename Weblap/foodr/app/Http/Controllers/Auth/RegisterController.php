<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Felhasznalo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class RegisterController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'nev' => ['required', 'min:3'],
            'email' => ['required', 'email', 'unique:felhasznalo,email'],
            'jelszo' => ['required', Password::min(8), 'confirmed'],
            'allergen_id' => ['nullable', 'integer'],
        ]);

        $user = Felhasznalo::create([
            'nev' => $validated['nev'],
            'email' => $validated['email'],
            'jelszo' => Hash::make($validated['jelszo']),
            'allergen_id' => $validated['allergen_id']
        ]);

        $user->sendEmailVerificationNotification();

        return redirect()->route('bejelentkezes')
            ->with('message', 'Check your email to verify your account.');
    }
}
