<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recept extends Model
{
    protected $table = 'receptek';
    
    protected $fillable = [
        'felhasznalo_id',
        'nev',
        'leiras',
        'adag',
        'kep_url'
    ];

    public function felhasznalo()
    {
        return $this->belongsTo(Felhasznalo::class, 'felhasznalo_id');
    }
}