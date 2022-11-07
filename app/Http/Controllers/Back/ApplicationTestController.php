<?php

namespace App\Http\Controllers\Back;

use App\Enums\ConfirmationRecsStatus;
use App\Enums\LaType;
use App\Enums\LessonAttendOption;
use App\Enums\LessonAttendStatus;
use App\Enums\ResultType;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\ConfirmationRecord;
use App\Models\LessonAttend;
use App\Models\SchoolPeriodM;
use App\Models\Tests;
use App\Models\SystemValue;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationTestController extends Controller
{
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
            ->where('glesson_attends.test_id', $dataTest && $dataTest->id)
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
}
