<?php

namespace App\Http\Controllers\Back;

use App\Enums\ConfgInformationType;
use App\Enums\ConfirmationRecsStatus;
use App\Enums\Degree;
use App\Enums\LaType;
use App\Enums\LessonAttendOption;
use App\Enums\LessonAttendStatus;
use App\Enums\LessonCode;
use App\Enums\LicenseType;
use App\Enums\ResultType;
use App\Enums\SchoolStaffRole;
use App\Enums\StageType;
use App\Enums\Status;
use App\Enums\TestType;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\AdmCheckItem;
use App\Models\ConfirmationRecord;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\Period;
use App\Models\SchoolPeriodM;
use App\Models\SchoolStaff;
use App\Models\Tests;
use App\Models\SystemValue;
use BenSampo\Enum\Rules\EnumValue;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rules\Enum;

class ApplicationTestController extends Controller
{
    const INIT_BLOCK = 0;
    const BLOCK_B = 1;
    const BLOCK_C = 2;
    const RESTRICTION_RELEASED = 1;
    const LICENSE_NORMAL = 0;
    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            if ($request->routeIs('apply-test.examiner-allocation-regis.ajax', 'apply-test.examiner-allocation-regis.ajax-save')) {
                // 1.入力パラメータ
                $lessonAttendId = $request->lesson_attend_id;
                $sesSchoolId = $request->session()->get('school_id');
                $sesSchoolStaffId = $request->session()->get('school_staff_id');

                // 3. 存在チェック
                // A. 受講IDの存在チェック。 共通ロジック/存在チェック#5
                $lessonAttend = LessonAttend::with(['school'])->where('id', $lessonAttendId)->where('school_id', $sesSchoolId)->first();
                if (!$lessonAttend) {
                    abort(404, '404 Error. Not Found');
                }
                //  B. ログイン職員の存在チェック。 共通ロジック/存在チェック#1 セッションの職員IDの情報を読む。
                $currentStaffLogin = SchoolStaff::where('id', $sesSchoolStaffId)->first();
                if (!$currentStaffLogin) {
                    abort(404, '404 Error. Not Found');
                }

                //4. 権限チェック
                // 受講データの教習所IDがsession/school_idと同じであること。
                if (
                    $request->session()->get('school_id') != $lessonAttend->school->id
                    || (($currentStaffLogin->role & (SchoolStaffRole::ADMINISTRATOR + SchoolStaffRole::SUB_ADMINISTRATOR)) == 0)
                ) {
                    abort(403, '403 Error. Forbidden.');
                }

                // 5. 前提条件チェック
                // A. 受講IDの受講(glesson_attends)の状態(status)が、全て 2:実施待 の前の状態か確認する。
                if ($lessonAttend->status->value >= LessonAttendStatus::PENDING()->value) {
                    abort(412, 'Error. Precondition Failed.');
                }
                $request['lessonAttend'] = $lessonAttend;
            }
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * @Route('/', method: 'GET', name: 'apply-test.index')
     */
    public function index(Request $request)
    {
        $schoolId =  $request->session()->get('school_id');

        if (!$schoolId) {
            return abort(403);
        }

        if ($request->has('action') && $request->action == 'create') {
            return redirect()->route('apply-test.create', $request->query->all());
        }

        $user = Auth()->user();
        $role = $user->schoolStaff->role;

        $systemValue = SystemValue::where('sv_key', 'test_num_of_days_max')->first();

        $testNumberOfDaysMax = [];
        for ($i = 1; $i <= (int)$systemValue?->sv_value; $i++) {
            array_push($testNumberOfDaysMax, $i);
        }

        $dataTest = Tests::where('school_id', $schoolId)
            ->where('test_date', $request->test_date)
            ->where('num_of_days', $request->num_of_days)
            ->first();

        $data = LessonAttend::select('glesson_attends.*', 'gadm_check_items.student_no', 'gadm_check_items.name_kana', 'gadm_check_items.target_license_names')->join('gadm_check_items', 'glesson_attends.ledger_id', '=', 'gadm_check_items.ledger_id')
            ->where('gadm_check_items.status', Status::ENABLED())
            ->where('glesson_attends.test_id', $dataTest?->id)
            ->where('glesson_attends.la_type', $request->la_type)
            ->orderBy('glesson_attends.test_num', 'ASC');

        $titleType = '';
        $checkLaType = self::INIT_BLOCK;
        if (!$request->la_type) {
            $laType = LaType::COMPLTST;
        } else if ($request->la_type == LaType::PL_TEST) {
            $checkLaType = self::BLOCK_B;
            $laType = LaType::PL_TEST;
        } else {
            $checkLaType = self::BLOCK_C;
            $laType = $request->la_type;
            $titleType = LaType::getDescription((int)$laType);
        }
        if (
            $laType == LaType::COMPLTST ||
            $laType == LaType::GRADTST ||
            $laType == LaType::DRVSKLTST
        ) {
            $data = $data->with(['schoolStaff']);
        }

        // Data ResultType
        $dataResultType = [ResultType::NG, ResultType::OK, ResultType::CANCEL];

        // Get data option period
        $dataOptionPeriod = SchoolPeriodM::where('school_id', $schoolId)->get();

        $data = $data->get();

        return  view('back.apply-test.index', ['data' => $data, 'numberMax' => $testNumberOfDaysMax, 'laType' => $laType, 'checkLaType' => $checkLaType, 'titleType' => $titleType, 'dataOptionPeriod' => $dataOptionPeriod, 'dataResultType' => $dataResultType, 'role' => $role]);
    }

