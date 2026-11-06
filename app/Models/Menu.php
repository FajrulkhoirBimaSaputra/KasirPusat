<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    protected $fillable = [
        'jenis',
        'nama',
        'harga',
        'image_path',
        'ingredient_id'
    ];

    public function ingredient()
    {
        return $this->belongsTo(Ingredient::class);
    }
}
