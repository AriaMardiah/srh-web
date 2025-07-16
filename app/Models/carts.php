<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Carts extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'user_id',
        'quantity',
    ];

    /**
     * Relasi ke Product (Setiap item cart dimiliki satu produk)
     */
    public function products()
    {
        return $this->belongsTo(Products::class);
    }

    /**
     * Relasi ke User (Setiap item cart dimiliki oleh satu user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
