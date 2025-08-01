<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Products extends Model
{
    use HasFactory;

    // Nama tabel jika berbeda dari 'products'
    // protected $table = 'nama_tabel_kamu';

    // Kolom yang dapat diisi secara massal
    protected $fillable = [
        'name',
        'images',
        'description',
        'price',
        'model_id',
    ];

    // Casting data
    protected $casts = [
        'price' => 'decimal:2',
    ];
    public function order_details()
    {
        return $this->hasMany(order_details::class);
    }
    public function stocks()
    {
        return $this->hasMany(stocks::class, 'product_id','id');
    }
    public function carts()
    {
        return $this->hasMany(carts::class);
    }
    public function model()
    {
        return $this->belongsTo(ModelRequest::class,'model_id');
    }
    public function getGroupedStokAttribute()
    {
        return $this->stocks
            ->groupBy(fn($item) => strtolower($item->color) . '-' . strtolower($item->size))
            ->map(function ($group) {
                $firstItem = $group->first();
                $color = $group->first()->color;
                $size  = $group->first()->size;

                $masuk  = $group->where(fn($s) => strtolower($s->status) === 'masuk')->sum('quantity');
                $keluar = $group->where(fn($s) => strtolower($s->status) === 'keluar')->sum('quantity');

                return [
                    'id' => $firstItem->id,
                    'color' => ucfirst(strtolower($color)),
                    'size' => strtoupper($size),
                    'stock' => $masuk - $keluar,
                ];
            })->values();
    }
}
