<?php

namespace App\Models;

use App\Enums\Status;
use Exception;
use App\Enums\SchoolStaffRole;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class SchoolStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "gschool_staffs";
    protected $perPage = 20;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => 'int',
        'status' => Status::class,
    ];

    protected $fillable = [
        'id',
        'school_id',
        'school_staff_no',
        'name',
        'role',
    ];

    public static function buildQuery(array $params): Builder
    {
        $params = array_merge([
            'school_staff_no' => false,
            'name' => false,
        ], $params);

        return static::when($params['school_staff_no'], function (Builder $query, $school_staff_no) {
            return $query->where('school_staff_no', 'like', "%{$school_staff_no}%");
        })->when($params['name'], function (Builder $query, $name) {
            return $query->where('name', 'like', "%{$name}%");
        });
    }

    public static function handleDelete($model, $user, $authUser)
    {
        try {
            DB::transaction(function () use ($model, $user, $authUser) {
                $model->status = Status::DISABLED();
                $model->deleted_user_id = $authUser->id;
                $model->deleted_at = now();
                $user->deleted_user_id = $authUser->id;
                $user->status = Status::DISABLED();
                $user->deleted_at = now();
                $user->save();
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
