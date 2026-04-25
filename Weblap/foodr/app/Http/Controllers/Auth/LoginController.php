<?php

namespace App\Http\Controllers\Auth;
use App\Http\Controllers\Controller;
use App\Models\Felhasznalo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Fortify\Contracts\TwoFactorAuthenticationProvider;

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
            $user = Auth::user();

            if ($user->hasEnabledTwoFactorAuthentication()) {
                $userId = $user->id;
                Auth::logout();
                $request->session()->put('login.id', $userId);
                $request->session()->put('login.remember', $remember);

                return back()->withErrors(['two_factor' => 'required']);
            }

            $request->session()->regenerate();
            return redirect('/');
        }

        return back()->withErrors([
            'authInput' => 'Hibás email/felhasználónév vagy jelszó.',
        ]);
    }

    public function twoFactorVerify(Request $request)
    {
        $request->validate(['code' => ['required', 'string']]);

        $userId = $request->session()->get('login.id');
        $remember = $request->session()->get('login.remember', false);

        if (!$userId) {
            return back()->withErrors(['code' => 'Lejárt munkamenet. Kérjük, jelentkezz be újra.']);
        }

        $user = Felhasznalo::find($userId);

        if (!$user) {
            return back()->withErrors(['code' => 'Felhasználó nem található.']);
        }

        $code = str_replace(' ', '', $request->input('code'));

        /** @var TwoFactorAuthenticationProvider $provider */
        $provider = app(TwoFactorAuthenticationProvider::class);

        $valid = $provider->verify(decrypt($user->two_factor_secret), $code);

        if (!$valid) {
            $recoveryCodes = json_decode(decrypt($user->two_factor_recovery_codes), true) ?? [];
            $valid = in_array($code, $recoveryCodes);

            if ($valid) {
                $user->forceFill([
                    'two_factor_recovery_codes' => encrypt(json_encode(
                        array_values(array_filter($recoveryCodes, fn($c) => $c !== $code))
                    )),
                ])->save();
            }
        }

        if (!$valid) {
            return back()->withErrors(['code' => 'Hibás kód. Próbáld újra.']);
        }

        $request->session()->forget(['login.id', 'login.remember']);
        Auth::login($user, $remember);
        $request->session()->regenerate();

        return redirect('/');
    }
}
