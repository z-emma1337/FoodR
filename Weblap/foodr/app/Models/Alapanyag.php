<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alapanyag extends Model
{
    protected $table = 'alapanyag';
    
    protected $fillable = [
        'nev'
    ];

    public function receptek()
    {
        return $this->belongsToMany(Recept::class, 'recept_alapanyag', 'alapanyag_id', 'recept_id')
                    ->withPivot('adag');
    }

    public function allergenek()
    {
        return $this->belongsToMany(Allergen::class, 'alapanyag_allergenek', 'alapanyag_id', 'allergen_id');
    }
}