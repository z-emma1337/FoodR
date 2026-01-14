<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Felhasznalo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Auth;
class LoginController extends Controller
{


    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'jelszo' => ['required']
        ]);

        if (
            Auth::attempt([
                'email' => $credentials['email'],
                'password' => $credentials['jelszo']
            ])
        ) {
            $request->session()->regenerate();
            return redirect()->intended('');
        }

        return back()->withErrors([
            'email' => 'Hibás email vagy jelszó.',
        ]);
    }

}