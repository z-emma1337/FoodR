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
use App\Http\Controllers\KedvencController;

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

Route::get('/recipes', function () {
    return \App\Models\Recept::with([
        'receptAlapanyagok.alapanyag.allergenek'
    ])
    ->get()
    ->map(function($recept) {
        $osszesAllergen = $recept->receptAlapanyagok
            ->pluck('alapanyag.allergenek')
            ->flatten()
            ->unique('id');
        
        $allergenek = $osszesAllergen
            ->whereNotIn('id', [6, 7])
            ->pluck('nev')
            ->values();
        
        $nemVegetarianusSzuro = $osszesAllergen->where('id', 6)->isNotEmpty();
        $nemVeganSzuro = $osszesAllergen->where('id', 7)->isNotEmpty();
        
        $dietTags = [];
        
        if (!$nemVegetarianusSzuro) {
            $dietTags[] = 'Vegetáriánus';
        }
        
        if (!$nemVeganSzuro && !$nemVegetarianusSzuro) {
            $dietTags[] = 'Vegán';
        }
        
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

Route::get('/check-username', function (Request $request) {
    $username = $request->query('username');
    $available = !Felhasznalo::where('nev', $username)->exists();
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

Route::post('/interakcio/like', [InterakcioController::class, 'likeRecept'])->middleware('auth');
Route::post('/interakcio/dislike', [InterakcioController::class, 'dislikeRecept'])->middleware('auth');



Route::get('/interakciok', function () {
    $felhasznalo = Auth::user();

    $likedReceptek = $felhasznalo->interakciok
        ->where('liked', 1)
        ->pluck('recept_id');

    return \App\Models\Recept::whereIn('id', $likedReceptek)->get();
});
Route::middleware('auth')->group(function () {
    
    Route::get('/api/kedvencek', [KedvencController::class, 'index'])
        ->name('kedvencek.index');
    
    Route::post('/api/kedvencek', [KedvencController::class, 'store'])
        ->name('kedvencek.store');
    
    Route::delete('/api/kedvencek/{receptId}', [KedvencController::class, 'destroy'])
        ->name('kedvencek.destroy');
    
    Route::get('/api/kedvencek/check/{receptId}', [KedvencController::class, 'check'])
        ->name('kedvencek.check');
    
    Route::get('/kedvencek', function () {
        return Inertia::render('Kedvencek');
    })->name('kedvencek');
});

require __DIR__.'/settings.php';
