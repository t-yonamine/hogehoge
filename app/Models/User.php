<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Auth;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = 'gusers';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'id',
        'school_id',
        'login_id',
        'password',
        'remember_token',
        'status',
        'created_user_id'
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
        'status' => 'int',
        'password' => 'string',
        'created_user_id' => 'int'
    ];

    public function schoolStaff()
    {
        return $this->hasOne(SchoolStaff::class, 'id', 'id');
    }

    public function staff()
    {
        return $this->hasOne(Staff::class, 'id', 'id');
    }

    public function getName()
    {
        $user = Auth::user();
        if (!$user->school_id) {
            return  $this->staff?->name;
        } else {
            return  $this->schoolStaff?->name;
        }
    }
}
