<?php

namespace App\Models;
use App\Models\kommentek;
use Illuminate\Database\Eloquent\Model;

class Recept extends Model
{
    protected $table = 'recept';

    protected $fillable = [
        'felhasznalo_id',
        'nev',
        'leiras',
        'ido',
        'adag',
        'kep_url',
    ];

    public function felhasznalo()
    {
        return $this->belongsTo(Felhasznalo::class, 'felhasznalo_id');
    }

    public function receptAlapanyagok()
    {
        return $this->hasMany(ReceptAlapanyag::class, 'recept_id');
    }

    public function interakciok()
    {
        return $this->hasMany(Interakciok::class, 'recept_id');
    }

    public function alapanyagok()
    {
        return $this->belongsToMany(Alapanyag::class, 'recept_alapanyag', 'recept_id', 'alapanyag_id')
            ->withPivot('adag');
    }

    public function kommentek()
    {
        return $this->hasMany(kommentek::class, 'recept_id');
    }
}