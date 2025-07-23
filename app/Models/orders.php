<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'total',
        'status',
    ];
    protected $casts = [
        'total' => 'integer', // Pastikan 'total' selalu menjadi integer
        'user_id' => 'integer',
    ];
    /**
     * Relasi ke user (order dimiliki oleh satu user)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function order_details()
    {
        return $this->hasMany(Order_details::class, 'order_id');
    }
    public function payments()
    {
        return $this->hasMany(payments::class, 'order_id');
    }
}
