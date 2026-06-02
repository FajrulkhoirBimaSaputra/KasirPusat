<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'stok', 'last_updated_by'];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'last_updated_by');
    }
}