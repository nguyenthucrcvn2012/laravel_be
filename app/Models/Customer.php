<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

class Customer extends Model
{
    use HasFactory;
    public $timestamps = true;
    protected $table = "customers";
    protected $primaryKey = "customer_id";
    public $incrementing;
    protected $fillable = [
        'customer_name',
        'email',
        'tel_num',
        'address',
        'is_active',
    ];

    public function scopeName($query, $request)
    {
        if ($request->has('customer_name') && $request->input('customer_name') != '') {
            $query->where('customer_name', 'LIKE', '%'.$request->input('customer_name').'%')->get();
        }

        return $query;
    }

    public function scopeEmail($query, $request)
    {
        if ($request->has('email') && $request->input('email') != '') {
            $query->where('email', 'LIKE', '%'.$request->input('email').'%')->get();
        }

        return $query;
    }

    public function scopeAddress($query, $request)
    {
        if ($request->has('address') && $request->input('address') != '') {
            $query->where('address', 'LIKE', '%'.$request->input('address').'%')->get();
        }

        return $query;
    }

    public function scopeIsActive($query, $request)
    {
        if ($request->has('is_active') && $request->input('is_active') != '') {
            $query->where('is_active', '=' , $request->input('is_active'))->get();
        }

        return $query;
    }
}
