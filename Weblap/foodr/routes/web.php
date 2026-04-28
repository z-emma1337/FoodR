<?php

use App\Http\Controllers\KommentController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Felhasznalo;
use App\Models\Kommentek;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InterakcioController;
use App\Http\Controllers\ReceptController;
use App\Mail\WelcomeUser;
use Illuminate\Support\Facades\Mail;
use Illuminate\Foundation\Auth\EmailVerificationRequest;


Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('login', function () {
    return redirect()->route('bejelentkezes');
})->name('login');

Route::get('bejelentkezes', function () {
    return Inertia::render('Bejelentkezes');
})->middleware('guest')->name('bejelentkezes');

Route::get('regisztracio', function () {
    return Inertia::render('Regisztracio');
})->name('regisztracio');

Route::get('/felfedezes', function () {
    return Inertia::render('Felfedezes');
})->name('felfedezes');


Route::match(['get', 'post'], '/felhasznalo', function (Request $request) {

    $user = Auth::user();

    if ($request->isMethod('post')) {
        $request->validate([
            'profilkepurl' => 'nullable|string',
            'profilkepfajl' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        if ($request->hasFile('profilkepfajl')) {
            $request->file('profilkepfajl')->move(
                public_path('imgs/FeltoltottProfilkepek'),
                $user->id . '.' . $request->file('profilkepfajl')->getClientOriginalExtension()
            );
            $user->profilkepurl = 'imgs/FeltoltottProfilkepek/' . $user->id . '.' . $request->file('profilkepfajl')->getClientOriginalExtension();
        }

        if ($request->filled('profilkepurl')) {
            $user->profilkepurl = $request->profilkepurl;
        }
        Kommentek::where('felhasznalo_id', $user->id)
            ->update(['pfpurl' => $user->profilkepurl]);

        $user->save();
        return Redirect::back();
    }

    return [
        'id' => $user->id,
        'profilkepurl' => $user->profilkepurl
    ];
});


Route::match(['get', 'post'], '/recipes', ReceptController::class . '@index')->name('recipes');
Route::get('/check-username', function (Request $request) {
    $username = strtolower($request->query('username'));
    $available = !Felhasznalo::whereRaw('LOWER(nev) = ?', [$username])->exists();
    return response()->json(['available' => $available]);
});

Route::post('/regisztracio', [RegisterController::class, 'store'])
    ->name('regisztracio.store');

Route::post('/bejelentkezes', [LoginController::class, 'login'])
    ->name('login');

Route::post('/bejelentkezes/2fa', [LoginController::class, 'twoFactorVerify'])
    ->name('login.two-factor')
    ->middleware('throttle:5,1');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');
Route::post('/receptLetrehozas', [ReceptController::class, 'ReceptHozzaadasa'])->middleware('auth');
Route::post('/receptSzerkesztese/{id}', [ReceptController::class, 'ReceptSzerkesztese'])->middleware('auth');
Route::post('/komment/delete/{id}', [KommentController::class, 'deleteKomment'])->middleware('auth');
Route::post('/interakcio/like', [InterakcioController::class, 'likeRecept'])->middleware('auth');
Route::post('/interakcio/dislike', [InterakcioController::class, 'dislikeRecept'])->middleware('auth');
Route::post('/interakcio/unlike', [InterakcioController::class, 'unlikeRecept'])->middleware('auth');



Route::get('/interakciok', function () {
    $felhasznalo = Auth::user();

    $likedReceptek = $felhasznalo->interakciok
        ->where('liked', 1)
        ->pluck('recept_id');

    return \App\Models\Recept::whereIn('id', $likedReceptek)->get();
});
Route::middleware('auth')->group(function () {

    Route::get('/kedvencek', function () {
        return Inertia::render('Kedvencek');
    })->name('kedvencek');


    Route::get('/receptjeim', function () {
        return Inertia::render('Receptjeim');
    })->name('receptjeim');

});
Route::get('/allergenek/felhasznalo', [App\Http\Controllers\AllergenController::class, 'show'])->middleware('auth');
Route::post('/allergenek/felhasznalo', [App\Http\Controllers\AllergenController::class, 'felhasznaloallergenhozzaad'])->middleware('auth');
Route::get('/recept-alapanyagok', [App\Http\Controllers\ReceptAlapanyagController::class, 'index']);
Route::get('/allergenek', [App\Http\Controllers\AllergenController::class, 'index']);
Route::get('/osszes-alapanyag', [ReceptController::class, 'getOsszesAlapanyag']);

Route::get('/email/verify', function () {
    return Inertia::render('VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/felfedezes');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::delete('/fiok-torles', function () {
    $felhasznalo = Auth::user();
    $id = $felhasznalo->id;

    \App\Models\Recept::where('felhasznalo_id', $id)->delete();

    DB::table('sessions')->where('user_id', $id)->delete();

    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    $felhasznalo->delete();

    return redirect()->route('home');
})->middleware('auth')->name('fiok.torles');

Route::delete('/recept-torles/{id}', function ($id) {
    $felhasznalo = Auth::user();
    \App\Models\Recept::where('id', $id)
        ->where('felhasznalo_id', $felhasznalo->id)
        ->delete();

    return response()->json(['success' => true], 200);
});

Route::post('/komment', [KommentController::class, 'komment']);
Route::get('/kommentek/{recept_id}', [KommentController::class, 'getKommentek']);
require __DIR__ . '/settings.php';