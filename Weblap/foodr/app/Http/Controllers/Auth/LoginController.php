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
            'authInput' => ['required'],
            'jelszo' => ['required']
        ]);

        $remember = $request->boolean('remember');

        $fieldType = filter_var($credentials['authInput'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nev';
        $fieldType = strtolower($fieldType);

        if ($fieldType == 'nev') {
            $user = Felhasznalo::whereRaw('LOWER(nev) = ?', [strtolower($credentials['authInput'])])->first();
            if (!$user) {
                return back()->withErrors(['authInput' => 'Hibás email/felhasználónév vagy jelszó.']);
            }
            $credentials['authInput'] = $user->nev;
        }

        if (Auth::attempt([
            $fieldType => $credentials['authInput'],
            'password' => $credentials['jelszo']
        ], $remember)) {
            $request->session()->regenerate();
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'authInput' => 'Hibás email/felhasználónév vagy jelszó.',
        ]);
    }
}