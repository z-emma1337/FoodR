<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;

class Felhasznalo extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    protected $table = 'felhasznalo';

    protected $fillable = [
        'nev',
        'email',
        'jelszo',
        'allergen_id',
        'email_verified_at',
        'profilkepurl',
    ];

    protected $hidden = [
        'jelszo',
    ];

    public function getAuthPassword()
    {
        return $this->jelszo;
    }

    public function getAuthPasswordName()
    {
        return 'jelszo';
    }

    public function sendEmailVerificationNotification()
    {
        $this->notify(new \App\Notifications\VerifyEmailNotification);
    }

    public function interakciok()
    {
        return $this->hasMany(\App\Models\Interakcio::class, 'felhasznalo_id');
    }

    public function allergenek()
{
    return $this->belongsToMany(
        Allergen::class,
        'felhasznalo_allergenek',
        'felhasznalo_id',
        'allergen_id'
    );
}
}