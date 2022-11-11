<?php

namespace App\Http\Controllers\Front;

use App\Enums\CommentType;
use App\Enums\ImageType;
use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Enums\Status;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\LessonAttend;
use App\Models\LessonItemMastery;
use App\Models\Period;
use App\Models\SchoolCode;
use App\Models\SchoolPeriodM;
use Illuminate\Http\Request;

class TodayGinouController extends Controller
{

    public function __construct()
    {
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        // Block A

        /**
         * 初期表示		
         *  1. 入力パラメータ	
         *   A. セッション情報 共通ロジック/セッション情報		
         *   B. 時限ID … 遷移元の画面で選択したID。																																																																															
         */

        /** 
         * 
         *  2. 存在チェック																										
         *   A. 職員IDの存在チェック 共通ロジック/存在チェック#1  セッションの職員IDの情報を読む。																										
         *   B. 時限IDの存在チェック																										
         */

        $period = Period::whereHas('schoolPeriodM')->where('period_date', Helper::getStringFormatDate($request->period_date, 'Y-m-d'))
            ->where('period_num', $request->period_num)->first();

        // $period = Period::where('id', 1)->with(['schoolPeriodM'])->first();
        if (!$period) {
            abort(404);
        }

        $typeCode = Code::where('cd_name', 'period_type')->where('cd_value', $period->period_type)->first();
        $schoolCode = SchoolCode::where('school_id', $period->school_id)->where('cd_name', 'drl_type')->where('cd_value', $period->drl_type)->first();


        /** 
         * 3. 権限チェック																										
         *   A. 同一教習所チェック																										
         *       別教習所の時限が指定された場合は権限エラー。403 Error. Forbidden.																										
         *       gperiods.school_id != {session/school_id} の場合はエラー。																										
                                                                                                                
         *   B. 変更できるか確認。入力項目のenable判定。																										
         *       承認済以外の場合は、変更可能。以下を確認。																										
         */

        $schoolId =  $request->session()->get('school_id');

        if (!$schoolId) {
            return abort(403);
        }

        if ($schoolId != $period->school_id) {
            return abort(403);
        }

        /*
            4. 前提条件チェック																										
                A. 対象の時限の時限タイプが技能教習以外の場合は前提条件エラー。412 Error. Precondition Failed.																										
                gperiods.period_type != 1:技能 の場合はエラー。																										
         */

        if ($period->period_type != PeriodType::DRV_LESSON()) {
            return abort(412, 'Error. Precondition Failed.');
        }

        /**
         *  5. 受講データの取得	
         *      本時限に結びついた受講データを検索する。				
         */

        $lessonAttend = LessonAttend::select(['glesson_attends.*'])
            ->whereHas('admCheckItems', function ($q) {
                $q->where('status', Status::ENABLED());
            })
            ->whereHas('lessonComments', function ($q) {
                $q->where('comment_type', CommentType::ITEMS_TO_BE_SENT())->where('status',  Status::ENABLED());
            })
            ->leftJoin('gimages', 'gimages.target_id', '=', 'glesson_attends.ledger_id')
            ->where('gimages.image_type', ImageType::FOR_ORIGINAL())
            ->where('gimages.status', Status::ENABLED())
            ->where('glesson_attends.period_id', $period->id)
            ->where('glesson_attends.status', Status::ENABLED())
            ->with(['admCheckItems', 'lessonItemMastery', 'lessonComments', 'dsipatchCar.lessonCars'])
            ->orderBy('glesson_attends.id')->get();

        /**
         *  6. 受講単位の教習項目習熟度を取得する。	
         *      技能教習を受けた教習項目のデータ(教習項目習熟度:glesson_item_mastery)を0件以上取得する。				
         */

        $lessonItemMastery = LessonItemMastery::where('lesson_attend_id', 1)->orderBy('stage', 'ASC')->orderBy('lesson_item_num', 'ASC')->get();

        // Block B

        /**
         * みきわめ表示判定処理
         * 1. 対象の受講データに教習項目習熟度が登録されているか確認する。	
         *   その中にみきわめ項目があるかも確認する。	
         * 	
         *   A. 受講、教習項目習熟度、教習項目テーブルで集計する。		
         */

        $collect = LessonItemMastery::with('lessonItems')->where('lesson_attend_id', 1)->count();

        return view('components.tablet.today.today_ginou', ['period' => $period, 'lessonAttend' => $lessonAttend, 'lessonItemMastery' => $lessonItemMastery, 'typeCode' => $typeCode, 'schoolCode' => $schoolCode]);
    }

}
