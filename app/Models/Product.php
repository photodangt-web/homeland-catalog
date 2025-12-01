<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'codigo_producto',
        'nombre_producto',
        'cantidad',
        'fotografia',
        'precio',
        'fecha_ingreso',
        'fecha_vencimiento',
    ];

    protected $casts = [
        'precio' => 'decimal:2',
        'fecha_ingreso' => 'date',
        'fecha_vencimiento' => 'date',
    ];
}