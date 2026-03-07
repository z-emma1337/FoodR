<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Kedvenc extends Model
{
    protected $table = 'kedvencek';
    
    protected $fillable = [
        'felhasznalo_id',
        'recept_id'
    ];

    public function felhasznalo(): BelongsTo
    {
        return $this->belongsTo(Felhasznalo::class, 'felhasznalo_id');
    }

    public function recept(): BelongsTo
    {
        return $this->belongsTo(Recept::class, 'recept_id');
    }
}