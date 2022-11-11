<?php

namespace App\Models;

use App\Enums\DrlType;
use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Enums\StageType;
use App\Enums\WorkType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Period extends Model
{
    use HasFactory;

    protected $table = "gperiods";

    protected $casts = [
        'period_type' => PeriodType::class,
        'stage' => StageType::class,
        'status' => PeriodStatus::class,
        'period_from' => 'datetime',
        'period_to' => 'datetime',
        'drl_type' => DrlType::class,
        'work_type' => WorkType::class,
    ];

    protected $fillable = [
        'remarks',
        'status',
        'updated_at',
        'updated_user_id',
    ];

    public function lessonAttend()
    {
        return $this->hasMany(LessonAttend::class);
    }

    public function codes()
    {
        return $this->hasOne(Code::class, 'cd_value', 'course_type_cd');
    }


    /**
     * to handle insert period
     */
    public static function handleInsert($modelTest, $sessSchoolStaffId, $lessonAttend, $schoolPeriodM)
    {
        try {
            DB::transaction(function () use ($modelTest, $sessSchoolStaffId, $lessonAttend, $schoolPeriodM) {
                $currentTime = now();
                for ($periodNum = $modelTest->period_num_from; $periodNum <= $modelTest->period_num_to; $periodNum++) {
                    $schoolPeriodNum = $schoolPeriodM->first(function ($period) use ($periodNum) {
                        return $period->period_num == $periodNum;
                    });
                    $model = new static();
                    $model->period_type = PeriodType::TEST();
                    $model->school_id = $lessonAttend->school_id;
                    $model->period_date = $modelTest->test_date;
                    $model->period_num = $periodNum;
                    $model->period_name = $schoolPeriodNum->period_name;
                    $model->period_from = $schoolPeriodNum->period_from;
                    $model->period_to = $schoolPeriodNum->period_to;
                    $model->school_staff_id = $lessonAttend->school_staff_id;
                    $model->stage = $lessonAttend->stage;
                    $model->test_id = $modelTest->id;
                    $model->status = PeriodStatus::NEW();
                    $model->created_at = $currentTime;
                    $model->created_user_id = $sessSchoolStaffId;
                    $model->updated_at = $currentTime;
                    $model->updated_user_id = $sessSchoolStaffId;
                    $model->save();
                }
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * to handle update period
     */
    public static function handleSave(array $data, Period $model = null, $currentUserId)
    {
        try {
            $model = $model ?: new static;
            DB::transaction(function () use ($data, $model, $currentUserId) {
                $model->updated_at = now();
                $model->updated_user_id = $currentUserId;
                $model->fill($data);
                $model->save();
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public function schoolPeriodM()
    {
        return $this->belongsTo(SchoolPeriodM::class, 'period_num', 'period_num');
    }
}
