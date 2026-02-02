<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Felhasznalo;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Models\Interakciok;
use App\Http\Controllers\InterakcioController;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('bejelentkezes', function () { //BEJELENTKEZES
    return Inertia::render('Bejelentkezes');
})->middleware('guest')->name('bejelentkezes');

Route::get('regisztracio', function () { //REGISZTRACIO 
    return Inertia::render('Regisztracio');
})->name('regisztracio');

Route::get('/felfedezes', function () {
    return Inertia::render('Felfedezes');
})->name('felfedezes');

Route::get('/recipes', function () {
    return \App\Models\Recept::with([
        'receptAlapanyagok.alapanyag.allergenek'
    ])
    ->get()
    ->map(function($recept) {
        // Összes allergén összegyűjtése a receptből
        $osszesAllergen = $recept->receptAlapanyagok
            ->pluck('alapanyag.allergenek')
            ->flatten()
            ->unique('id');
        
        // Allergének neve (kivéve vegetáriánus/vegán)
        $allergenek = $osszesAllergen
            ->whereNotIn('id', [6, 7]) // Kihagyjuk a "Nem vegetáriánus" és "Nem vegán" ID-kat
            ->pluck('nev')
            ->values();
        
        // Vegán/Vegetáriánus pozitív logika
        $nemVegetarianusSzuro = $osszesAllergen->where('id', 6)->isNotEmpty();
        $nemVeganSzuro = $osszesAllergen->where('id', 7)->isNotEmpty();
        
        $dietTags = [];
        
        // Ha NINCS "Nem vegetáriánus" allergén, akkor vegetáriánus
        if (!$nemVegetarianusSzuro) {
            $dietTags[] = 'Vegetáriánus';
        }
        
        // Ha NINCS "Nem vegán" allergén ÉS vegetáriánus, akkor vegán
        if (!$nemVeganSzuro && !$nemVegetarianusSzuro) {
            $dietTags[] = 'Vegán';
        }
        
        // Összefűzzük az allergéneket és a diet tageket
        $osszesTag = array_merge($allergenek->toArray(), $dietTags);
        
        return [
            'id' => $recept->id,
            'nev' => $recept->nev,
            'leiras' => $recept->leiras,
            'ido' => $recept->ido,
            'adag' => $recept->adag,
            'kep_url' => $recept->kep_url,
            'allergenek' => $osszesTag
        ];
    });
});
Route::get('/check-username', function (Request $request) { //USERNAME ELLENŐRZÉS
    $username = $request->query('username');

    $available = !Felhasznalo::where('nev', $username)->exists();

    return response()->json([
        'available' => $available
    ]);
});

Route::post('/regisztracio', [RegisterController::class, 'store']) //REGISZTRÁCIÓ ADATOK KÜLDÉSE
    ->name('regisztracio.store');

Route::post('/bejelentkezes', [LoginController::class, 'login'])->name('login');

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect()->route('bejelentkezes');
})->name('logout');

Route::post('/interakcio/like', [InterakcioController::class, 'likeRecept'])->middleware('auth');
Route::post('/interakcio/dislike', [InterakcioController::class, 'dislikeRecept'])->middleware('auth');



Route::get('/interakciok', function () {
    $felhasznalo = Auth::user();

    $likedReceptek = $felhasznalo->interakciok
        ->where('liked', 1)
        ->pluck('recept_id');

    return \App\Models\Recept::whereIn('id', $likedReceptek)->get();
});