<?php

namespace App\Http\Controllers\Front;

use App\Enums\CodeName;
use App\Enums\ConfgInformationType;
use App\Enums\ConfirmationRecsStatus;
use App\Enums\PeriodAction;
use App\Enums\PeriodStatus;
use App\Enums\PeriodType;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\ConfirmationRecord;
use App\Models\Period;
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
        $codeWord = Code::where('cd_name', 'work_type')->where('cd_value', $period->work_type)->first();

        return  view('tablet.today.index', ['period' => $period, 'codePeriod' => $codePeriod, 'codeWord' => $codeWord]);
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
                // where gperiods.status = 2:承認済																										
                // or gperiods.data_sts = 0:無効																										
                // pending waiting for Q&A #24

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
            default:
                break;
        }
        return  redirect()->route('frt.today.index', $request->query->all());
    }
}
