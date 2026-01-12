<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Felhasznalo extends Authenticatable
{
    protected $table = 'felhasznalo';

protected $fillable = [
    'nev',
    'email',
    'jelszo',
    'allergen_id',
];


    protected $hidden = [
        'jelszo',
    ];

    public function getAuthPassword()
    {
        return $this->jelszo;
    }
}
