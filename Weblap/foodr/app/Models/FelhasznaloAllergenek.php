<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FelhasznaloAllergenek extends Model
{
    protected $table = 'felhasznalo_allergenek';

    protected $fillable = [
        'felhasznalo_id',
        'allergen_id'

    ];

    public function felhasznalo(){
        return $this->belongsTo(Felhasznalo::class, 'felhasznalo_id');
    }

    public function allergen(){
        return $this->belongsTo(Allergen::class, 'allergen_id');
    }
}