    /**
     * @Route('/', method: 'POST', name: 'apply-test.index')
     */
    public function post(Request $request, $id)
    {
        $user = Auth::user();
        $schoolId =  $request->session()->get('school_id');

        if (!$schoolId) {
            return abort(403);
        }

        $schoolStaff = $user->schoolStaff;

        $lessonAttend = LessonAttend::find($id);

        if (!$lessonAttend) {
            return abort(404);
        }

        // 4.権限チェック
        //  A. 受講データの教習所IDがsession/school_idと同じであること。

        if ($lessonAttend->school_id != $schoolId) {
            return abort(403);
        }

        $data = LessonAttend::where('test_id', $lessonAttend->test_id)
            ->where('la_type', $lessonAttend->la_type)
            ->where('status', '>=', LessonAttendStatus::PENDING())
            ->get();

        $firstElement = 1;

        switch ($request->input('action')) {
            case LessonAttendOption::LICENSE_CONFIRM:
                // 仮免許確定処理
                $key = (int)$request->key;
                try {
                    $request->validate(
                        [
                            'question_num_' . $key => 'nullable|max:10',
                            'lang_' . $key => 'nullable|max:20',
                            'score_' . $key => 'nullable|regex:/^[0-9]+$/|max:3',
                            'result' => 'nullable|max:1' . new EnumValue(ResultType::class, false),
                        ],
                        [
                            'question_num_' . $key => __('messages.MSE00004', ['label' => '問題番号	']),
                            'lang_' . $key => __('messages.MSE00004', ['label' => '問題言語	']),
                            'score_' . $key => __('messages.MSE00004', ['label' => '得点']),
                            'result' => __('messages.MSE00004', ['label' => '合否']),
                        ]
                    );
                    $confirmRecord = ConfirmationRecord::where('conf_target', 'glesson_attends')->where('target_id', $id)->first();

                    if (!$confirmRecord) {
                        return abort(404);
                    }

                    if ($user->schoolStaff->role & SchoolStaffRole::ADMINISTRATOR != 0) {
                        // 5. 受講データの更新 /
                        $lessonAttend->question_num = $request['question_num_' . $key];
                        $lessonAttend->lang = $request['lang_' . $key];
                        $lessonAttend->score = $request['score_' . $key];
                        if ($request->result != null) {
                            $lessonAttend->result = (int)$request->result;
                            $lessonAttend->status = LessonAttendStatus::APPROVED;

                            //6. 受講(glesson_attends)の結果(result)を null→null以外 に変更したとき、結果承認の確認記録を更新する。
                            $confirmRecord->staff_id = $schoolStaff->id;
                            $confirmRecord->staff_name = $schoolStaff->name;
                            $confirmRecord->confirm_date = now();
                            $confirmRecord->confirm_time = date('H:i:s');
                            $confirmRecord->updated_at = now();
                            $confirmRecord->status = ConfirmationRecsStatus::CONFIRMED;
                            $confirmRecord->updated_user_id = $schoolStaff->id;
                            $confirmRecord->save();

                            // 7. 結果(result)が1:OK の場合、教習原簿(gledgers)の教習ステータス等も変更する。
                            $ledger = Ledger::where('id', $lessonAttend->ledger_id)->first();
                            if ($request->result == ResultType::OK) {
                                $ledger->lesson_sts = LessonCode::SECOND_STAGE;
                                $ledger->pl_test_date = $lessonAttend->period_date;
                                $ledger->updated_at = now();
                                $ledger->updated_user_id = $schoolStaff->id;
                                $ledger->save();
                            }
                        }
                        // Update Confirmation Recs
                        $lessonAttend->save();
                    }
                } catch (\Throwable $th) {
                    throw $th;
                }
                break;
            case LessonAttendOption::DELETE_APPPLICATION:
                // 検定申込削除処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                $confirmRecord = ConfirmationRecord::where('conf_target', 'glesson_attends')->where('target_id', $id)->first();
                if (!$confirmRecord) {
                    return abort(404);
                }

                $listLessonAttends = LessonAttend::where('test_num', '>', $lessonAttend->test_num)
                    ->where('la_type', $lessonAttend->la_type)
                    ->where('test_id', $lessonAttend->test_id)
                    ->get();
                foreach ($listLessonAttends as $item) {
                    $item->test_num = $item->test_num - 1;
                    $item->updated_at = now();
                    $item->updated_user_id = $user->id;
                    $item->save();
                }

                $confirmRecord->deleted_at = now();
                $confirmRecord->deleted_user_id = $user->id;

                $lessonAttend->test_num = 0;
                $lessonAttend->deleted_at = now();
                $lessonAttend->deleted_user_id = $user->id;

                $confirmRecord->save();
                $lessonAttend->save();
                return back();
                break;
            case LessonAttendOption::TOP_BUTTON:
                // 先頭へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                if ($lessonAttend->test_num === $firstElement) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', '<', $lessonAttend->test_num)
                        ->where('la_type', $lessonAttend->la_type)
                        ->where('test_id', $lessonAttend->test_id)
                        ->get();

                    LessonAttend::rearrangeList($listLessonAttends, $user->id, true);
                    LessonAttend::rearrangePerRecord($lessonAttend, $user->id, now(), ($firstElement - $lessonAttend->test_num));
                }
                break;
            case LessonAttendOption::UP_BUTTON:
                // 上へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                if ($lessonAttend->test_num === 1) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', $lessonAttend->test_num - $firstElement)
                        ->where('la_type', $lessonAttend->la_type)
                        ->where('test_id', $lessonAttend->test_id)
                        ->get();
                    LessonAttend::rearrangeList($listLessonAttends, $user->id, true);
                    LessonAttend::rearrangePerRecord($lessonAttend, $user->id, now(), (-$firstElement));
                }
                break;
            case LessonAttendOption::DOWN_BUTTON:
                // 下へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                $findMaxTestNum = LessonAttend::where('test_id', $lessonAttend->test_id)
                    ->where('la_type', $lessonAttend->la_type)->max('test_num');
                if ($lessonAttend->test_num == $findMaxTestNum) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', $lessonAttend->test_num + $firstElement)
                        ->where('la_type', $lessonAttend->la_type)
                        ->where('test_id', $lessonAttend->test_id)
                        ->get();

