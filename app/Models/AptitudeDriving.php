<?php

namespace App\Models;

use App\Enums\Encoding;
use App\Enums\ResultCharacter;
use App\Enums\Seq;
use App\Enums\Status;
use App\Enums\TestType;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class AptitudeDriving extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'gaptitude_drvs';

    protected const SEPARATOR = ',';
    protected const NEWLINE = "\n";

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'ledger_id' => 'int',
        'school_id' => 'int',
        'seq' => Seq::class,
        'test_type' => TestType::class,
        'test_date' => 'datetime:Y-m-d',
        'score' => 'string',
        'od_persty_pattern_1' => 'string',
        'od_persty_pattern_2' => 'string',
        'od_drv_aptitude' => 'string',
        'od_safe_aptitude' => 'string',
        'od_specific_rxn' => 'string',
        'od_a' => 'string',
        'od_b' => 'string',
        'od_c' => 'string',
        'od_d' => 'string',
        'od_e' => 'string',
        'od_f' => 'string',
        'od_g' => 'string',
        'od_h' => 'string',
        'od_i' => 'string',
        'od_j' => 'string',
        'od_k' => 'string',
        'od_l' => 'string',
        'od_m' => 'string',
        'od_n' => 'string',
        'od_o' => 'string',
        'od_p' => 'string',
        'status' => Status::class,
        'created_user_id' => 'int',
        'updated_user_id' => 'int',
    ];

    protected $fillable = [
        'ledger_id',
        'seq',
        'school_id',
        'test_type',
        'test_date',
        'score',
        'od_persty_pattern_1',
        'od_persty_pattern_2',
        'od_drv_aptitude',
        'od_safe_aptitude',
        'od_specific_rxn',
        'od_a',
        'od_b',
        'od_c',
        'od_d',
        'od_e',
        'od_f',
        'od_g',
        'od_h',
        'od_i',
        'od_j',
        'od_k',
        'od_l',
        'od_m',
        'od_n',
        'od_o',
        'od_p',
        'status',
        'created_user_id',
        'updated_user_id'
    ];

    public static function readCsv($files)
    {
        $responses = [];
        $user = Auth::user();
        foreach ($files as $key => $line) {
            mb_convert_variables(Encoding::UTF8, Encoding::SHIFT_JIS, $line);
            //先頭行はヘッダーとして扱う
            if (!isset($columns)) {
                try {
                    $columns = array_map('self::csvHeaderTableColumn', $line);
                } catch (\Throwable $th) {
                    Log::error($th->getMessage());
                    throw $th;
                }
                continue;
            }

            foreach ($columns as $key => $column) {
                if (!$column) {
                    continue;
                }
                $attributes[$column] = trim($line[$key]) ?? '';
            }

            $msgError = [];
            $errorDate = '';
            if ($attributes['student_no']) {
                //各項目のサイズ、形式、文字種などをチェックする
                $validators = static::csvValidator($attributes);
                if ($validators->fails()) {
                    $errorDate = $validators->errors()->first('date');
                    $msgError[] = Lang::get('messages.MSE00008');
                }
                // 整理番号に一致する教習生の存在をチェック
                $studentNo = $attributes['student_no'];
                $checkIdLedger = Ledger::where('school_id', $user->schoolStaff->school_id)->where('student_no', $studentNo)->where('status', Status::ENABLED)->first();
                if (!$checkIdLedger) {
                    $msgError[] = Lang::get('messages.MSE00009');
                }
            }
            $attributes['error'] = join(self::NEWLINE, $msgError);
            if (!$errorDate) {
                $attributes['date'] = date('Y/m/d', strtotime($attributes['date']));
            }
            $attributes['disabled'] = $attributes['error'] ? true : false;
            $attributes['id'] = $key;
            $responses[] = $attributes;
        }
        return $responses;
    }

    public static function insertFromTable($dataInput)
    {
        $dataOutput = DB::transaction(function () use ($dataInput) {
            $user = Auth::user();
            $dataOutput = [];
            foreach ($dataInput as $key => $attributes) {
                $dataTemp = $attributes;
                //除外のチェックがONのレコードは保存しない
                //除外のチェックがOFFのレコードが保存対象データ
                if ($attributes['disabled'] == "false") {
                    unset($attributes['error']);
                    unset($attributes['disabled']);
                    $attributes['date'] = date('Ymd', strtotime($attributes['date']));
                    //各項目のチェック
                    $validators = static::csvValidator($attributes);
                    $checkIdLedger = Ledger::where('student_no', $attributes['student_no'])->where('status', Status::ENABLED)->first();
                    if ($validators->fails()) {
                        $dataTemp['error'] = Lang::get('messages.MSE00008');
                    } else if ($checkIdLedger) {
                        //不要なアイテムの追加と削除
                        $attributes['ledger_id'] = $checkIdLedger->id;
                        $attributes['seq'] = Seq::FIRST1;
                        $attributes['school_id'] = $checkIdLedger->school_id;
                        $attributes['test_type'] = TestType::OD;
                        $attributes['test_date'] = date('Y/m/d', strtotime($attributes['date']));
                        $attributes['score'] = $attributes['od_drv_aptitude'] . $attributes['od_safe_aptitude'];
                        $attributes['status'] = Status::ENABLED;
                        unset($attributes['date']);
                        unset($attributes['name']);
                        unset($attributes['gender']);
                        unset($attributes['age']);
                        unset($attributes['student_no']);
                        //運転適性検査結果を保存する
                        static::handleSaveFile($attributes, $user->id);
                        $dataTemp['success'] = Lang::get('messages.MSI00005');
                        $dataTemp['disabled'] = true;
                    } else {
                        $dataTemp['error'] = Lang::get('messages.MSE00009');
                    }
                }
                $dataTemp['id'] = $key;
                $dataOutput[] = $dataTemp;
            }
            return $dataOutput;
        });
        return $dataOutput;
    }

    public static function handleSave(array $aptitudeDrvs, AptitudeDriving $mode = null)
    {
        try {
            $mode = new AptitudeDriving();
            DB::transaction(function () use ($aptitudeDrvs, $mode) {
                $userId = Auth::id();
                $aptitudeDrvs['created_user_id'] = $userId;
                $aptitudeDrvs['updated_user_id'] = $userId;
                $mode->fill($aptitudeDrvs);
                $mode->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function handleSaveFile(array $data, int $userId, $model = null)
    {
        try {
            $model = $model ?: new static();
            DB::transaction(function () use ($data, $userId, $model) {
                $aptitudeDrvs['created_user_id'] = $userId;
                $aptitudeDrvs['updated_user_id'] = $userId;
                $model->fill($data);
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * CSV各項目のサイズ、形式、文字種などをチェックする
     *
     * @param  array $line
     * @return object
     */
    private static function csvValidator($line)
    {
        $validAtoC = join(self::SEPARATOR, [ResultCharacter::A, ResultCharacter::B, ResultCharacter::C]);
        $validAtoE = $validAtoC . self::SEPARATOR . join(self::SEPARATOR, [ResultCharacter::D, ResultCharacter::E]);
        $validAtoEMinus = $validAtoE . self::SEPARATOR . join(self::SEPARATOR, [ResultCharacter::DMINUS, ResultCharacter::EMINUS]);
        $rule = [
            'date' => 'required|max:8|date_format:"Ymd"',
            'student_no' => 'required|string|max:8|min:1',
            'name' => 'nullable|string|max:128',
            'od_persty_pattern_1' => 'required|int|max:2',
            'od_persty_pattern_2' => 'required|int|min:0|max:2',
            'od_drv_aptitude' => 'required|int|min:1|max:5',
            'od_safe_aptitude' => 'required|max:1|in:' . $validAtoE,
            'od_specific_rxn' => 'required|int|min:1|max:3',
            'od_a' => 'required|string|max:1|in:' . $validAtoE,
            'od_b' => 'required|string|max:1|in:' . $validAtoE,
            'od_c' => 'required|string|max:1|in:' . $validAtoE,
            'od_d' => 'required|string|max:2|in:' . $validAtoEMinus,
            'od_e' => 'required|string|max:1|in:' . $validAtoE,
            'od_f' => 'required|string|max:1|in:' . $validAtoE,
            'od_g' => 'required|string|max:1|in:' . $validAtoE,
            'od_h' => 'required|string|max:1|in:' . $validAtoC,
            'od_i'  => 'required|string|max:1|in:' . $validAtoC,
            'od_j'  => 'required|string|max:1|in:' . $validAtoC,
            'od_k' => 'required|string|max:1|in:' . $validAtoC,
            'od_l' => 'required|string|max:1|in:' . $validAtoC,
            'od_m' => 'required|string|max:1|in:' . $validAtoC,
            'od_n' => 'required|string|max:1|in:' . $validAtoC,
            'od_o' => 'required|string|max:1|in:' . $validAtoC,
            'od_p' => 'required|string|max:1|in:' . $validAtoC,
        ];
        $attributes = [
            'date' => '実施日',
            'student_no' => '整理番号',
            'name' => '氏名',
            'od_persty_pattern_1' => '性格パターン1',
            'od_persty_pattern_2' => '性格パターン2',
            'od_drv_aptitude' => '運転適性度',
            'od_safe_aptitude' => '安全運転度',
            'od_specific_rxn' => '特異反応',
            'od_a' => '注意力',
            'od_b' => '判断力',
            'od_c' => '柔軟性',
            'od_d' => '決断力',
            'od_e' => '緻密性',
            'od_f' => '動作の安定性',
            'od_g' => '適応性',
            'od_h' => '身体的健康度',
            'od_i' => '精神的健康度',
            'od_j' => '社会的成熟度',
            'od_k' => '情緒不安定性',
            'od_l' => '衝迫性・暴発性',
            'od_m' => '自己中心性',
            'od_n' => '神経質・過敏性',
            'od_o' => '虚飾性',
            'od_p' => '運転マナー',
        ];
        return Validator::make($line, $rule, [], $attributes);
    }

    /**
     * CSVヘッダ名をテーブルカラム名に変換する
     *
     * @param  string $csv_header
     * @return array
     */
    private static function csvHeaderTableColumn(string $csv_header)
    {
        return (self::getCsvConversionTable()[$csv_header] ?? throw new Exception());
    }

    /**
     * CSVヘッダ名とテーブルカラム名の変換表を返す
     *
     * @return array
     */
    private static function getCsvConversionTable()
    {
        return [
            '実施日' => 'date',
            '整理番号' => 'student_no',
            '氏名' => 'name',
            '性別' => 'gender',
            '年齢' => 'age',
            '性格パターン１' => 'od_persty_pattern_1',
            '性格パターン２' => 'od_persty_pattern_2',
            '運転適性度' => 'od_drv_aptitude',
            '安全運転度' => 'od_safe_aptitude',
            '特異反応' => 'od_specific_rxn',
            '注意力' => 'od_a',
            '判断力' => 'od_b',
            '柔軟性' => 'od_c',
            '決断力' => 'od_d',
            '緻密性' => 'od_e',
            '動作の安定性' => 'od_f',
            '適応性' => 'od_g',
            '身体的健康度' => 'od_h',
            '精神的健康度' => 'od_i',
            '社会的成熟度' => 'od_j',
            '情緒不安定性' => 'od_k',
            '衝迫性・暴発性' => 'od_l',
            '自己中心性' => 'od_m',
            '神経質・過敏性' => 'od_n',
            '虚飾性' => 'od_o',
            '運転マナー' => 'od_p',
            '処理区分' => 'processing_category'
        ];
    }
}
