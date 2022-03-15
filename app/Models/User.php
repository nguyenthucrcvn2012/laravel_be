<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Scope;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'group_role',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function scopeGroupRole($query, $request)
    {
        if ($request->has('group_role') && $request->input('group_role') != '') {
            $query->where('group_role', '=', $request->input('group_role'))->get();
        }

        return $query;
    }

    public function scopeName($query, $request)
    {
        if ($request->has('name') && $request->input('name') != '') {
            $query->where('name', 'LIKE', '%'.$request->input('name').'%')->get();
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

    public function scopeIsActive($query, $request)
    {
        if ($request->has('is_active') && $request->input('is_active') != '') {
            $query->where('is_active', '=' , $request->input('is_active'))->get();
        }

        return $query;
    }
}
