<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Stocks extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity',
        'color',
        'size',
        'status',
    ];

    /**
     * Relasi ke produk (satu varian stock dimiliki satu produk)
     */
    public function products()
    {
        return $this->belongsTo(Products::class,'product_id');
    }
    public function OrderDetail()
    {
        return $this->hasMany(Order_details::class);
    }
    
}
