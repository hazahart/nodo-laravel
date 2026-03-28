<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaccion extends Model
{
    protected $table = "transacciones";
    protected $fillable = [
        'datos',
        'minada',
    ];

    protected $casts = [
        'datos'  => 'array',
        'minada' => 'boolean',
    ];
}