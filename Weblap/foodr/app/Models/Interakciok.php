<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interakciok extends Model
{
    protected $table = 'interakciok';

    
    protected $fillable = [
        'felhasznalo_id',
        'recept_id',
        'liked',
        'mentett',
        'vote'
    ];
}
