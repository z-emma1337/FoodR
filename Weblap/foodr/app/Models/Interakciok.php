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

    public $timestamps = true;

    public function felhasznalo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'felhasznalo_id');
    }

    public function recept(): BelongsTo
    {
        return $this->belongsTo(Recept::class, 'recept_id');
    }
}
