<?php

namespace App\Http\Controllers\Front;

use App\Enums\AbsentType;
use App\Enums\CancelType;
use App\Enums\CodeName;
use App\Enums\CommentType;
use App\Enums\ConfgInformationType;
use App\Enums\ConfirmationRecsStatus;
use App\Enums\ImageType;
use App\Enums\LessonAttendStatus;
use App\Enums\PeriodAction;
use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Enums\Status;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ConfirmationRecord;
use App\Models\LessonAttend;
use App\Models\Period;
use App\Models\SchoolCode;
use Closure;
use Illuminate\Http\Request;

class TodayController extends Controller
{

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {

            //1. 入力パラメータ																										
            //   A. セッション情報 共通ロジック/セッション情報  
            $schoolStaffId = session('school_staff_id');
            $schoolId = session('school_id');
            if (!$request->period_date || !$request->period_num) {
                abort(404);
            }

            // B. 時限ID … 遷移元の画面で選択したID。
            $period = Period::whereHas('schoolPeriodM')->where('period_date', Helper::getStringFormatDate($request->period_date, 'Y-m-d'))
                ->where('period_num', $request->period_num)->first();

            // 2. 存在チェック																										
            //   A. 職員IDの存在チェック 共通ロジック/存在チェック#1  セッションの職員IDの情報を読む。																										
            //   B. 時限IDの存在チェック																										
            if (!$schoolStaffId || !$schoolId || !$period) {
                abort(404);
            }

            // 4. 権限チェック																										
            //   A. 同一教習所チェック																										
            if ($period->school_id != $schoolId) {
                abort(403);
            }

            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * @Route('/', method: 'GET', name: 'tablet.font.today-detail')
     */
    public function index(Request $request)
    {

        // 時限IDの存在チェック
        $period = Period::whereHas('schoolPeriodM')->where('period_date', Helper::getStringFormatDate($request->period_date, 'Y-m-d'))
            ->where('period_num', $request->period_num)->first();

        //   B. 変更できるか確認。入力項目のenable判定。
        // 
        // 

        $codePeriod = Code::where('cd_name', 'period_type')->where('cd_value', $period->period_type)->first();

        switch ($period->period_type) {
            case PeriodType::WORK():
                # code...
                $codeWord = Code::where('cd_name', 'work_type')->where('cd_value', $period->work_type)->first();
                return  view('tablet.today.index', ['period' => $period, 'codePeriod' => $codePeriod, 'codeWord' => $codeWord]);

            case PeriodType::DRV_LESSON():

                // 4. 権限チェック
                //   B. 変更できるか確認。入力項目のenable判定。
                $isEnableForm = $period->status != PeriodStatus::APPROVED() && $period->data_sts == Status::ENABLED();

                $schoolCode = SchoolCode::where('school_id', $period->school_id)->where('cd_name', 'drl_type')->where('cd_value', $period->drl_type)->first();
                /**
                 *  5. 受講データの取得	
                 *      本時限に結びついた受講データを検索する。
                 *  6. 受講単位の教習項目習熟度を取得する。					
                 */

                $lessonAttend = LessonAttend::whereHas('admCheckItem', function ($q) {
                    $q->where('status', Status::ENABLED());
                })
                    ->with(['dsipatchCar.lessonCar', 'lessonComments' => function ($q) {
                        $q->where('comment_type', CommentType::ITEMS_TO_BE_SENT())->where('status',  Status::ENABLED());
                    }, 'image' => function ($q) {
                        $q->where('image_type', ImageType::FOR_ORIGINAL())->where('status',  Status::ENABLED());
                    }, 'lessonItemMastery' => function ($q) {
                        $q->orderBy('stage')->orderBy('lesson_item_num');
                    }])
                    ->where('period_id', $period->id)
                    ->where('data_sts', Status::ENABLED())
                    ->select('*')
                    ->orderBy('id')->get();

                return  view('tablet.today.index', ['period' => $period, 'codePeriod' => $codePeriod, 'lessonAttend' => $lessonAttend,  'schoolCode' => $schoolCode, 'isEnableForm' => $isEnableForm]);

            default:
                abort(404);
        }
    }

    /**
     * @Route('/', method: 'PUT', name: 'today.index')
     */
    public function update(Request $request)
    {

        //1. 入力パラメータ																										
        //   A. セッション情報 共通ロジック/セッション情報
        $schoolStaffId = session('school_staff_id');
        if (!$request->period_id) {
            abort(404);
        }

        // B. 時限ID … 遷移元の画面で選択したID。
        $period = Period::find($request->period_id);
        if (!$period) {
            abort(404);
        }

        switch ($request->action) {
            case PeriodAction::UPDATE_WORK:

                // B. 変更できない場合はエラー。																										
                // 承認済の場合、無効の場合は権限エラー。 403 Error. Forbidden.																										
                $isEnableForm = $period->status != PeriodStatus::APPROVED() && $period->data_sts == Status::ENABLED();
                if (!$isEnableForm) {
                    abort(403);
                }
                // 5. 前提条件チェック																										
                //   A. 対象の時限の時限タイプが業務以外の場合は前提条件エラー。412 Error. Precondition Failed.																										
                if ($period->period_type != PeriodType::WORK()) {
                    abort(412);
                }
                // 5. 時限を更新する。
                $dataPeriodFill = [
                    'remarks' => $request->remarks,
                    'status' => PeriodStatus::NEW()
                ];
                Period::handleSave($dataPeriodFill, $period, $schoolStaffId);

                // 6. 確認記録(承認)を1:確認待ち にする。
                $confirmationRecord = ConfirmationRecord::where('conf_target', CodeName::PERIODS)
                    ->where('target_id', $period->id)
                    ->where('conf_type', ConfgInformationType::APPROVAL)
                    ->where('status', ConfirmationRecsStatus::WAITING)->first();
                if ($confirmationRecord) {
                    $dataConfirmationFill = [
                        'status' => ConfirmationRecsStatus::WAITING_CONFIRMATION,
                        'updated_at' => now(),
                        'updated_user_id' =>  $schoolStaffId
                    ];
                    ConfirmationRecord::handleSave($dataConfirmationFill, $confirmationRecord);
                }
                break;
            case PeriodAction::REDIRECT_LINK:
                // B. 変更できない場合はエラー。																										
                // 承認済の場合、無効の場合は権限エラー。 403 Error. Forbidden.																										
                // where gperiods.status = 2:承認済																										
                // or gperiods.data_sts = 0:無効																										
                // pending waiting for Q&A #24

                // 4. 教習所フロント_新規時限.xlsx へ更新モードで遷移する。
                break;

            case PeriodAction::UPDATE_LESSON:
                // B. 変更できない場合はエラー。																										
                // 承認済の場合、無効の場合は権限エラー。 403 Error. Forbidden.	
                $isEnableForm = $period->status != PeriodStatus::APPROVED() && $period->data_sts == Status::ENABLED();
                if (!$isEnableForm) {
                    abort(403);
                }
                // 5. 前提条件チェック																										
                // A. 対象の時限の時限タイプが技能教習以外の場合は前提条件エラー。412 Error. Precondition Failed.																										
                if ($period->period_type != PeriodType::DRV_LESSON()) {
                    abort(412);
                }

                // 6. 不在フラグの反映	
                foreach ($request->lessonAttendIds as $value) {
                    $lessonAttend = LessonAttend::find($value);
                    if (!$lessonAttend) {
                        continue;
                    }
                    $isAbsent = $request->input('is_absent_' . $value);
                    $data = [
                        'updated_at' => now(),
                        'updated_user_id' => $schoolStaffId,
                    ];
                    //   A. 入力の不在フラグ = 1:不在 の場合、受講をキャンセル扱いとする。
                    if ($isAbsent && $lessonAttend->is_absent != AbsentType::ABSENT()) {
                        $data['is_absent'] = AbsentType::ABSENT();
                        $data['status'] = LessonAttendStatus::PENDING();
                        $data['cancel_cd'] = CancelType::CANCELED_DUE_TO_PRE_CHECK();
                        LessonAttend::handleUpdate($data, $lessonAttend);
                    } else if (!$isAbsent && $lessonAttend->is_absent == AbsentType::ABSENT()) {
                        // B. 入力の不在フラグ = 0:存在 の場合、以下を行う。
                        $data['is_absent'] = AbsentType::PRESENT();
                        $data['status'] = LessonAttendStatus::PENDING();
                        $data['cancel_cd'] = CancelType::IMPLEMENTED();
                        LessonAttend::handleUpdate($data, $lessonAttend);
                    }
                }
                // 7. 実施済み判定																										
                //   全受講が実施済みか判定する。																										
                $countLessonAttend = LessonAttend::where('period_id', $request->period_id)
                    ->where('data_sts', Status::ENABLED())
                    ->whereIn('status', [LessonAttendStatus::SCHEDULED_WAITING(), LessonAttendStatus::PENDING(), LessonAttendStatus::COMPLETED()])
                    ->count();

                // 7. 時限(gperiods)の更新																										
                if ($countLessonAttend == count($request->lessonAttendIds)) {
                    //   A. 全受講が実施済みの場合、時限も実施済みにする。																										
                    $dataPeriodFill = [
                        'remarks' => $request->remarks,
                        'status' => PeriodStatus::APPROVED()
                    ];
                    Period::handleSave($dataPeriodFill, $period, $schoolStaffId);

                    // 8. 全受講が実施済みの場合、時限、受講の確認記録(gconfirmation_recs)を確認待ちにする。																										
                    //   A. 時限の確認記録を確認待ちにする。																										
                    $confirmationRecordPeriod = ConfirmationRecord::where('conf_target', CodeName::PERIODS)
                        ->where('target_id', $period->id)
                        ->where('target_id_seq', Status::ENABLED)->first();
                    if ($confirmationRecordPeriod) {
                        $dataConfirmationFill = [
                            'status' => ConfirmationRecsStatus::WAITING_CONFIRMATION,
                            'updated_at' => now(),
                            'updated_user_id' =>  $schoolStaffId
                        ];
                        ConfirmationRecord::handleSave($dataConfirmationFill, $confirmationRecordPeriod);
                    }

                    //   B. 受講の確認記録を確認待ちにする。
                    $confirmationRecordLesson = ConfirmationRecord::join('glesson_attends', 'glesson_attends.id', 'gconfirmation_recs.target_id')
                        ->where('conf_target', CodeName::LESSON_ATTEND)
                        ->where('glesson_attends.period_id', $period->id)
                        ->where('target_id_seq', Status::ENABLED)->first();

                    if ($confirmationRecordLesson) {
                        $dataConfirmationFill = [
                            'status' => ConfirmationRecsStatus::WAITING_CONFIRMATION,
                            'updated_at' => now(),
                            'updated_user_id' =>  $schoolStaffId
                        ];
                        ConfirmationRecord::handleSave($dataConfirmationFill, $confirmationRecordLesson);
                    }
                } else {
                    //  B. 全受講が実施済みでない場合、時限の備考欄を更新する。
                    $dataPeriodFill = [
                        'remarks' => $request->remarks,
                    ];
                    Period::handleSave($dataPeriodFill, $period, $schoolStaffId);
                }

                break;
            default:
                break;
        }
        return  redirect()->route('frt.today.index', $request->query->all());
    }
}
