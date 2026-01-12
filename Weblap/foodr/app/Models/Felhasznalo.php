<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Hash;

class Felhasznalo extends Authenticatable
{
    use HasFactory, Notifiable;

    // 🟢 A tábla neve az adatbázisban
    protected $table = 'felhasznalo';

    // 🟢 Ezeket lehet tömegesen kitölteni (fillable)
    protected $fillable = [
        'nev',        // username
        'email',      // email
        'password',   // jelszó
    ];

    // 🟢 Ha jelszót mentünk, automatikusan hash-eljük
    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = Hash::make($value);
    }

    // 🟢 Laravel alapból ezt várja az authnál
    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
}