                    LessonAttend::rearrangeList($listLessonAttends, $user->id, false);
                    LessonAttend::rearrangePerRecord($lessonAttend, $user->id, now(), ($firstElement));
                }
                break;
            case LessonAttendOption::END_BUTTON:
                // 末尾へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                $findMaxTestNum = LessonAttend::where('test_id', $lessonAttend->test_id)
                    ->where('la_type', $lessonAttend->la_type)->max('test_num');
                if ($lessonAttend->test_num == $findMaxTestNum) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', '>', $lessonAttend->test_num)
                        ->where('la_type', $lessonAttend->la_type)
                        ->where('test_id', $lessonAttend->test_id)
                        ->get();
                    LessonAttend::rearrangeList($listLessonAttends, $user->id, false);
                    LessonAttend::rearrangePerRecord($lessonAttend, $user->id, now(), ($findMaxTestNum - $firstElement));
                }
                break;
        }
        return back();
    }

    /**
     * @Route('/apply-test/examiner-allocation-regis/ajax', method: 'GET', name: 'apply-test.completion-test')
     */
    public function examinerAllocationRegisAjax(Request $request)
    {
        $lessonAttend = $request->lessonAttend;
        // 6. 対象受講の入所時確認項目(gadm_check_items)を読んでおく。
        $admCheckItem = AdmCheckItem::with('licenseType')->where('ledger_id', $lessonAttend->ledger_id)->where('status', Status::ENABLED())->first();
        if (!$admCheckItem) {
            abort(404, '404 Error. Not Found');
        }

        // 7. 検定資格を持つ指導員をリストアップする。
        // 下表にない免許種別CDではXX資格は確認しない。
        $models = SchoolStaff::where('school_id', $lessonAttend->school_id)->where('role', '&', SchoolStaffRole::EXAMINER)
            ->tap(function ($query) use ($admCheckItem) {
                return $this->setLicCode($query, $admCheckItem->licenseType);
            })->orderBy('school_staff_no')->get();

        $existSchoolStaff = Period::where('test_id', $lessonAttend->test_id)->where('school_staff_id', $lessonAttend->school_staff_id)->get()->pluck('school_staff_id');

        return response()->json(['status' => 200, 'data' => $models, 'exist_school_staff_id' => $existSchoolStaff]);
    }

    /**
     * @Route('/apply-test/examiner-allocation-regis/ajax-save', method: 'POST', name: 'apply-test.completion-save')
     */
    public function examinerAllocationRegisAjaxSave(Request $request)
    {
        try {
            DB::transaction(function () use ($request) {
                $lessonAttend = $request->lessonAttend;
                $schoolStaffSelected = $request->id_selected;
                $sesSchoolStaffId = $request->session()->get('school_staff_id');
                // 6. 対象の検定(gtests)、教習所別次元マスタ(gschool_period_m) を読む。
                $modelTest = Tests::where('id', $lessonAttend->test_id)->first();
                if (!$modelTest) {
                    abort(404);
                }
                // B. 教習所別次元マスタ(gschool_period_m)を読む。複数行ある場合あり。
                $schoolPeriodMs = SchoolPeriodM::where('school_id', $lessonAttend->school_id)->where('period_num', '>=', $modelTest->period_num_from)->where('period_num', '<=', $modelTest->period_num_to)->get();
                if (!$schoolPeriodMs) {
                    abort(404);
                }

                //8. 変更前の指導員が検定を担当する教習生が0人になった場合、指導員の時限(gperiods)を削除する。.
                //A. 変更前の指導員の検定担当の受講(glesson_attends)が0件か確認する。
                $numOfAttendances = LessonAttend::where('test_id', $modelTest->id)->where('school_staff_id', $lessonAttend->school_staff_id)->count();

                // B. 0件の場合、時限(gperiods)を削除する。
                if ($numOfAttendances == 0) {
                    Period::where('test_id', $modelTest->id)->where('school_staff_id',  $lessonAttend->school_staff_id)->delete();
                }

                // 7. 対象受講(glesson_attends)の職員ID(school_staff_id)を変更する。
                $lessonAttend->school_staff_id = $schoolStaffSelected;
                $lessonAttend->updated_at = now();
                $lessonAttend->updated_user_id = $sesSchoolStaffId;
                $lessonAttend->save();

                // 9. 変更後の指導員の時限(gperiods)が登録されていなければ追加する。
                //   A. 変更後の指導員の検定の時限が登録されているか確認する。
                $numOfperiods = Period::where('test_id', $modelTest->id)->where('school_staff_id', $lessonAttend->school_staff_id)->count();
                // B. 0件の場合、時限を追加する。時限のfrom-toの数だけ追加する。
                if ($numOfperiods == 0) {
                    Period::handleInsert($modelTest, $sesSchoolStaffId, $lessonAttend, $schoolPeriodMs);
                }
                return response()->json(['status' => 200]);
            });
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @Route('apply-test/error-page', method: 'GET', name: 'apply-test.error-page')
     */
    public function errorPage(Request $request)
    {
        return abort($request->status_code);
    }

    /**
     * @Route('/apply-test/create', method: 'GET', name: 'apply-test.create')
     */
    public function create(Request $request)
    {
        $request->validate(
            [
                'la_type' => 'required|', new Enum(LaType::class),
                'test_date' => 'required',
                'num_of_days' => 'required',
                'period_num_from' => 'required',
                'period_num_to' => 'required',

            ],
            [],
            [
                'la_type' => '検定種別',
                'test_date' => '検定日',
                'num_of_days' => '実施回',
                'period_num_from' => '実施時限From',
                'period_num_to' => '実施時限To',
            ]
        );
        try {
            $schoolId =  $request->session()->get('school_id');
            $laType = (int)$request->query('la_type');
            $numFrom = $request->query('period_num_from');
            $numTo = $request->query('period_num_to');
            $periods = SchoolPeriodM::where('school_id', $schoolId)
                ->where(function ($query) use ($numFrom, $numTo) {
                    $query->where('period_num', $numFrom)
                        ->orWhere('period_num', $numTo);
                })->get();

            $ischeckType = LaType::getDescription($laType);

            switch ($laType) {
                case LaType::COMPLTST:
                    $list_student = $this->checkPeriodStudent($laType, $schoolId, LessonCode::FIRST_TEST_WAIT());
                    break;
                case LaType::PL_TEST:
                    $list_student = $this->checkPeriodStudent($laType, $schoolId, LessonCode::PL_TESTWAIT());
                    break;
                case LaType::GRADTST:
                    $list_student = $this->checkPeriodStudent($laType, $schoolId, LessonCode::TEST_2_WAIT());
                    break;
                case LaType::DRVSKLTST:
                    $list_student = $this->checkPeriodStudent($laType, $schoolId, LessonCode::TEST_2_WAIT());
                    break;
            }

            $data = [
                'la_type' => $ischeckType ?  $ischeckType : '',
                'test_date' =>  $request->query('test_date'),
                'num_of_days' => $request->query('num_of_days'),
                'period_num_to' =>  $numTo,
                'period_name_to' =>  $periods->filter(function ($value) use ($numTo) {
                    return $value->period_num == $numTo;
                })->first()->period_name,
                'period_num_from' =>   $numFrom,
                'period_name_from' =>   $periods->filter(function ($value) use ($numFrom) {
                    return $value->period_num == $numFrom;
                })->first()->period_name,
                'list_student' => $list_student ? $list_student : [],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
        return view('back.apply-test.create', ['data' =>  (object)$data]);
    }
    /**
     * @Route('/apply-test/create, method: 'POST', name: 'apply-test.create-save')
     */
    public function createSave(Request $request)
    {
        $request->validate(
            [
                'la_type' => 'required',
                'test_date' => 'required',
                'num_of_days' => 'required',
                'period_num_from' => 'required',
                'period_num_to' => 'required',
                'ledger_id' => 'required',

            ],
            [],
            [
                'la_type' => '検定種別',
                'test_date' => '検定日',
                'num_of_days' => '実施回',
                'period_num_from' => '実施時限From',
                'period_num_to' => '実施時限To',
                'ledger_id' => '教習原簿ID'
            ]
        );
        DB::transaction(function () use ($request) {
            // check section
            $schoolStaffId = $request->session()->get('school_staff_id');
            $schoolId = $request->session()->get('school_id');
            $schoolCd = $request->session()->get('school_cd');

            // A. 職員IDの存在チェック 共通ロジック/存在チェック#1  セッションの職員IDの情報を読む。
            if (empty($schoolStaffId) || empty($schoolId) || empty($schoolCd)) {
                return abort(404);
            }

            // check ledger #3
            $existLedge = Ledger::whereIn('id', $request->ledger_id)->get();
            if (!$existLedge) {
                return abort(404);
            }
            $laType = (int)$request->la_type;

            $testType = $laType == LaType::PL_TEST ? TestType::TEM_LICENSE : TestType::ORTHER_LICENSE;
            $modelTests = Tests::where('school_id', $schoolId)
                ->where('test_date', $request->test_date)
                ->where('num_of_days', $request->num_of_days)
                ->where('test_type', $testType)
                ->first();

            //4. 権限チェック
            $user = Auth::user();
            if ($user->schoolStaff->role & SchoolStaffRole::CLERK_ONE == 0) {
                abort(403);
            }

            // 5. 検定の追加・更新
            //  A. 検定の追加
            if (!$modelTests) {
                $dataTest['school_id'] = $schoolId;
                $dataTest['num_of_days'] = $request->num_of_days;
                $dataTest['test_type'] = $testType;
                $dataTest['period_num_from'] = $request->period_num_from;
                $dataTest['period_num_to'] = $request->period_num_to;
                $dataTest['created_at'] = now();
                $dataTest['created_user_id'] = $schoolStaffId;
                $dataTest['updated_at'] = now();
                $dataTest['updated_user_id'] = $schoolStaffId;
                $dataTest['test_date'] = $request->test_date;
                $modelTests = Tests::handleSave($dataTest, null);
            } else if (
                $modelTests->period_num_to != $request->period_num_to &&
                $modelTests->period_num_from !=  $request->period_num_to
            ) {
                //  B. 検定の更新
                $dataTest['period_num_from'] = $request->period_num_from;
                $dataTest['period_num_to'] = $request->period_num_to;
                $dataTest['updated_user_id'] = $schoolStaffId;
                $dataTest['updated_at'] = now();
                $modelTests = Tests::handleSave($dataTest, $modelTests);
            }
            // 6. 検定、検定種別毎の最大受験番号を求める。
            $testNumMax = LessonAttend::where('test_id', $modelTests->id)->where('la_type', $laType)->max('test_num');

            // 7. 教習原簿単位で、受講、確認記録の追加・更新
            $confTarget = "glesson_attends";
            foreach ($existLedge as $ledger) {
                //4. 権限チェック
                // B. 選択した教習原簿の教習所が、ログイン者の教習所と同じかチェックする。
                if ($ledger->school_id != $schoolId) {
                    return abort(403);
                }

                $existAttend = LessonAttend::where('ledger_id', $ledger->id)->first();
                if (!$existAttend) {
                    // 7 a. 対象検定IDの受講データが無い場合は、追加する。
                    $dataLessonAttend['school_id'] = $schoolId;
                    $dataLessonAttend['ledger_id'] = $ledger->id;
                    $dataLessonAttend['la_type'] = $testType;
                    $dataLessonAttend['stage'] = StageType::checkType($laType);
                    $dataLessonAttend['period_date'] = now();
                    $dataLessonAttend['period_from'] = $request->period_num_from;
                    $dataLessonAttend['period_to'] = $request->period_num_to;
                    $dataLessonAttend['test_id'] = $modelTests->id;
                    $dataLessonAttend['test_num'] = $testNumMax ? $testNumMax + 1 : 0;
                    $dataLessonAttend['status'] = LessonAttendStatus::SCHEDULED_WAITING();
                    $dataLessonAttend['created_at'] = now();
                    $dataLessonAttend['updated_at'] = now();
                    $dataLessonAttend['created_user_id'] = $schoolStaffId;
                    $dataLessonAttend['updated_user_id'] = $schoolStaffId;
                    $existAttend = LessonAttend::handleSave($dataLessonAttend, $ledger);
                } else {
                    //7 b. 受講がある場合は、状態を変更する。
                    $dataLessonAttend['status'] = LessonAttendStatus::SCHEDULED_WAITING();
                    $dataLessonAttend['updated_at'] = now();
                    $dataLessonAttend['updated_user_id'] = $schoolStaffId;
                    LessonAttend::handleSave($dataLessonAttend, $ledger, $existAttend);
                }
                $configInformation = ConfirmationRecord::where('target_id', $existAttend->id)
                    ->where('conf_target', $confTarget)->first();
                if (!$configInformation) {
                    $dataConfigInformation['ledger_id'] = $ledger->id;
                    $dataConfigInformation['school_id'] = $schoolId;
                    $dataConfigInformation['conf_target'] = $confTarget;
                    $dataConfigInformation['target_id'] =  $existAttend->id;
                    $dataConfigInformation['conf_type'] = ConfgInformationType::APPLY_APPRO();
                    $dataConfigInformation['conf_role'] = SchoolStaffRole::ADMINISTRATOR;
                    $dataConfigInformation['status'] = 1;
                    $dataConfigInformation['created_user_id'] = $schoolStaffId;
                    $dataConfigInformation['updated_at'] = now();
                    $dataConfigInformation['updated_user_id'] = $schoolStaffId;
                    ConfirmationRecord::handleSave($dataConfigInformation);
                }
            }
        });
        return redirect()->route('apply-test.index', $request->query->all());
    }

    private function checkPeriodStudent($type, $school_id, $lesson_sts)
    {

        $data = Ledger::with(['admCheckItem', 'lessonAttend'])
            ->whereHas('admCheckItem', function ($q) {
                $q->where('status', Status::ENABLED());
            })
            ->where('school_id', $school_id)
            ->where('status', Status::ENABLED())
            ->where('lesson_sts', $lesson_sts)
            ->whereDoesntHave('lessonAttend', function ($c) use ($type) {
                $c->where('la_type', $type)->whereIn('status', [LessonAttendStatus::SCHEDULED_WAITING(), LessonAttendStatus::PENDING()]);
            });
        switch ($type) {
            case LaType::COMPLTST:
            case LaType::PL_TEST:
                return $data->orderBy('student_no')->get();
            case LaType::GRADTST:
                return $data->where(DB::raw("(`target_license_cd` & " . self::RESTRICTION_RELEASED . ")"), '=', self::LICENSE_NORMAL)->orderBy('student_no')->get();
            case LaType::DRVSKLTST:
                return $data->where(DB::raw("(`target_license_cd` & " . self::RESTRICTION_RELEASED . ")"), '!=', self::LICENSE_NORMAL)->orderBy('student_no')->get();
            default:
                return null;
        }
    }
    private function setLicCode($query, $licenseType)
    {
        $allow = [
            LicenseType::SL_MVL,
            LicenseType::SL_MVL_L,
            LicenseType::L_MVL,
            LicenseType::L_MVL_L,
            LicenseType::M_MVL,
            LicenseType::M_MVL_L,
            LicenseType::SM_MVL,
            LicenseType::SM_MVL_L,
            LicenseType::S_MVL_MT,
            LicenseType::S_MVL_MT_L,
            LicenseType::S_MVL_AT,
            LicenseType::TOWING,
            LicenseType::TOWING_L,
            LicenseType::L_ML,
            LicenseType::L_ML_L,
            LicenseType::L_ML_AT,
            LicenseType::S_ML,
            LicenseType::S_ML_L,
            LicenseType::S_ML_AT,
            LicenseType::L_MVL_2,
            LicenseType::M_MVL_2,
            LicenseType::S_MVL_2
        ];
        if (!in_array($licenseType->license_cd, $allow)) {
            return $query;
        }

        return $query->where($this->getQualificationCheckField(intval($licenseType->license_cd)), '&', Degree::CERTIFICATION);
    }

    private function getQualificationCheckField($licenseCode)
    {
        return match ($licenseCode) {
            LicenseType::SL_MVL, LicenseType::SL_MVL_L => "lic_sl_mvl",
            LicenseType::L_MVL, LicenseType::L_MVL_L => "lic_l_mvl",
            LicenseType::M_MVL, LicenseType::M_MVL_L, => "lic_m_mvl",
            LicenseType::SM_MVL, LicenseType::SM_MVL_L => "lic_sm_mvl",
            LicenseType::S_MVL_MT, LicenseType::S_MVL_MT_L, LicenseType::S_MVL_AT => "lic_s_mvl",
            LicenseType::TOWING, LicenseType::TOWING_L => "lic_towing",
            LicenseType::L_ML, LicenseType::L_ML_L, LicenseType::L_ML_AT => "lic_l_ml",
            LicenseType::S_ML, LicenseType::S_ML_L, LicenseType::S_ML_AT => "lic_s_ml",
            LicenseType::L_MVL_2 => "lic_l_mvl_2",
            LicenseType::M_MVL_2 => "lic_m_mvl_2",
            LicenseType::S_MVL_2 => "lic_s_mvl_2l"
        };
    }
}
