<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\Felhasznalo;
use Illuminate\Http\Request;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;


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
    return \App\Models\Recept::with('felhasznalo')
        ->orderBy('created_at', 'desc')
        ->get();
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

Route::post('/bejelentkezes', [LoginController::class, 'login'])->name('bejelentkezes');

Route::post('/logout', function () {
    Auth::logout();
    return redirect()->route('bejelentkezes');
})->name('logout');
require __DIR__.'/settings.php';
