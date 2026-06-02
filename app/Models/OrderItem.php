<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    // Pastikan fillable kamu tetap seperti ini (atau sesuaikan dengan yang sudah kamu buat sebelumnya)
    protected $fillable = ['order_id', 'menu_id', 'harga', 'qty', 'subtotal'];

    // 1. TAMBAHKAN FUNGSI INI: Relasi ke tabel Order
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // 2. PASTIKAN FUNGSI INI ADA: Relasi ke tabel Menu
    public function menu()
    {
        return $this->belongsTo(Menu::class);
    }
}