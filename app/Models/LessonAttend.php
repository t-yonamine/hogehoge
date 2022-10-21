<?php

namespace App\Models;

use App\Enums\LaType;
use App\Enums\LessonAttendStatus;
use App\Enums\PerfectScore;
use App\Enums\ResultType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        'la_type' => 'int',
        'stage' => 'int',
        'school_staff_id' => 'int',
        'target_license_names' => 'string',
        'period_date' => 'datetime:Y-m-d',
        'period_from' => 'datetime:h:i',
        'period_to' => 'datetime:h:i',
        'score' => 'int',
        'result' => 'int',
        'question_num' => 'int',
        'remarks' => 'string',
        'perfect_score' => 'int',
        'status' => 'int',
    ];

    protected $attributes = [
        'perfect_score' => PerfectScore::ONE_HUNDRED,
        'status' => LessonAttendStatus::PENDING
    ];

    public static function handleSave(array $data, Ledger $ledger, LessonAttend $model = null)
    {
        try {
            $model = $model ?: new static;
            DB::transaction(function () use ($data, $ledger, $model) {
                // 3. 受講テーブルに追加する。
                $model->fill($data);
                $model->save();

                if ($model->result == ResultType::OK) {
                    // 4.1. 仮免前の場合
                    if ($model->la_type == LaType::PRE_EXAMINATION) {
                        $ledger->effect_meas1_date = $model->period_date;
                    } elseif ($model->la_type == LaType::GRADUATION) {
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
    }

    public function schoolStaff()
    {
        return $this->hasOne(SchoolStaff::class, 'id', 'school_staff_id');
    }
}
