<?php

namespace App\Models;

use App\Enums\AbsentType;
use App\Enums\CancelType;
use App\Enums\LaType;
use App\Enums\LessonAttendStatus;
use App\Enums\LessonCode;
use App\Enums\PerfectScore;
use App\Enums\ResultType;
use App\Enums\Seq;
use App\Enums\StageType;
use App\Enums\Status;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LessonAttend extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'glesson_attends';
    protected const SEPARATOR = ',';
    protected const SEPARATOR_FULL_SIZE = '、';
    protected const NEWLINE = "\n";
    protected const NEWLINE_IN_LOG = "\r\n";
    protected const MAX_COLL_CSV = 8;
    protected const MIN_COLL_CSV = 0;
    protected const PRE_PROVISIONAL = 11; //仮免前
    protected const BEFORE_GRADUATION = 12; //卒検前
    protected const VALUE_ZER0 = 0;
    protected const VALUE_ONE = 1;
    protected const ARRAY_FIELD_TO_KEY = [
        'student_no' => '1', // col_1
        'period_date' => '2', // col_2
        'period_from' => '3', // col_3
        'question_num' => '4', // col_4
        'score' => '5', // col_5
        'result' => '6', // col_6
        'la_type' => '7', // col_7
        'perfect_score' => '8', // col_8
    ];
    protected const ARRAY_KEY_TO_FIELD = [
        '1' => 'student_no', // col_1
        '2' => 'period_date', // col_2
        '3' => 'period_from', // col_3
        '4' => 'question_num', // col_4
        '5' => 'score', // col_5
        '6' => 'result', // col_6
        '7' => 'la_type', // col_7
        '8' => 'perfect_score', // col_8
    ];

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
        return $this->hasOne(LessonComment::class, 'lesson_attend_id', 'id');
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
    public static function handleUpdateOrInsert(array $data,  LessonAttend $model = null)
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
            ->where('test_id', $testId)
            ->groupBy('glesson_attends.la_type', 'gledgers.target_license_cd')->get();
        return $numberOfParticipants;
    }

    public static function readCsv($files)
    {
        $responses = [];
        $user = Auth::user();
        foreach ($files as $key => $line) {
            $msgError = [];
            // a. 空行の場合、この行は読み飛ばし、次の行の処理へ移る。
            if (count($line) == self::MIN_COLL_CSV) {
                continue;
            }

            // b. 行の項目数が想定と合わない場合はエラー。
            if (count($line) != self::MAX_COLL_CSV) {
                $msgError[] = Lang::get('messages.MSE00011');
                goto end;
            }

            //先頭行はヘッダーとして扱う
            if (!isset($columns)) {
                try {
                    $columns =  self::getCsvConversionTable();
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                    throw $th;
                }
            }

            foreach ($columns as $key => $column) {
                if (!$column) {
                    continue;
                }
                $attributes[$column] = trim($line[$key]) ?? '';
            }

            //各項目のサイズ、形式、文字種などをチェックする
            $validators = static::csvValidator($attributes);
            if ($validators->fails()) {
                $msgError[] = __('messages.MSE00015', ['label' => join(self::SEPARATOR_FULL_SIZE, $validators->errors()->all())]);
                goto end;
            }

            // 整理番号に一致する教習生の存在をチェック
            $studentNo = $attributes['student_no'];
            //  a. CSVの教習生番号で教習原簿TBLへアクセス。
            $admCheckItem = AdmCheckItem::whereHas('ledger', function ($q) {
                $q->where('status', Status::ENABLED());
            })->where('school_id', $user->schoolStaff->school_id)->where('student_no', $studentNo)->where('status', Status::ENABLED)->first();
            if (!$admCheckItem) {
                $msgError[] = Lang::get('messages.MSE00012');
                $attributes['name_kana'] = '';
                goto end;
            } else {
                $attributes['name_kana'] = $admCheckItem->name_kana;
            }

            $laType = $attributes['la_type'];
            // a. CSVのテスト区分 = 11:仮免前 の場合、
            if ($laType === self::PRE_PROVISIONAL) {
                if ($admCheckItem->ledger->lesson_sts <= LessonCode::BEFORE) {
                    $msgError[] = Lang::get('messages.MSE00013');
                }
            } else {
                // b. CSVのテスト区分 = 12:卒検前 の場合、
                if ($admCheckItem->ledger->lesson_sts <= LessonCode::PL_TESTWAIT) {
                    $msgError[] = Lang::get('messages.MSE00014');
                }
            }

            end:
            // to custom data show
            if (!$validators->errors()->first('period_date')) {
                $attributes['period_date_text'] = date('Y/m/d', strtotime($attributes['period_date']));
            } else {
                $attributes['period_date_text'] =  $attributes['period_date'];
            }
            if (!$validators->errors()->first('result')) {
                $attributes['result_text'] = $attributes['result'] == ResultType::OK ? ResultType::OK()->description : ResultType::NG()->description;
            } else {
                $attributes['result_text'] =  $attributes['result'];
            }
            if (!$validators->errors()->first('perfect_score')) {
                $attributes['perfect_score_text'] = $attributes['perfect_score'] == self::VALUE_ZER0 ? PerfectScore::ONE_HUNDRED : PerfectScore::FIFTY;
            } else {
                $attributes['perfect_score_text'] =  $attributes['perfect_score'];
            }
            if (!$validators->errors()->first('la_type')) {
                $attributes['la_type_text'] = $attributes['la_type'] == self::PRE_PROVISIONAL ? LaType::EFF_MEAS_1()->description : LaType::EFF_MEAS_2()->description;
            } else {
                $attributes['la_type_text'] =  $attributes['la_type'];
            }
            $attributes['error'] = join(self::NEWLINE, $msgError);

            $attributes['disabled'] = false;
            $attributes['id'] = $key;
            $responses[] = $attributes;
        }
        return $responses;
    }

    public static function insertFromTable($dataInput, $fileName)
    {
        try {
            $dataOutput = DB::transaction(function () use ($dataInput,  $fileName) {
                $user = Auth::user();
                // 登録行数
                // 除外行数
                // エラー行数
                // 合計行数
                $summary = [
                    'number_of_registered_lines' => self::VALUE_ZER0,
                    'excluded_rows' => self::VALUE_ZER0,
                    'number_of_error_lines' => self::VALUE_ZER0,
                    'total_rows' => self::VALUE_ZER0,
                ];
                foreach ($dataInput as $key => $attributes) {
                    $summary['total_rows'] =  $key + self::VALUE_ONE;
                    $infoLog = [
                        'row_index' => $key + self::VALUE_ONE,
                        'file_name' => $fileName,
                        'item_number' => '',
                        'message_error' => '',
                        'item_value' => '',
                        'student_no' => $attributes['student_no'],
                    ];

                    // check excluded rows
                    if ($attributes['disabled'] == "true") {
                        $summary['excluded_rows'] =  ++$summary['excluded_rows'];
                        continue;
                    }

                    //各項目のチェック
                    $validators = static::csvValidator($attributes);
                    if ($validators->fails()) {
                        $summary['number_of_error_lines'] =  ++$summary['number_of_error_lines'];

                        $errors = $validators->errors()->all();
                        $infoLog['item_number'] = join(self::SEPARATOR_FULL_SIZE, $errors);
                        foreach ($errors as $key => $value) {
                            $infoLog['item_value'] =  $infoLog['item_value'] . $attributes[self::ARRAY_KEY_TO_FIELD[$value]];
                            if (count($errors) != $key + self::VALUE_ONE) {
                                $infoLog['item_value'] =  $infoLog['item_value'] . self::SEPARATOR_FULL_SIZE;
                            }
                        }

                        $infoLog['message_error'] =  __('messages.MSE00015', ['label' => join(self::SEPARATOR_FULL_SIZE, $validators->errors()->all())]);
                        self::writingLog($user, $infoLog);
                        continue;
                    }


                    // 整理番号に一致する教習生の存在をチェック
                    $studentNo = $attributes['student_no'];
                    //  a. CSVの教習生番号で教習原簿TBLへアクセス。
                    $admCheckItem = AdmCheckItem::whereHas('ledger', function ($q) {
                        $q->where('status', Status::ENABLED());
                    })->where('school_id', $user->schoolStaff->school_id)->where('student_no', $studentNo)->where('status', Status::ENABLED)->first();
                    if (!$admCheckItem) {
                        $summary['number_of_error_lines'] =  ++$summary['number_of_error_lines'];
                        $infoLog['message_error'] = Lang::get('messages.MSE00012');
                        $infoLog['item_number'] = self::ARRAY_FIELD_TO_KEY['student_no'];
                        $infoLog['item_value'] = $studentNo;
                        self::writingLog($user, $infoLog);
                        continue;
                    }

                    $laType = $attributes['la_type'];
                    // a. CSVのテスト区分 = 11:仮免前 の場合、
                    if ($laType === self::PRE_PROVISIONAL) {
                        if ($admCheckItem->ledger->lesson_sts <= LessonCode::BEFORE) {
                            $summary['number_of_error_lines'] =  ++$summary['number_of_error_lines'];
                            $infoLog['message_error'] = Lang::get('messages.MSE00013');
                            $infoLog['item_number'] = self::ARRAY_FIELD_TO_KEY['la_type'];
                            $infoLog['item_value'] = $laType;
                            self::writingLog($user, $infoLog);
                            continue;
                        }
                    } else {
                        // b. CSVのテスト区分 = 12:卒検前 の場合、
                        if ($admCheckItem->ledger->lesson_sts <= LessonCode::PL_TESTWAIT) {
                            $summary['number_of_error_lines'] =  ++$summary['number_of_error_lines'];
                            $infoLog['message_error'] = Lang::get('messages.MSE00014');
                            $infoLog['item_number'] = self::ARRAY_FIELD_TO_KEY['la_type'];
                            $infoLog['item_value'] = $laType;
                            self::writingLog($user, $infoLog);
                            continue;
                        }
                    }

                    // D. 受講(glesson_attends)の追加
                    $dataLessonAttend = [
                        'school_id' => $user->schoolStaff->school_id,
                        'ledger_id' => $admCheckItem->ledger_id,
                        'la_type' => $attributes['la_type'] == self::PRE_PROVISIONAL ? LaType::EFF_MEAS_1N() : LaType::EFF_MEAS_2N(),
                        'stage' => $attributes['la_type'] == self::PRE_PROVISIONAL ?  StageType::STAGE_1() : StageType::STAGE_2(),
                        'school_staff_id' => $user->id,
                        'period_date' => date('Y-m-d', strtotime($attributes['period_date'])),
                        'period_from' => date('h:m', strtotime($attributes['period_from'])),
                        'period_to' => date('h:m', strtotime($attributes['period_from'])),
                        'score' => $attributes['score'],
                        'result' => $attributes['result'],
                        'question_num' => $attributes['question_num'],
                        'perfect_score' => $attributes['perfect_score_text'],
                        'status' => LessonAttendStatus::APPROVED(),
                        'created_user_id' => $user->id,
                        'updated_user_id' => $user->id,
                    ];
                    // E. 教習原簿(gledgers)の効果測定日の更新
                    $ledger = Ledger::find($admCheckItem->ledger_id);
                    self::handleSave($dataLessonAttend, $ledger, null);
                    $summary['number_of_registered_lines'] =  ++$summary['number_of_registered_lines'];
                }
                return $summary;
            });
        } catch (Exception $e) {
            throw $e;
        }
        return $dataOutput;
    }

    /**
     * CSV各項目のサイズ、形式、文字種などをチェックする
     *
     * @param  array $line
     * @return object
     */
    private static function csvValidator($line)
    {
        $validOneToTwo = join(self::SEPARATOR, [self::VALUE_ZER0, self::VALUE_ONE]);

        $rule = [
            'student_no' => 'required|string|max:8|min:1',
            'period_date' => 'required|max:8|date_format:"Ymd"',
            'period_from' => 'required|min:4|max:4|alpha_num',
            'question_num' => 'required|regex:/^[0-9]+$/|max:3',
            'score' => 'required|regex:/^[0-9]+$/|max:3',
            'result' => 'required|in:' . $validOneToTwo,
            'la_type' => 'required|in:' . self::PRE_PROVISIONAL . self::SEPARATOR . self::BEFORE_GRADUATION,
            'perfect_score' => 'required|in:' . $validOneToTwo,
        ];
        return Validator::make($line, $rule, self::ARRAY_FIELD_TO_KEY);
    }

    /**
     * CSVヘッダ名とテーブルカラム名の変換表を返す
     *
     * @return array
     */
    private static function getCsvConversionTable()
    {
        return [
            'student_no',
            'period_date',
            'period_from',
            'question_num',
            'score',
            'result',
            'la_type',
            'perfect_score'
        ];
    }

    /**
     *  ログのメッセージ欄に以下の情報を出力する。
     *
     * @return void
     */
    private static function writingLog($user, $infoLog)
    {
        $errorMess = self::NEWLINE_IN_LOG .
            '教習所ID:' . $user['school_id'] . self::NEWLINE_IN_LOG  .
            '職員ID:' . $user['id'] . self::NEWLINE_IN_LOG  .
            '処理:効果測定結果インポート ' . self::NEWLINE_IN_LOG  .
            'CSVファイル名:' . $infoLog['file_name'] . self::NEWLINE_IN_LOG  .
            '行番号:'  . $infoLog['row_index'] . self::NEWLINE_IN_LOG  .
            '項目番号:'  . $infoLog['item_number'] . self::NEWLINE_IN_LOG  .
            'メッセージ:'  . $infoLog['message_error'] . self::NEWLINE_IN_LOG  .
            '項目値:'  . $infoLog['item_value'] . self::NEWLINE_IN_LOG  .
            '教習生番号:'  . $infoLog['student_no'];
        Log::error($errorMess);
    }
}
