<?php

namespace App\Models;

use App\Enums\AbsentType;
use App\Enums\CancelType;
use App\Enums\LaType;
use App\Enums\LessonAttendStatus;
use App\Enums\PerfectScore;
use App\Enums\ResultType;
use App\Enums\StageType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LessonAttend extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'glesson_attends';

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $guarded = [];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ledger_id' => 'int',
        'school_id' => 'int',
        'la_type' => LaType::class,
        'stage' => StageType::class,
        'school_staff_id' => 'int',
        'target_license_names' => 'string',
        'period_date' => 'datetime:Y-m-d',
        'period_from' => 'datetime:h:i',
        'period_to' => 'datetime:h:i',
        'score' => 'int',
        'result' => ResultType::class,
        'question_num' => 'string',
        'remarks' => 'string',
        'perfect_score' => PerfectScore::class,
        'status' => LessonAttendStatus::class,
        'is_absent' => AbsentType::class,
        'cancel_cd' => CancelType::class,
    ];

    protected $attributes = [
        'perfect_score' => PerfectScore::ONE_HUNDRED,
        'status' => LessonAttendStatus::PENDING
    ];

    public function lessonComments()
    {
        return $this->hasOne(LessonComment::class, 'ledger_id', 'ledger_id');
    }

    public function school()
    {
        return $this->hasOne(School::class, 'id', 'school_id');
    }

    public function schoolStaff()
    {
        return $this->hasOne(SchoolStaff::class, 'id', 'school_staff_id');
    }

    public function ledger()
    {
        return $this->belongsTo(Ledger::class, 'id', 'ledger_id');
    }

    public function admCheckItem()
    {
        return $this->hasOne(AdmCheckItem::class, 'ledger_id', 'ledger_id');
    }

    public function lessonItemMastery()
    {
        return $this->hasMany(LessonItemMastery::class, 'lesson_attend_id', 'id');
    }

    public function dispatchCar()
    {
        return $this->hasMany(DispatchCar::class, 'ledger_id', 'ledger_id');
    }

    public function image()
    {
        return $this->hasOne(Image::class, 'target_id', 'ledger_id');
    }

    public static function handleSave(array $data, Ledger $ledger, LessonAttend $model = null)
    {
        try {
            $model = $model ?: new static;
            DB::transaction(function () use ($data, $ledger, $model) {
                // 3. 受講テーブルに追加する。
                $model->fill($data);
                $model->save();

                if ($model->result == ResultType::OK()) {
                    // 4.1. 仮免前の場合
                    if ($model->la_type == LaType::EFF_MEAS_1N()) {
                        $ledger->effect_meas1_date = $model->period_date;
                    } elseif ($model->la_type == LaType::EFF_MEAS_2N()) {
                        // 4.2. 卒検前の場合
                        $ledger->effect_meas2_date = $model->period_date;
                    }
                    $ledger->updated_user_id = $model->updated_user_id;
                    $ledger->save();
                }
            });
        } catch (Exception $e) {
            throw $e;
        }
        return $model;
    }

    /**
     * handle update only lesson attend
     */
    public static function handleUpdate(array $data,  LessonAttend $model = null)
    {
        try {
            $model = $model ?: new static;
            DB::transaction(function () use ($data,  $model) {
                // 3. 受講テーブルに追加する。
                $model->fill($data);
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
        return $model;
    }

    public static function handleDelete(LessonAttend $model)
    {
        try {
            DB::transaction(function () use ($model) {
                $userId = Auth::id();
                $model->status = LessonAttendStatus::WAITING_FOR_APPLICATION();
                $model->deleted_at = now();
                $model->deleted_user_id = $userId;
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function rearrangeList($listLessonAttends, $loginId, $isIncreasing)
    {
        $currentTime = now();
        $testNumValueToAdd = $isIncreasing ? 1 : -1;
        foreach ($listLessonAttends as $item) {
            static::rearrangePerRecord($item, $loginId, $currentTime, $testNumValueToAdd);
        }
    }

    public static function rearrangePerRecord($lessAttend, $loginId, $currentTime, $testNumValueToAdd)
    {
        try {
            DB::transaction(function () use ($lessAttend, $loginId, $currentTime, $testNumValueToAdd) {
                $lessAttend->test_num = $lessAttend->test_num + $testNumValueToAdd;
                $lessAttend->updated_at = $currentTime;
                $lessAttend->updated_user_id = $loginId;
                $lessAttend->save();
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    public static  function countParticipants($testId, $sessSchoolStaffId)
    {
        $numberOfParticipants = LessonAttend::select('glesson_attends.la_type', 'gledgers.target_license_cd', DB::raw('count(*) as total'))
            ->join('gledgers', 'gledgers.id', '=', 'glesson_attends.ledger_id')
            ->where('school_staff_id', $sessSchoolStaffId)
            ->groupBy('glesson_attends.la_type', 'gledgers.target_license_cd')->get();
        return $numberOfParticipants;
    }
}
