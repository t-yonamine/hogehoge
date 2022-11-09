<?php

namespace App\Http\Controllers\Back;

use App\Enums\ConfgInformationType;
use App\Enums\LaType;
use App\Enums\LessonAttendStatus;
use App\Enums\LessonCode;
use App\Enums\SchoolStaffRole;
use App\Enums\StageType;
use App\Enums\Status;
use App\Enums\TestType;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolStaff\SchoolStaffRequest;
use App\Models\ConfirmationRecord;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\SchoolPeriodM;
use App\Models\SchoolStaff;
use App\Models\Tests;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;

class SchoolStaffController extends Controller
{
    public function __construct()
    {
        //     ・権限チェック
        // ログインユーザーが選択教習所のシステム管理者の役割を持っていることを確認
        // システム管理者の役割を持ってない場合、403 Error. Forbden.
        // 役割のチェックはビット演算でチェックする
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            // ログインユーザー.role == 1:システム管理者
            if (!$user->school_id) {
                abort(403);
            }
            Helper::checkRole($user->schoolStaff->role);
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @Route('/', method: 'GET', name: 'school-staff.index')
     */
    public function index(Request $request)
    {

        $request->validate(
            [
                'school_staff_no' => 'nullable|regex:/^[a-zA-Z0-9]+$/'
            ],
            [
                'school_staff_no' => __('messages.MSE00004', ['label' => '職員番号'])
            ]
        );

        $school_id =  $request->session()->get('school_id');
        if (!$school_id) {
            return abort(403);
        }
        $user = Auth::user();
        Helper::checkRole($user->schoolStaff->role);

        $data = SchoolStaff::buildQuery($request->input())->where('school_id', $school_id)->where('status', Status::ENABLED())
            ->orderBy('school_staff_no', 'ASC')->paginate();

        return  view('back.school-staff.index', ['data' => $data]);
    }

    /**
     * @Route('/school-staff/{id}', method: 'DELETE', name: 'school-staff.delete')
     */
    public function delete($id)
    {
        $model = SchoolStaff::where('id', $id)->first();
        $authUser = Auth::user();
        $user = User::where('id', $id)->first();
        Helper::checkRole($authUser->schoolStaff->role);
        if (!$model) {
            return redirect()->route('school-staff.index')->with('error', Lang::get('messages.MSE00002'));
        } else {
            SchoolStaff::handleDelete($model, $user, $authUser);
        }
        return redirect()->route('school-staff.index')->with('success', Lang::get('messages.MSI00002'));
    }

    /**
     * @Route('/school-staff/{id}', method: 'GET', name: 'school-staff.show')
     */
    public function show($id)
    {
        $user = User::where('id', $id)->where('status', Status::ENABLED)->first();

        if (empty($user) || empty($user->schoolStaff)) {
            abort(404);
        }

        return view('back.school-staff.update', ['data' => $user->schoolStaff, 'user' => $user]);
    }

    /**
     * @Route('/school-staff/{id}', method: 'PUT', name: 'school-staff.update')
     */
    public function update(SchoolStaffRequest $request, $id)
    {
        try {
            $schoolStaff = SchoolStaff::where('id', $id)->first();
            $userById = User::where('id', $id)->first();
            if (!$schoolStaff) {
                return back()->withErrors('school_staff_no', Lang::get('messages.MSE00002'))->withInput($request->input());;
            }
            if (!$userById) {
                return back()->withErrors('login_id', Lang::get('messages.MSE00002'))->withInput($request->input());;
            }
            SchoolStaff::handleSave($request->input(), false, $userById, $schoolStaff);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('school-staff.show', [$id])->with('success', Lang::get('messages.MSI00002'));
    }

    /**
     * @Route('/school-staff/create', method: 'GET', name: 'school-staff.create')
     */
    public function create()
    {
        return view('back.school-staff.create', ['data' => new SchoolStaff(), 'user' => new User()]);
    }

    /**
     * @Route('/school-staff/create', method: 'POST', name: 'school-staff.store')
     */
    public function store(SchoolStaffRequest $request)
    {
        $user = Auth::user();
        // 選択教習所に同じログインIDが存在する場合はエラー
        $existsUser = User::where('school_id', $user->school_id)->where('login_id', $request->login_id)->first();
        if ($existsUser) {
            return back()->withErrors(['login_id' => __('messages.MSE00001', ['label' => 'ユーザー名'])])->withInput($request->input());
        }

        // 選択教習所に同じ職員番号が存在する場合はエラー
        $existsSchoolStaff = SchoolStaff::where('school_id', $user->school_id)->where('school_staff_no', $request->school_staff_no)->first();
        if ($existsSchoolStaff) {
            return back()->withErrors(['school_staff_no' => __('messages.MSE00001', ['label' => '職員番号'])])->withInput($request->input());
        }

        try {
            SchoolStaff::handleSave($request->input(), true);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('school-staff.index')->with(['success' => Lang::get('messages.MSI00004')]);
    }

    public function checkPeriodStudent($type, $school_id, $lesson_sts)
    {
        $data = [];
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
            case LaType::PLS_TEST:
                return $data->orderBy('student_no')->get();
            case LaType::GRASTST:
                return $data->where(DB::raw("(`target_license_cd` & 1)"), '=', 0)->orderBy('student_no')->get();
            case LaType::DRV_LESSON:
                return $data->where(DB::raw("(`target_license_cd` & 1)"), '!=', 0)->orderBy('student_no')->get();
            default:
                return null;
        }
    }

    /**
     * @Route('/school-staff/test', method: 'GET', name: 'school-staff.test-index')
     */
    public function studentTestIndex(Request $request)
    {
        try {
            $school_id =  $request->session()->get('school_id');
            $test_type = (int)$request->query('test_type');
            $period = SchoolPeriodM::where('school_id', $school_id)->where('period_num', $request->query('period_num'))->first();
            $ischeckType = LaType::getDescription($test_type);

            switch ($request->query('test_type')) {
                case LaType::COMPLTST:
                    $list_student = $this->checkPeriodStudent($test_type, $school_id, LessonCode::FIRST_TEST_WAIT());
                    break;
                case LaType::PLS_TEST:
                    $list_student = $this->checkPeriodStudent($test_type, $school_id, LessonCode::PL_TESTWAIT());
                    break;
                case LaType::GRASTST:
                    $list_student = $this->checkPeriodStudent($test_type, $school_id, LessonCode::TEST_2_WAIT());
                    break;
                case LaType::DRV_LESSON:
                    $list_student = $this->checkPeriodStudent($test_type, $school_id, LessonCode::TEST_2_WAIT());
                    break;
            }

            $data = [
                'test_type' => $ischeckType ?  $ischeckType : '',
                'test_date' =>  $request->query('test_date'),
                'num_of_days' => $request->query('num_of_days'),
                'period_num' =>  $period ? $period->period_from . '~' . $period->period_to : null,
                'list_student' => $list_student ? $list_student : [],
            ];
        } catch (\Throwable $th) {
            throw $th;
        }
        return view('back.school-staff.register', ['data' =>  (object)$data]);
    }
    /**
     * @Route('/school-staff/test, method: 'POST', name: 'school-staff.test-create')
     */
    public function studentTestCreate(Request $request)
    {
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
            // foreach ($existLedge as $value) {
            //     if ($value->school_id != $schoolId) {
            //         return abort(403);
            //     }
            // }
            $testType = $request->test_type == LaType::PLS_TEST ? TestType::TEM_LICENSE : TestType::ORTHER_LICENSE;
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
            // foreach ($existLedge as $ledge) {
            //     //4. 権限チェック
            //     // B. 選択した教習原簿の教習所が、ログイン者の教習所と同じかチェックする。
            //     if ($ledge->school_id != $schoolId) {
            //         return abort(403);
            //     }
            // }
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
                $dataTest['test_date'] = now();
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
            $testNumMax = LessonAttend::where('test_id', $modelTests->id)->where('la_type', $request->test_type)->max('test_num');

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
                    $dataLessonAttend['stage'] = StageType::checkType($request->test_type);
                    $dataLessonAttend['period_date'] = now();
                    $dataLessonAttend['period_from'] = $request->period_num_from;
                    $dataLessonAttend['period_to'] = $request->period_to;
                    $dataLessonAttend['test_id'] = $modelTests->test_id;
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
        return view('back.school-staff.register');
    }
}
