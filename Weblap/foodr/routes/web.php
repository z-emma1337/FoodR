<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;
use App\Models\User;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canRegister' => Features::enabled(Features::registration()),
    ]);
})->name('home');

Route::get('dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('bejelentkezes', function () {
    return Inertia::render('Bejelentkezes');
})->middleware('guest')->name('login');

Route::get('regisztracio', function () {
    return Inertia::render('Regisztracio');
})->name('regisztracio');

Route::get('/check-username', function (Request $request) {
    return response()->json([
        'available' => !User::where('username', $request->username)->exists()
    ]);
});


require __DIR__.'/settings.php';
