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
        'product_image',
        'description',
        'is_active',
        'is_delete',
        'product_id',
    ];

}
