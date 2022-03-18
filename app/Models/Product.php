<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

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
        'is_sales',
        'is_delete',
        'product_id'
    ];

    public function scopeProductName($query, $request)
    {
        if ($request->has('product_name')) {
            $query->where('product_name', 'LIKE', '%'.$request->input('product_name').'%')->get();
        }

        return $query;
    }

    public function scopeIsSales($query, $request)
    {
        if ($request->has('is_sales') && $request->input('is_sales') != '') {
            $query->where('is_sales', '=' , $request->input('is_sales'))->get();
        }

        return $query;
    }

    public function scopeProductPrice($query, $request)
    {
        if ($request->has('price_from')  && $request->input('price_from') != '') {
            $query->where('product_price', '>=' , $request->input('price_from'))->get();
        }

        if ($request->has('price_to')  &&  $request->input('price_to') != '') {
            $query->where('product_price', '<=' , $request->input('price_to'))->get();
        }

        return $query;
    }

    public function getProductImageAttribute($value)
    {
        return str_replace('\\', '/',env("PATH_IMG").'uploads/products/'.$value);
    }

}
