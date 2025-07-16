<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Payments extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'midtrans_transaction_id',
        'midtrans_order_id',
        'snap_token',
        'total_pembayaran',
        'metode_pembayaran',
        'status_pembayaran',
        'raw_response',
    ];

    /**
     * Relasi ke order (satu pembayaran milik satu order)
     */
    public function orders()
    {
        return $this->belongsTo(Orders::class);
    }
}
