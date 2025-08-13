<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Order_details extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'color',
        'size',
    ];
    protected $casts = [
        'quantity' => 'integer', // Pastikan 'quantity' selalu menjadi integer
        'product_id' => 'integer',
        'order_id' => 'integer',
    ];
    /**
     * Relasi ke Order (setiap detail order milik satu order)
     */
    public function orders()
    {
        return $this->belongsTo(Orders::class,'order_id');
    }

    /**
     * Relasi ke Product (setiap detail order berkaitan dengan satu produk)
     */
    public function products()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }

    /**
     * Relasi ke Stock (untuk variasi produk seperti warna/ukuran)
     */
    public function stocks()
    {
        return $this->belongsTo(Stocks::class);
    }
}
