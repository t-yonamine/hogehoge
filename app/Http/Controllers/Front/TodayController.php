<?php

namespace App\Http\Controllers\Front;

use App\Enums\AbsentType;
use App\Enums\CancelType;
use App\Enums\CodeName;
use App\Enums\CommentType;
use App\Enums\ConfgInformationType;
use App\Enums\ConfirmationRecsStatus;
use App\Enums\ImageType;
use App\Enums\IsLatestType;
use App\Enums\ItemMasteryStatus;
use App\Enums\LessonAttendStatus;
use App\Enums\PeriodAction;
use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Enums\SchoolStaffRole;
use App\Models\LessonItemMastery;
use App\Enums\Status;
use App\Enums\TargetType;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ConfirmationRecord;
use App\Models\DispatchCar;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\LessonCar;
use App\Models\LessonComment;
use App\Models\Period;
use App\Models\SchoolCode;
use App\Models\SchoolPeriodM;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TodayController extends Controller
{

    const PROC_TYPE_MIKIWA = 2; //みきわめ項目

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $role = $user->schoolStaff->role;
            //1. 入力パラメータ																										
            //   A. セッション情報 共通ロジック/セッション情報  
            $schoolStaffId = session('school_staff_id');
            $schoolId = session('school_id');

            // B. 指定された時限が存在するか確認。
            // a. 時限データの存在確認。ある場合は、「権限チェック」へ。
            $existPeriod = Period::with('schoolPeriodM',)->where('school_staff_id', $schoolStaffId)
                ->where('period_date', $request->period_date)
                ->where('period_num', $request->period_num)->with('dispatchCars', function ($query) {
                    $query->where('target_type', TargetType::PERIOD)->with('lessonCar');
                })->first();

            // b. 教習所別時限マスタに存在するか確認。
            $existTimePeriod = SchoolPeriodM::where('school_id', $schoolId)
                ->where('period_num', $request->period_num)->first();

            // 2. 存在チェック																										
            //   A. 職員IDの存在チェック 共通ロジック/存在チェック#1  セッションの職員IDの情報を読む。																										
            //   B. 時限IDの存在チェック																										
            if (
                !$schoolStaffId
                || !$schoolId
                || !$existPeriod
                || !$existTimePeriod
            ) {
                abort(404);
            }

            // 4. 権限チェック																										
            //   A. 同一教習所チェック																										

            if (
                $existPeriod->school_id != $schoolId
                || ($role & (SchoolStaffRole::INSTRUCTOR + SchoolStaffRole::EXAMINER)) == 0
            ) {
                abort(403);
            }

            $request['period'] = $existPeriod;

            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * @Route('/', method: 'GET', name: 'frt.today.index')
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $schoolStaffId = session('school_staff_id');
        $date = $request->input('period_date') ?? date('Y-m-d');

        $periodNum = $request->period_num;

        $request->validate([
            'period_date' => 'nullable|max:10|date',
        ]);

        // 時限IDの存在チェック
        $period =  $request->period;

        $periodM = SchoolPeriodM::with([
            'period' => function ($q) use ($user, $date) {
                $q->where('school_id', $user->schoolStaff->school_id)
                    ->where('period_date', $date)->where('school_staff_id', $user->schoolStaff->id);
            }, 'period.drlType', 'period.workType'
        ])->orderBy('period_num')->get();

        // B. 変更できるか確認。入力項目のenable判定。																										
        $disabled = !($period->status != PeriodStatus::APPROVED() && $period->data_sts == Status::ENABLED());

        $selectDisabled =  $period->status->value == PeriodStatus::APPROVED;

        $codePeriod = Code::where('cd_name', 'period_type')->where('cd_value', $period->period_type)->first();

        //data response 
        $data = [
            'period' => $period,
            'codePeriod' => $codePeriod,
            'period_date' => $date,
            'periodM' => $periodM,
            'schoolStaffId' => $schoolStaffId,
            'periodNum' => $periodNum,
            'disabled' => $disabled,
            'selectDisabled' =>  $selectDisabled
        ];
        switch ($period->period_type) {
            case PeriodType::WORK():
                # code...
                $codeWord = Code::where('cd_name', 'work_type')->where('cd_value', $period->work_type)->first();
                $data['cdText'] = $codeWord->cd_text;
                break;

            case PeriodType::DRV_LESSON():

                $schoolCode = SchoolCode::where('school_id', $period->school_id)->where('cd_name', 'drl_type')->where('cd_value', $period->drl_type)->first();

                /**
                 *  5. 受講データの取得	
                 *      本時限に結びついた受講データを検索する。
                 *  6. 受講単位の教習項目習熟度を取得する。					
                 */
                $lessonAttend = LessonAttend::with(['admCheckItem', 'dispatchCar.lessonCar', 'lessonComments' => function ($q) {
                    $q->where('comment_type', CommentType::ITEMS_TO_BE_SENT())->where('status',  Status::ENABLED());
                }, 'image' => function ($q) {
                    $q->where('image_type', ImageType::FOR_ORIGINAL())->where('status',  Status::ENABLED());
                }, 'lessonItemMastery' => function ($q) {
                    $q->orderBy('stage')->orderBy('lesson_item_num');
                }])
                    ->whereHas('admCheckItem', function ($q) {
                        $q->where('status', Status::ENABLED());
                    })
                    ->where('period_id', $period->id)
                    ->where('data_sts', Status::ENABLED())
                    ->select('*')
                    ->orderBy('id')->get();

                // みきわめ表示判定処理
                foreach ($lessonAttend as $key => $value) {
                    // A. 受講、教習項目習熟度、教習項目テーブルで集計する。
                    $resultA = LessonItemMastery::join('glesson_items as b', 'b.id', 'glesson_item_mastery.lesson_item_id')
                        ->where('glesson_item_mastery.lesson_attend_id', $value->id)->selectRaw(
                            'count(*) as total, 
                            sum(case when b.proc_type = ' . self::PROC_TYPE_MIKIWA . ' then 1 else 0 end) as mikiwame, 
                            max(case when b.proc_type = ' . self::PROC_TYPE_MIKIWA . ' then glesson_item_mastery.re_lesson else 0 end) as relesson'
                        )->first();

                    if ($resultA->total > 0) {
                        if ($resultA->mikiwame > 0 && $resultA->relesson > 0) {
                            $lessonAttend[$key]['is_show_mikiwame'] = true;
                            $lessonAttend[$key]['is_show_good'] = false;
                        } else if ($resultA->mikiwame > 0 && $resultA->relesson <= 0) {
                            $lessonAttend[$key]['is_show_mikiwame'] = true;
                            $lessonAttend[$key]['is_show_good'] = true;
                        } else {
                            // 2. 先に読んだ受講TBL(glesson_attends)の情報で、現在の段階から教習済みでない項目の数を数えて判定。
                            $resultA = LessonItemMastery::join('glesson_items as b', 'b.id', 'glesson_item_mastery.lesson_item_id')
                                ->where('glesson_item_mastery.ledger_id', $value->ledger_id)
                                ->where('glesson_item_mastery.stage', $value->stage)
                                ->where('glesson_item_mastery.is_latest', IsLatestType::LATEST)
                                ->selectRaw(
                                    'sum(case when b.proc_type =  ' . self::PROC_TYPE_MIKIWA . ' and glesson_item_mastery.status != ' . ItemMasteryStatus::COMPLETED . ' then 1 else 0 end) as mikiwame,
                                     sum(case when b.proc_type !=  ' . self::PROC_TYPE_MIKIWA . ' and glesson_item_mastery.status != ' . ItemMasteryStatus::COMPLETED . ' then 1 else 0 end) as other'
                                )->first();

                            if ($resultA->mikiwame > 0 && $resultA->other == 0) {
                                $lessonAttend[$key]['is_show_mikiwame'] = true;
                                $lessonAttend[$key]['is_show_good'] = false;
                            } else {
                                $lessonAttend[$key]['is_show_mikiwame'] = false;
                                $lessonAttend[$key]['is_show_good'] = false;
                            }
                        }
                    }
                }

                $data['lessonAttend'] = $lessonAttend;
                $data['cdText'] = $schoolCode->cd_text;
                break;
            default:
                abort(404);
        }

        // 6. 選択肢用データ読み込み

        $schoolId = session('school_id');
        $optionSchoolCode = SchoolCode::where('school_id', $schoolId)
            // ->where('cd_name', 'drl_type')
            ->where('status', Status::ENABLED)
            ->orderBy('display_order')
            ->orderBy('cd_value')
            ->get();

        $optionCode = Code::where('status', Status::ENABLED)
            ->orderBy('display_order')
            ->orderBy('cd_value')
            ->get();


        $optionCarModel = DB::select(DB::raw("SELECT *FROM gcodes AS a INNER JOIN 
        (SELECT car_type_cd FROM glesson_cars WHERE school_id = 1 AND status = 1 GROUP BY car_type_cd)
         AS b ON (b.car_type_cd = a.cd_value)
        WHERE a.cd_name = 'car_type' AND a.status = 1"));

        $optionNumberCar = LessonCar::where('school_id', $schoolId)
            ->where('status', Status::ENABLED)
            ->orderBy('car_type_cd')
            ->orderBy('lesson_car_num')
            ->get();


        $data['optionCode'] = $optionCode;
        $data['optionSchoolCode'] = $optionSchoolCode;
        $data['optionCarModel'] = $optionCarModel;
        $data['optionNumberCar'] = $optionNumberCar;

        return  view('tablet.today.index', $data);
    }

    /**
     * @Route('/', method: 'PUT', name: 'frt.today.index')
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

        // B. 変更できない場合はエラー。																										
        if ($period->status == PeriodStatus::IMPLEMENTED() || $period->data_sts  == Status::DISABLED()) {
            abort(403);
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

    /**
     * @Route('/comment', method: 'POST', name: 'frt.today.comment')
     */
    public function commentSave(Request $request)
    {
        $request->validate([
            'comment_text' => 'required|max:100',
        ], [], [
            'comment_text' => '申し送り事項',
        ]);
        $data = $request->input();
        //指定教習原簿の存在チェック
        $existLedgers = Ledger::where('id', $data['ledger_id'])->where('status', Status::ENABLED)->first();
        //受講の存在チェック
        $existLessonAttends = LessonAttend::where('id', $data['lesson_attend_id'])->first();
        if (!$existLedgers || !$existLessonAttends) {
            abort(404);
        }
        //パラメータ.教習コメントIDが null でない場合、教習コメントの存在チェック
        $existLessonComments = LessonComment::where('id', $data['comment_id'])->first();
        //教習コメント glesson_comments 登録・更新処理	
        LessonComment::handleSave($data, $existLedgers, $existLessonAttends, $existLessonComments);
        return redirect()->route('frt.today.index', $request->only(['period_date', 'period_num']));
    }

    /**
     * @Route('/comment', method: 'POST', name: 'frt.today.newPeriod')
     */
    public function newPeriod(Request $request)
    {
        $request->validate([
            'period_type_l' => 'required|max:1',
            'sub_task' => 'max:8',
            'room_cd' => 'max:4',
            'car_model' => 'max:2',
            'number_car' => 'max:10',

        ], [], [
            'period_type_l' => '業務内容',
            'sub_task' => '業務内容',
            'room_cd' => '教室名・場所',
            'car_model' => '車種と号車',
            'number_car' => '車種と号車',
        ]);

        $schoolStaffId = session('school_staff_id');
        $schoolId = session('school_id');
        // A. 存在チェック
        //   a. 時限が存在しない場合はエラー。

        // B. 権限チェック
        $periodSchoolStaff = Period::where('id', $request->period_id)->where('school_staff_id', $schoolStaffId)->first();;

        // C. 編集可不可判定。
        $selectDisabled =  $periodSchoolStaff->status->value == PeriodStatus::APPROVED;

        // D. 業務種別変更可不可判定。
        $existBusiness = LessonAttend::where('period_id', $request->period_id)->where('data_sts', Status::ENABLED)->first();

        if (!$periodSchoolStaff || !$existBusiness) {
            abort(403);
        }
        $periodType = $request->period_type_l;
        $schoolStaffId = session('school_staff_id');
        $dataPeriodFill = [
            'period_type' => $periodType,
            'work_type' =>  $periodType == PeriodType::WORK ? $request->sub_task : null,
            'room_cd' => $periodType == PeriodType::LECTURE ? $request->room_cd : null,
            'course_type_cd' => $periodType == PeriodType::DRV_LESSON || $periodType == PeriodType::TEST ? $request->room_cd : null,
            'updated_user_id' => $schoolStaffId,
        ];

        if ($request->period_id) {
            // A. 現在、時限にリンクしている配車(gdispatch_cars)テーブルを削除する。
            $period = Period::find($request->period_id);
            if (!$period) {
                abort(404);
            }
            Period::handleUpdate($dataPeriodFill, $request->period_id, $schoolStaffId);

            //  B. 号車が未選択でない場合、配車(gdispatch_cars)を追加する。
            $model = DispatchCar::where('period_id', $period->id)->where('target_type', TargetType::PERIOD)->first();
            $dataDispatchCarsFill = [
                'lesson_car_id' => $request->number_car,
                'school_id' => $schoolId,
                'use_date' => $period->period_date,
                'use_period_num' => $period->period_num,
                'target_type' => TargetType::PERIOD,
                'period_id' => $period->id,
                'school_staff_id' => $schoolStaffId,
            ];
            DispatchCar::handleDelete($model);
            DispatchCar::handleSave($dataDispatchCarsFill, null);
        }

        return redirect()->route('frt.today.index', $request->only(['period_date', 'period_num']));
    }
}
