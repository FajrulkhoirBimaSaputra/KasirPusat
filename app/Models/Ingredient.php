<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class Ingredient extends Model
{
    use HasFactory;

    protected $fillable = ['nama', 'stok', 'last_updated_by'];

    public function user()
    {
        return $this->belongsTo(User::class, 'last_updated_by');
    }

    public function histories()
    {
        return $this->hasMany(IngredientHistory::class);
    }
}