<?php

namespace App\Models;

use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class SchoolStaff extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "gschool_staffs";
    protected $perPage = 20;
    private const QUALIFICATION_VAL = 1;
    private const DEFAULT_VAL = 0;

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'role' => 'int',
        'status' => Status::class,
        'name' => 'string',
        'lic_expy_date' => 'date:Y-m-d',
        'is_revoked' => 'int',
        'is_beginner' => 'int',
        'is_senior' => 'int',
        'is_first_aid_1' => 'int',
        'is_first_aid_2' => 'int',
        'is_sim_4' => 'int',
        'is_sim_2' => 'int',
        'is_aptitude_1' => 'int',
        'is_aptitude_2' => 'int',
        'is_highway' => 'int',
        'is_road' => 'int',
        'is_wireless' => 'int',
    ];

    protected $fillable = [
        'id',
        'school_id',
        'school_staff_no',
        'name',
        'role',
        'school_staff_no',
        'name',
        'role',
        'lic_expy_date',
        // ----
        'lic_l_mvl',
        'lic_m_mvl',
        'lic_s_mvl',
        'lic_sl_mvl',
        'lic_l_ml',
        'lic_s_ml',
        'lic_sm_mvl',
        'lic_towing',
        'lic_l_mvl_2',
        'lic_m_mvl_2',
        'lic_s_mvl_2',
        'is_revoked',
        'is_beginner',
        'is_senior',
        'is_first_aid_1',
        'is_first_aid_2',
        'is_sim_4',
        'is_sim_2',
        'is_aptitude_1',
        'is_aptitude_2',
        'is_highway',
        'is_road',
        'is_wireless',
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
    public static function handleSave(array $data, int $userId, $userById, $model = null)
    {
        try {
            $model = $model ?: new static();
            DB::transaction(function () use ($data, $userId, $model, $userById) {
                if ($data['password']) {
                    $userById->fill([
                        'password' => Hash::make($data['password']),
                        'updated_user_id' => $userId,
                    ])->save();
                }

                $data['role'] = $userId == $userById->id ? array_sum($data['role']) + SchoolStaffRole::SYS_ADMINISTRATOR : array_sum($data['role']);

                $data['lic_l_mvl'] = $data['qualification_lic_l_mvl'] == self::QUALIFICATION_VAL ? array_sum($data['lic_l_mvl']) : self::DEFAULT_VAL;
                $data['lic_m_mvl'] = $data['qualification_lic_m_mvl'] == self::QUALIFICATION_VAL ? array_sum($data['lic_m_mvl']) : self::DEFAULT_VAL;
                $data['lic_s_mvl'] = $data['qualification_lic_s_mvl'] == self::QUALIFICATION_VAL ? array_sum($data['lic_s_mvl']) : self::DEFAULT_VAL;
                $data['lic_sl_mvl'] = $data['qualification_lic_sl_mvl'] == self::QUALIFICATION_VAL ? array_sum($data['lic_sl_mvl']) : self::DEFAULT_VAL;
                $data['lic_l_ml'] = $data['qualification_lic_l_ml'] == self::QUALIFICATION_VAL ? array_sum($data['lic_l_ml']) : self::DEFAULT_VAL;
                $data['lic_s_ml'] = $data['qualification_lic_s_ml'] == self::QUALIFICATION_VAL ? array_sum($data['lic_s_ml']) : self::DEFAULT_VAL;
                $data['lic_sm_mvl'] = $data['qualification_lic_sm_mvl'] == self::QUALIFICATION_VAL ? array_sum($data['lic_sm_mvl']) : self::DEFAULT_VAL;
                $data['lic_towing'] = $data['qualification_lic_towing'] == self::QUALIFICATION_VAL ? array_sum($data['lic_towing']) : self::DEFAULT_VAL;
                $data['lic_l_mvl_2'] = $data['qualification_lic_l_mvl_2'] == self::QUALIFICATION_VAL ? array_sum($data['lic_l_mvl_2']) : self::DEFAULT_VAL;
                $data['lic_m_mvl_2'] = $data['qualification_lic_m_mvl_2'] == self::QUALIFICATION_VAL ? array_sum($data['lic_m_mvl_2']) : self::DEFAULT_VAL;
                $data['lic_s_mvl_2'] = $data['qualification_lic_s_mvl_2'] == self::QUALIFICATION_VAL ? array_sum($data['lic_s_mvl_2']) : self::DEFAULT_VAL;
                $data['updated_user_id'] = $userId;

                $model->fill($data);
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }
}
