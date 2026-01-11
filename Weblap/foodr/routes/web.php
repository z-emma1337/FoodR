<?php

use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

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

Route::get('bejelentkezes', function () {
    return Inertia::render('Bejelentkezes');
})->name('bejelentkezes');


require __DIR__.'/settings.php';
