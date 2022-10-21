<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "gschools";
    protected $perPage = 20;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'school_cd' => 'string',
        'name' => 'string',
        'name_kana' => 'string',
        'status' => 'int',
        'created_at' => 'datetime',
        'created_user_id' => 'int',
        'updated_at' => 'datetime',
        'updated_user_id' => 'int',
    ];

    public static function buildQuery(array $params): Builder
    {
        $params = array_merge([
            'school_cd' => false,
            'name_kana' => false,
        ], $params);

        return static::when($params['school_cd'], function (Builder $query, $school_cd) {
            return $query->where('school_cd', $school_cd);
        })->when($params['name_kana'], function (Builder $query, $name_kana) {
            return $query->where('name_kana','like', "%{$name_kana}%");
        });
    }
}
