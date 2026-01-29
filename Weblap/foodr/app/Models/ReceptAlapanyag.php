<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReceptAlapanyag extends Model
{
    protected $table = 'recept_alapanyag';
    
    protected $fillable = [
        'recept_id',
        'alapanyag_id',
        'adag'
    ];

    public function recept()
    {
        return $this->belongsTo(Recept::class, 'recept_id');
    }

    public function alapanyag()
    {
        return $this->belongsTo(Alapanyag::class, 'alapanyag_id');
    }
}