<?php

namespace App\Http\Controllers\Back\EffectMeasurement;

use App\Enums\LaType;
use App\Enums\Role;
use App\Enums\StageType;
use App\Http\Controllers\Controller;
use App\Http\Requests\EffectMeasurements\EffectMeasurementRequest;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\SchoolStaff;
use Exception;
use Illuminate\Http\Request;

class EffectMeasurementController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //
        return  view('back.effect-measurement.index');
    }

    public function create(Request $request, $ledger_id)
    {
        // set default data to check
        // $request->session()->put('school_staff_id', 2);
        // $request->session()->put('school_id', 1);
        // $request->session()->put('school_cd', 9901);

        // 1. 入力パラメータ
        //   A. セッション情報 共通ロジック/セッション情報#1-3
        $school_staff_id =  $request->session()->get('school_staff_id');
        $school_id =  $request->session()->get('school_id');
        $school_cd =  $request->session()->get('school_cd');
        if (empty($school_staff_id) || empty($school_id) || empty($school_cd)) {
            abort(403, 'Forbidden.');
        }

        //   B. param/ledger_id 遷移元から渡された教習原簿ID																										
        //   C. param/la_type 受講区分。遷移元で指定。仮免新規:2211、卒検新規:2221																										
        if (!isset($ledger_id) || !isset($request->la_type) || ($request->la_type != 2221 && $request->la_type != 2211)) {
            abort(404);
        }
        // 2. 存在チェック																										
        //   A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3																										
        $data = Ledger::with('admCheckItem')->where('id', $ledger_id)->first();

        if (!isset($data)) {
            return abort(404);
        }

        // 3.権限チェック
        //   A. 教習原簿とセッションの教習所一致確認 共通ロジック
        if ($school_id != $data->school_id) {
            return abort(403, 'Forbidden.');
        }

        //   B. ログインした人の役割チェック。事務員2以上が操作可能。
        $schoolStaff = SchoolStaff::find($school_staff_id);
        if (!isset($schoolStaff) || $schoolStaff->role < Role::CLERK_2) {
            return abort(403, 'Forbidden.');
        }

        return view('back.effect-measurement.create', ['data' => $data, 'laType' => $request->la_type, 'resultInit' => true]);
    }

    public function store(EffectMeasurementRequest $request)
    {
        // 1. 入力パラメータ
        //   A. セッション情報 共通ロジック/セッション情報#1-3
        $school_staff_id =  $request->session()->get('school_staff_id');
        $school_id =  $request->session()->get('school_id');
        if (empty($school_id)) {
            abort(403, 'Forbidden.');
        }

        $schoolStaff = SchoolStaff::where('id', $school_staff_id)->first();
        if (!$schoolStaff) {
            return abort(403, 'Forbidden.');
        }

        // 2.存在チェック																										
        //    A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3																										
        $ledger = Ledger::find($request->ledger_id);
        if (!$ledger) {
            return abort(404);
        }

        try {
            $data = $request->input();
            $data['school_id'] = $school_id;
            $data['stage'] = $request->la_type > LaType::PRE_EXAMINATION ? StageType::STAGE_2 : StageType::STAGE_1;
            $data['school_staff_id'] = $schoolStaff->id;
            $data['period_to'] = $request->period_from;
            $data['school_id'] = $school_id;
            $data['created_user_id'] = $schoolStaff->id;
            $data['updated_user_id'] = $schoolStaff->id;

            LessonAttend::handleSave($data, $ledger, null);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }

        return redirect()->route('effect-measurement.create', ['ledger_id' => $ledger->id])->with(['success' => 'success']);
    }
}
