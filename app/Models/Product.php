<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "products";
    protected $primaryKey = "product_id";
    public $incrementing;
    protected $fillable = [
        'product_name',
        'product_price',
        'description',
        'is_active',
        'product_id',
    ];

}
