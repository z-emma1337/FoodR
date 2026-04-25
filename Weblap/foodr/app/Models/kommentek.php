<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class kommentek extends Model
{
    protected $table = 'kommentek';

    protected $fillable = [
        'felhasznalo_id',
        'felhasznalo_nev',
        'pfpurl',
        'recept_id',
        'komment',
    ];

    public function felhasznalo()
    {
        return $this->belongsTo(Felhasznalo::class, 'felhasznalo_id');
    }

    public function recept()
    {
        return $this->belongsTo(Recept::class, 'recept_id');
    }
}
