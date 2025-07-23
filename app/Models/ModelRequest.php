<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModelRequest extends Model
{
    use HasFactory;

    protected $table = 'model_request';
    protected $fillable = ['title','description','file','status'];

    public function product()
    {
        return $this->hasOne(Products::class);
    }



}
