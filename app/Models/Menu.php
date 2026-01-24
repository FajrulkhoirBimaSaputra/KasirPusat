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
    ];
}