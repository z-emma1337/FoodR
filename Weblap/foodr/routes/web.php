<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Felhasznalo;
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

        $user->save();
        return Redirect::back();
    }

    return [
        'id' => $user->id,
        'profilkepurl' => $user->profilkepurl
    ];
});


Route::match(['get', 'post'], '/recipes', function () {


    if (request()->isMethod('post')) {
        $recipeId = request()->input('recipe_id');

        if ($recipeId && Auth::check()) {
            \App\Models\Recept::findOrFail($recipeId)
                ->interakciok()
                ->where('felhasznalo_id', Auth::id())
                ->update(['liked' => 0]);
        }

        return response()->json(['success' => true]);
    }

    return \App\Models\Recept::with([
        'receptAlapanyagok.alapanyag.allergenek',
        'interakciok',
    ])
        ->get()
        ->map(function ($recept) {
            $osszesAllergen = $recept->receptAlapanyagok
                ->pluck('alapanyag.allergenek')
                ->flatten()
                ->unique('id');

            $allergenek = $osszesAllergen
                ->whereNotIn('id', [6, 7])
                ->pluck('nev')
                ->values();

            $nemVegetarianus = $osszesAllergen->where('id', 6)->isNotEmpty();
            $nemVegan = $osszesAllergen->where('id', 7)->isNotEmpty();

            $dietTags = [];
            if (!$nemVegetarianus)
                $dietTags[] = 'Vegetáriánus';
            if (!$nemVegan && !$nemVegetarianus)
                $dietTags[] = 'Vegán';

            $hozzavalok = $recept->receptAlapanyagok->map(fn($ra) => [
                'nev' => $ra->alapanyag->nev,
                'adag' => $ra->adag ?? 'ízlés szerint'
            ]);

            $felhasznaloId = Auth::id() ?? 0;

            $liked = $recept->interakciok
                ->where('felhasznalo_id', $felhasznaloId)
                ->first()
                ->liked ?? 0;

            return [
                'id' => $recept->id,
                'nev' => $recept->nev,
                'leiras' => $recept->leiras,
                'ido' => $recept->ido,
                'adag' => $recept->adag,
                'kep_url' => $recept->kep_url,
                'allergenek' => array_merge($allergenek->toArray(), $dietTags),
                'hozzavalok' => $hozzavalok,
                'liked' => $liked,
                'felhasznalo_id' => $recept->felhasznalo_id
            ];
        });
});

Route::get('/check-username', function (Request $request) {
    $username = strtolower($request->query('username'));
    $available = !Felhasznalo::whereRaw('LOWER(nev) = ?', [$username])->exists();
    return response()->json(['available' => $available]);
});

Route::post('/regisztracio', [RegisterController::class, 'store'])
    ->name('regisztracio.store');

Route::post('/bejelentkezes', [LoginController::class, 'login'])
    ->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('home');
})->name('logout');
Route::post('/receptLetrehozas', [ReceptController::class, 'ReceptHozzaadasa'])->middleware('auth');

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

    Route::get('/allergenek/felhasznalo', [App\Http\Controllers\AllergenController::class, 'show']);
Route::post('/allergenek/felhasznalo', [App\Http\Controllers\AllergenController::class, 'felhasznaloallergenhozzaad']);

});

Route::get('/recept-alapanyagok', [App\Http\Controllers\ReceptAlapanyagController::class, 'index']);
Route::get('/allergenek', [App\Http\Controllers\AllergenController::class, 'index']);

Route::get('/email/verify', function () {
    return Inertia::render('VerifyEmail');
})->middleware('auth')->name('verification.notice');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect('/felfedezes');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::delete('/fiok-torles', function () {
    $felhasznalo = Auth::user();
    \App\Models\Recept::where('felhasznalo_id', $felhasznalo->id)->delete();
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
require __DIR__ . '/settings.php';