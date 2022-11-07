<?php

namespace App\Models;

use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Enums\StageType;
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
    ];

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
}
