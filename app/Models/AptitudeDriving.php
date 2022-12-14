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
use Rabianr\Validation\Japanese\Rules\Hiragana;
use Rabianr\Validation\Japanese\Rules\Kanji;

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

    public function schoolStaffs()
    {
        return $this->belongsTo(SchoolStaff::class, 'created_user_id', 'id');
    }

    public static function readCsv($files)
    {
        $responses = [];
        $user = Auth::user();
        foreach ($files as $key => $line) {
            mb_convert_variables(Encoding::UTF8, Encoding::SHIFT_JIS, $line);
            //???????????????????????????????????????
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
                //?????????????????????????????????????????????????????????????????????
                $validators = static::csvValidator($attributes);
                if ($validators->fails()) {
                    $errors = $validators->errors();
                    $errorDate = $errors->first('date');
                    $failedField = join('???', $errors->all());
                    $msgError[] = Lang::get('messages.MSE00008', ['item' => $failedField]);
                }
                // ????????????????????????????????????????????????????????????
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
                //????????????????????????ON?????????????????????????????????
                //????????????????????????OFF???????????????????????????????????????
                if ($attributes['disabled'] == "false") {
                    unset($attributes['error']);
                    unset($attributes['disabled']);
                    $attributes['date'] = date('Ymd', strtotime($attributes['date']));
                    //????????????????????????
                    $validators = static::csvValidator($attributes);
                    $checkIdLedger = Ledger::where('student_no', $attributes['student_no'])->where('status', Status::ENABLED)->first();
                    if ($validators->fails()) {
                        $failedField = join('???',  $validators->errors()->all());
                        $dataTemp['error'] = Lang::get('messages.MSE00008', ['item' => $failedField]);
                    } else if ($checkIdLedger) {
                        //???????????????????????????????????????
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
                        //???????????????????????????????????????
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

    public static function handleSave(array $aptitudeDrvs, AptitudeDriving $model = null)
    {
        try {
            $model = $model ?: new static();
            DB::transaction(function () use ($aptitudeDrvs, $model) {
                $userId = Auth::id();
                $aptitudeDrvs['created_user_id'] = $userId;
                $aptitudeDrvs['updated_user_id'] = $userId;
                $model->fill($aptitudeDrvs);
                $model->save();
            });
        } catch (Exception $e) {
            throw $e;
        }
    }

    public static function handleDelete(AptitudeDriving $model)
    {
        try {
            DB::transaction(function () use ($model) {
                $userId = Auth::id();
                $model->status = Status::DISABLED;
                $model->deleted_at = now();
                $model->deleted_user_id = $userId;
                $model->save();
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
     * CSV?????????????????????????????????????????????????????????????????????
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
            'name' => ['nullable',new Hiragana([' ', new Kanji('', true)])],
            'od_persty_pattern_1' => 'required|regex:/^[0-9]+$/|max:2',
            'od_persty_pattern_2' => 'required|regex:/^[0-9]+$/|max:2',
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

        // ??????????????????CSV??????????????????
        $messages = [
            'date' => '1',
            'student_no' => '2',
            'name' => '3',
            'od_persty_pattern_1' => '6',
            'od_persty_pattern_2' => '7',
            'od_drv_aptitude' => '8',
            'od_safe_aptitude' => '9',
            'od_specific_rxn' => '10',
            'od_a' => '11',
            'od_b' => '12',
            'od_c' => '13',
            'od_d' => '14',
            'od_e' => '15',
            'od_f' => '16',
            'od_g' => '17',
            'od_h' => '18',
            'od_i' => '19',
            'od_j' => '20',
            'od_k' => '21',
            'od_l' => '22',
            'od_m' => '23',
            'od_n' => '24',
            'od_o' => '25',
            'od_p' => '26',
        ];
        return Validator::make($line, $rule, $messages, []);
    }

    /**
     * CSV??????????????????????????????????????????????????????
     *
     * @param  string $csv_header
     * @return array
     */
    private static function csvHeaderTableColumn(string $csv_header)
    {
        return (self::getCsvConversionTable()[$csv_header] ?? throw new Exception());
    }

    /**
     * CSV????????????????????????????????????????????????????????????
     *
     * @return array
     */
    private static function getCsvConversionTable()
    {
        return [
            '?????????' => 'date',
            '????????????' => 'student_no',
            '??????' => 'name',
            '??????' => 'gender',
            '??????' => 'age',
            '?????????????????????' => 'od_persty_pattern_1',
            '?????????????????????' => 'od_persty_pattern_2',
            '???????????????' => 'od_drv_aptitude',
            '???????????????' => 'od_safe_aptitude',
            '????????????' => 'od_specific_rxn',
            '?????????' => 'od_a',
            '?????????' => 'od_b',
            '?????????' => 'od_c',
            '?????????' => 'od_d',
            '?????????' => 'od_e',
            '??????????????????' => 'od_f',
            '?????????' => 'od_g',
            '??????????????????' => 'od_h',
            '??????????????????' => 'od_i',
            '??????????????????' => 'od_j',
            '??????????????????' => 'od_k',
            '?????????????????????' => 'od_l',
            '???????????????' => 'od_m',
            '?????????????????????' => 'od_n',
            '?????????' => 'od_o',
            '???????????????' => 'od_p',
            '????????????' => 'processing_category'
        ];
    }
}
