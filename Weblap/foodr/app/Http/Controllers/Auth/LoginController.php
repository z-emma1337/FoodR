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
        $fieldType = filter_var($credentials['authInput'], FILTER_VALIDATE_EMAIL) ? 'email' : 'nev';
        $fieldType = strtolower($fieldType);
        
        if ($fieldType == 'nev') {
        $credentials['authInput'] = Felhasznalo::when($fieldType=='nev', function($query) use ($credentials) { 
            $query->whereRaw('LOWER(nev) = ?', [strtolower($credentials['authInput'])]);
        })->first();
        }
        $credentials['authInput'] = $credentials['authInput']->nev;

        if (
            Auth::attempt([
                $fieldType => ($credentials['authInput']),
                'password' => $credentials['jelszo']
            ])
        ) {
            $request->session()->regenerate();
            return redirect()->intended('');
        }

        return back()->withErrors([
            'authInput' => 'Hibás email/felhasználónév vagy jelszó.',
        ]);
    }

}