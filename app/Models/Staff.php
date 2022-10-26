<?php

namespace App\Models;

use App\Enums\Status;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class Staff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gstaffs';
    protected $perPage = 20;

    protected $fillable = [
        'id',
        'staff_no',
        'name',
        'role',
        'status',
    ];

    public static function handleSave(array $data, int $id, int $user_id)
    {
        try {
            DB::transaction(function () use ($data, $id, $user_id) {
                //　担当者更新
                Staff::where('id', $id)->update([
                    'staff_no' => $data['staff_no'],
                    'name' => $data['name'],
                    'updated_user_id' => $user_id
                ]);
                //　ユーザー更新
                //　　パスワードに入力がない場合は、パスワードの更新は行わない
                if ($data['password']) {
                    User::where('id', $id)->update(
                        [
                            'password' => Hash::make($data['password']),
                            'updated_user_id' => $user_id
                        ]
                    );
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function handleDelete(Staff $model)
    {
        try {
            DB::transaction(function () use ($model) {
                //　選択されたユーザーを無効にする
                //　　gusers.status = {無効} で更新
                $model->status = Status::DISABLE;
                //　　gstaffs.status = {無効} で更新
                $model->user->status = Status::DISABLE;
                $model->save();
                $model->user->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'id');
    }
}
