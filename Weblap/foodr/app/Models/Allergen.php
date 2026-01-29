<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Allergen extends Model
{
    protected $table = 'allergen';
    
    protected $fillable = [
        'nev'
    ];

    public function alapanyagok()
    {
        return $this->belongsToMany(Alapanyag::class, 'alapanyag_allergenek', 'allergen_id', 'alapanyag_id');
    }
}