<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EGroceryProduto extends Model
{
    protected $table = 'e-grocery_produtos';

    protected $fillable = {
        'nome',
        'categoria',
        'preco',
        'estoque',
        'status',
    }
    
}
