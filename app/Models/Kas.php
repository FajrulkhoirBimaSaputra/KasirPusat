<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kas extends Model
{
    protected $fillable = ['user_id', 'jenis', 'nominal', 'keterangan'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}