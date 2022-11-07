<?php

namespace App\Http\Controllers\Back;

use App\Enums\TestType;
use App\Enums\Seq;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\AptitudeDriving\AptitudeDrivingRequest;
use App\Models\AdmCheckItem;
use App\Models\AptitudeDriving;
use App\Models\Ledger;
use App\Models\SchoolStaff;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;

class AptitudeDrivingController extends Controller
{


    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $role = $user->schoolStaff->role;
            // A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3												
            $ledger = Ledger::where('id', $request->ledger_id)->first();
            if (!$ledger) {
                abort(404);
            }
            if ($user->school_id !== session('school_id')) {
                abort(403);
            }
            // システム管理者 || 事務員1
            if (($role & (SchoolStaffRole::CLERK_TWO + SchoolStaffRole::APTITUDE_TESTER + SchoolStaffRole::INSTRUCTOR + SchoolStaffRole::EXAMINER + SchoolStaffRole::SUB_ADMINISTRATOR + SchoolStaffRole::ADMINISTRATOR)) == 0) {
                abort(403);
            }
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }


    /**
     * @Route('/aptitude-driving/create', method: 'GET', name: 'aptitude-driving.create')
     */
    public function create($ledger_id)
    {
        //4.教習生番号、氏名を取得
        $admCheckItem = AdmCheckItem::where('ledger_id', $ledger_id)->where('status', Status::ENABLED)->first();
        if (!isset($admCheckItem)) {
            return abort(404);
        }
        return view('back.aptitude-driving.create', ['data' => $admCheckItem, 'seq' => Seq::FIRST1, 'test_type' => TestType::OD]);
    }

    /**
     * @Route('/aptitude-driving/new', method: 'POST', name: 'aptitude-driving.new')
     */
    public function new(AptitudeDrivingRequest $request, $id)
    {
        //4.教習生番号、氏名を取得
        $admCheckItem = AdmCheckItem::where('ledger_id', $id)->where('status', Status::ENABLED)->first();
        if (!isset($admCheckItem)) {
            return abort(404);
        }
        try {
            $aptitudeDrvs = $request->input();
            $aptitudeDrvs['ledger_id'] = $admCheckItem->ledger_id;
            $aptitudeDrvs['school_id'] = $admCheckItem->school_id;
            $aptitudeDrvs['score'] = $request->od_drv_aptitude . '' . $request->od_safe_aptitude;
            $aptitudeDrvs['status'] =  Status::ENABLED();

            AptitudeDriving::handleSave($aptitudeDrvs, null);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('aptitude-driving.create', $id)->with(['success' => Lang::get('messages.MSI00004')]);
    }
}
