<?php

namespace App\Models;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

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

    protected $fillable = [
        'school_cd',
        'name',
        'name_kana'
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
            return $query->where('name_kana', 'like', "%{$name_kana}%");
        });
    }

    public static function handleSave(array $data, User $user, SchoolStaff $schoolStaff,  School $model = null)
    {
        try {
            $model = $model ?: new static;
            DB::transaction(function () use ($data, $user, $schoolStaff, $model) {
                // ・教習所テーブル（gschools）に変更内容をUPDATEを行う。
                $userId =  Auth::id();
                $schoolFill = [
                    'school_cd' => $data['school_cd'],
                    'name' => $data['name'],
                    'name_kana' => $data['name_kana'],
                    'updated_user_id' => $userId,
                ];
                $model->fill($schoolFill);
                $model->save();

                // ・ユーザー更新										
                //    パスワードに入力がない場合は、更新は行わない									
                if ($data['password']) {
                    $user->fill([
                        'password' => Hash::make($data['password']),
                        'updated_user_id' => $userId,
                    ])->save();
                }

                // ・教習所システム管理者の更新
                $schoolStaff->fill([
                    'school_staff_no' => $data['school_staff_no'],
                    'name' => $data['school_staff_name'],
                    'updated_user_id' => $userId,
                ])->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
