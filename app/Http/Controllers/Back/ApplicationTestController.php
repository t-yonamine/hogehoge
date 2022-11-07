<?php

namespace App\Http\Controllers\Back;

use App\Enums\ConfirmationRecsStatus;
use App\Enums\Degree;
use App\Enums\LaType;
use App\Enums\LessonAttendOption;
use App\Enums\LessonAttendStatus;
use App\Enums\LicenseType;
use App\Enums\ResultType;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdmCheckItem;
use App\Models\ConfirmationRecord;
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

class ApplicationTestController extends Controller
{
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

        $user = Auth()->user();
        $role = $user->schoolStaff->role;

        $systemValue = SystemValue::get();
        $svValue = 0;
        foreach ($systemValue as $item) {
            if ($item->sv_key == 'test_num_of_days_max') {
                $svValue = $item->sv_value;
            }
        }

        $testNumberOfDaysMax = [];
        for ($i = 1; $i <= $svValue; $i++) {
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
        $checkLaType = true;
        if (!$request->la_type) {
            $checkLaType = 0;
            $laType = LaType::COMPLTST;
        } else if ($request->la_type == LaType::PL_TEST) {
            $checkLaType = 1;
            $laType = LaType::PL_TEST;
        } else {
            $checkLaType = 2;
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

        foreach ($data as $item) {
            $item->school_staff;
        }

        return  view('back.apply-test.index', ['data' => $data, 'numberMax' => $testNumberOfDaysMax, 'laType' => $laType, 'checkLaType' => $checkLaType, 'titleType' => $titleType, 'dataOptionPeriod' => $dataOptionPeriod, 'dataResultType' => $dataResultType, 'role' => $role]);
    }

    public function post(Request $request, $id)
    {
        $user = Auth::user();
        $schoolId =  $request->session()->get('school_id');

        if (!$schoolId) {
            return abort(403);
        }

        $schoolStaff = $user->schoolStaff;
        if (!$schoolId) {
            return abort(403);
        }

        $dataTest = Tests::where('school_id', $schoolId)->first();
        $lessonAttends = LessonAttend::find($id);

        if (!$lessonAttends) {
            return abort(404);
        }

        $data = LessonAttend::where('test_id', $dataTest->id)
            ->where('la_type', $lessonAttends->la_type)
            ->where('status', '>=', LessonAttendStatus::PENDING())
            ->get();

        $firstElement = 1;

        switch ($request->input('action')) {
            case LessonAttendOption::OPEN_MODAL:
                break;
            case LessonAttendOption::LICENSE_CONFIRM:
                // 仮免許確定処理
                try {
                    $request->validate(
                        [
                            'question_num' => 'nullable|max:10',
                            'lang' => 'nullable|max:20',
                            'score' => 'nullable|regex:/^[0-9]+$/|max:3',
                            'result' => 'nullable|max:1' . new EnumValue(ResultType::class, false),
                            'status' => 'nullable|max:1',
                        ],
                        [
                            'question_num' => __('messages.MSE00004', ['label' => '問題番号	']),
                            'lang' => __('messages.MSE00004', ['label' => '問題言語	']),
                            'score' => __('messages.MSE00004', ['label' => '得点']),
                            'result' => __('messages.MSE00004', ['label' => '合否']),
                        ]
                    );
                    $confirmRecord = ConfirmationRecord::where('conf_target', 'glesson_attends')->where('target_id', $id)->first();

                    if (!$confirmRecord) {
                        return abort(404);
                    }

                    if ($user->schoolStaff->role & SchoolStaffRole::ADMINISTRATOR != 0) {
                        // 5. 受講データの更新 /
                        $lessonAttends->question_num = $request->question_num;
                        $lessonAttends->lang = $request->lang;
                        $lessonAttends->score = (int)$request->score;
                        if ($request->result != null) {
                            $lessonAttends->result = (int)$request->result;
                            $lessonAttends->status = LessonAttendStatus::APPROVED;

                            //6. 受講(glesson_attends)の結果(result)を null→null以外 に変更したとき、結果承認の確認記録を更新する。
                            $confirmRecord->staff_id = $schoolStaff->id;
                            $confirmRecord->staff_name = $schoolStaff->name;
                            $confirmRecord->confirm_date = now();
                            $confirmRecord->confirm_time = date('H:i:s');
                            $confirmRecord->updated_at = now();
                            $confirmRecord->status = ConfirmationRecsStatus::CONFIRMED;
                            $confirmRecord->updated_user_id = $schoolStaff->id;
                            $confirmRecord->save();
                        }
                        // Update Confirmation Recs
                        $lessonAttends->save();
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

                $listLessonAttends = LessonAttend::where('test_num', '>', $lessonAttends->test_num)
                    ->where('la_type', $lessonAttends->la_type)
                    ->where('test_id', $dataTest->id)
                    ->get();
                foreach ($listLessonAttends as $item) {
                    $item->test_num = $item->test_num - 1;
                    $item->updated_at = now();
                    $item->updated_user_id = $user->id;
                    $item->save();
                }

                $confirmRecord->deleted_at = now();
                $confirmRecord->deleted_user_id = $user->id;

                $lessonAttends->test_num = 0;
                $lessonAttends->deleted_at = now();
                $lessonAttends->deleted_user_id = $user->id;

                $confirmRecord->save();
                $lessonAttends->save();
                return back();
                break;
            case LessonAttendOption::TOP_BUTTON:
                // 先頭へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                if ($lessonAttends->test_num === $firstElement) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', '<', $lessonAttends->test_num)
                        ->where('la_type', $lessonAttends->la_type)
                        ->where('test_id', $dataTest->id)
                        ->get();

                    LessonAttend::rearrangeList($listLessonAttends, $user->id, true);
                    LessonAttend::rearrangePerRecord($lessonAttends, $user->id, now(), ($firstElement - $lessonAttends->test_num));
                }
                break;
            case LessonAttendOption::UP_BUTTON:
                // 上へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                if ($lessonAttends->test_num === 1) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', $lessonAttends->test_num - $firstElement)
                        ->where('la_type', $lessonAttends->la_type)
                        ->where('test_id', $dataTest->id)
                        ->get();
                    LessonAttend::rearrangeList($listLessonAttends, $user->id, true);
                    LessonAttend::rearrangePerRecord($lessonAttends, $user->id, now(), (-$firstElement));
                }
                break;
            case LessonAttendOption::DOWN_BUTTON:
                // 下へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                $findMaxTestNum = LessonAttend::where('test_id', $dataTest->id)
                    ->where('la_type', $lessonAttends->la_type)->max('test_num');
                if ($lessonAttends->test_num == $findMaxTestNum) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', $lessonAttends->test_num + $firstElement)
                        ->where('la_type', $lessonAttends->la_type)
                        ->where('test_id', $dataTest->id)
                        ->get();

                    LessonAttend::rearrangeList($listLessonAttends, $user->id, false);
                    LessonAttend::rearrangePerRecord($lessonAttends, $user->id, now(), ($firstElement));
                }
                break;
            case LessonAttendOption::END_BUTTON:
                // 末尾へボタン処理
                if (count($data) > 0) {
                    return abort(412, 'Error. Precondition Failed.');
                }
                $findMaxTestNum = LessonAttend::where('test_id', $dataTest->id)
                    ->where('la_type', $lessonAttends->la_type)->max('test_num');
                if ($lessonAttends->test_num == $findMaxTestNum) {
                    return back();
                } else {
                    $listLessonAttends = LessonAttend::where('test_num', '>', $lessonAttends->test_num)
                        ->where('la_type', $lessonAttends->la_type)
                        ->where('test_id', $dataTest->id)
                        ->get();
                    LessonAttend::rearrangeList($listLessonAttends, $user->id, false);
                    LessonAttend::rearrangePerRecord($lessonAttends, $user->id, now(), ($findMaxTestNum - $firstElement));
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
    public function errorPage(Request $request) {
        return abort($request->status_code);
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
