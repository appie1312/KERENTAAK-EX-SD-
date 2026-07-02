<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'categorie_id',
    'leverancier_id',
    'naam',
    'barcode',
    'prijs',
    'voorraad',
    'houdbaarheidsdatum',
    'omschrijving',
    'status',
    'is_actief',
    'opmerking',
])]
class Product extends Model
{
    public $timestamps = false;

    protected $table = 'products';

    protected function casts(): array
    {
        return [
            'prijs' => 'decimal:2',
            'houdbaarheidsdatum' => 'date',
            'is_actief' => 'boolean',
        ];
    }
}
