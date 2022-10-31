<?php

namespace App\Models;

use App\Enums\SchoolStaffRole;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Enums\Status;

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
        'status' => Status::class,
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

    public static function handleDelete(School $model)
    {
        try {
            DB::transaction(function () use ($model) {
                $userId = Auth::id();
                $model->status = Status::DISABLED();
                $model->deleted_at = now();
                $model->deleted_user_id = $userId;
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }


    public static function handleCreate(array $data)
    {
        try {
            DB::transaction(function () use ($data) {
                $userId = Auth::id();
                // 教習所登録
                $schoolModel = new School();
                $schoolModel['school_cd'] = $data['school_cd'];
                $schoolModel['name'] = $data['name'];
                $schoolModel['name_kana'] = $data['name_kana'];
                $schoolModel['status'] = Status::ENABLED();
                $schoolModel['created_user_id'] = $userId;
                $schoolModel['updated_user_id'] = $userId;
                $schoolModel->save();

                // ユーザー登録
                $userModel = new User();
                $userModel['school_id'] = $schoolModel->id;
                $userModel['login_id'] = $data['login_id'];
                $userModel['password'] = Hash::make($data['password']);
                $userModel['status'] = Status::ENABLED();
                $userModel->save();

                // 教習所システム管理者の登録
                $schoolStaffModel = new SchoolStaff();
                $schoolStaffModel['id'] = $userModel->id;
                $schoolStaffModel['school_id'] = $schoolModel->id;
                $schoolStaffModel['school_staff_no'] = $data['school_staff_no'];
                $schoolStaffModel['name'] = $data['school_staff_name'];
                $schoolStaffModel['role'] = SchoolStaffRole::SYS_ADMINISTRATOR();
                $schoolStaffModel['status'] = Status::ENABLED();
                $schoolStaffModel->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
