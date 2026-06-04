<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
protected $fillable = [
    'user_id',
    'total',
    'uang_bayar',
    'uang_kembali',
    'payment_method',
    'payment_status',
    'snap_token',
    'with_receipt'
];
public function user()
    {
        return $this->belongsTo(User::class);
    }

public function items()
{
return $this->hasMany(OrderItem::class);
}
}