<?php

namespace App\Http\Controllers\Back\EffectMeasurement;

use App\Http\Controllers\Controller;
use App\Models\Ledger;
use App\Models\LessonAttend;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EffectMeasurementController extends Controller
{
    const EFF_MEAS_MIN = 2200;
    const EFF_MEAS_MAX = 2299;
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        return view('back.effect-measurement.index');
    }

    /**
     * 入力パラメータ																										
     *    A. セッション情報 共通ロジック/セッション情報#1-3																										
     *    B. param/ledger_id 遷移元から渡された教習原簿ID
     */
    public function index($id)
    {
        // 教習原簿IDの存在チェック 共通ロジック/存在チェック#3	
        $existLedge = Ledger::where('id', $id)->first();
        if (empty($existLedge)) {
            abort(404);
        };
        // 教習原簿とセッションの教習所一致確認 共通ロジック/権限チェック#2
        if ($existLedge->school_id != Auth::user()->school_id) {
            abort(403);
        };
        // 対象教習原簿の効果測定の一覧を求めて表示。
        $data = Ledger::with(['gadmCheckItems', 'glessonAttends' => function ($q) {
            $q->with('gschoolStaff')->where('la_type', '>=', self::EFF_MEAS_MIN)
                ->where('la_type', '<=', self::EFF_MEAS_MAX)
                ->orderBy('period_date');
        }])->where('id', $id)->first();
        if (empty($data) || empty($data->gadmCheckItems)) {
            abort(404);
        }
        return view('back.effect-measurement.index', ['data' => $data, 'glesson_attends' => $data->glessonAttends]);
    }

    // 削除ボタン処理
    public function delete($id)
    {
        // 教習原簿IDの存在チェック
        $glessonAttends = LessonAttend::where('id', $id)->first();
        if (empty($glessonAttends)) {
            return back()->with('error', '見つけることができませんでした');
        }
        //ボタン処理
        $glessonAttends->delete();
        return back()->with('msg', '削除しました。');
    }
}
