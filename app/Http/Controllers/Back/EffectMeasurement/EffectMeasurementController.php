<?php

namespace App\Http\Controllers\Back\EffectMeasurement;

use App\Enums\LaType;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\EffectMeasurements\EffectMeasurementRequest;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\SchoolStaff;
use App\Models\Staff;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

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

        // 1. 入力パラメータ
        if (!isset($ledger_id) || !isset($request->la_type) || ($request->la_type != 2221 && $request->la_type != 2211)) {
            return abort(404);
        }

        $user = Auth::user();
        if (!isset($user)) {
            return abort(403, 'Forbidden.');
        }

        $data = Ledger::with('admCheckItem')->where('id', $ledger_id)->first();

        // check data Ledger exist
        if (!isset($data)) {
            return abort(404);
        }

        // 3.権限チェック
        //   A. 教習原簿とセッションの教習所一致確認 共通ロジック
        if ($user->school_id != $data->school_id) {
            return abort(403, 'Forbidden.');
        }

        //   B. ログインした人の役割チェック。事務員2以上が操作可能。
        $staff = Staff::where("staff_no", $user->login_id)->first();
        if (!isset($staff) || $staff->role < Role::CLERK_2) {
            return abort(403, 'Forbidden.');
        }

        return view('back.effect-measurement.create', ['data' => $data, '' => 1, 'laType' => $request->la_type, 'resultInit' => true]);
    }

    public function store(EffectMeasurementRequest $request)
    {
        // get user info login
        $user = Auth::user();
        if (!$user) {
            return abort(403, 'Forbidden.');
        }

        $schoolStaff = SchoolStaff::where('school_id', $user->school_id)->first();
        if (!$schoolStaff) {
            return abort(403, 'Forbidden.');
        }

        $ledger = Ledger::find($request->ledger_id);
        if (!$ledger) {
            return abort(404);
        }

        try {
            $data = $request->input();
            $data['school_id'] = $user->school_id;
            $data['stage'] = $request->la_type > LaType::PRE_EXAMINATION ? 2 : 1;
            $data['school_staff_id'] = $schoolStaff->id;
            $data['period_to'] = $request->period_from;
            $data['school_id'] = $user->school_id;
            $data['created_user_id'] = $schoolStaff->id;
            $data['updated_user_id'] = $schoolStaff->id;

            LessonAttend::handleSave($data, $ledger, null);
        } catch (\Throwable $th) {
            throw new Exception($th->getMessage());
        }

        return redirect()->route('effect-measurement.create', ['ledger_id' => $ledger->id])->with(['success' => 'success']);
    }
}
