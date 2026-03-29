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
        ], [
            'nev.required' => 'A felhasználónév megadása kötelező.',
            'nev.min' => 'A felhasználónév legalább 3 karakter hosszú legyen.',
            'email.required' => 'Az email cím megadása kötelező.',
            'email.email' => 'Érvényes email címet adj meg.',
            'email.unique' => 'Ez az email cím már foglalt.',
            'jelszo.required' => 'A jelszó megadása kötelező.',
            'jelszo.min' => 'A jelszónak legalább 8 karakter hosszúnak kell lennie.',
            'jelszo.confirmed' => 'A két jelszó nem egyezik.',
        ]);

        $user = Felhasznalo::create([
            'nev' => $validated['nev'],
            'email' => $validated['email'],
            'jelszo' => Hash::make($validated['jelszo']),
        ]);

        $user->sendEmailVerificationNotification();
    }
}