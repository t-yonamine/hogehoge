<?php

namespace App\Http\Controllers\Front;

use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AptitudeDriving;
use App\Models\Ledger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentController extends Controller
{
    protected const ORIGINALBOOK = 'original-book';
    protected const APTITUDE = 'aptitude';
    protected const TEST = 'test';
    protected const FIRSTSKILL = 'skills-1st';
    protected const FIRSTSUBJECT = 'subject-1st';
    protected const SECONDSKILL = 'skills-2nd';
    protected const SECONDSUBJECT = 'subject-2nd';
    protected const COMPLETION = 'completion';

    public function __construct()
    {
        #・権限チェック、存在チェックは、教習生情報(共通部）で実施する
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            if (!$user->school_id || $user->school_id !== session('school_id')) {
                abort(403);
            }
            $role = $user->schoolStaff->role;
            // 本画面は、指導員か検定員の権限が必要。
            if (($role & (SchoolStaffRole::INSTRUCTOR + SchoolStaffRole::EXAMINER)) == 0) {
                abort(403);
            }
            // 職員IDの存在チェック 共通ロジック/存在チェック#1
            $schoolStaffId = session('school_staff_id');
            // 教習原簿IDの存在チェック　共通ロジック/存在チェック#3
            $existLeger = Ledger::where('id', $request->ledger_id)->where('status', Status::ENABLED)->first();
            if (!$existLeger || !$schoolStaffId) {
                abort(404);
            }
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * 教習生情報(適正検査)
     * @Route('/{ledger_id}', method: 'GET', name: 'frt.student.detail')
     */
    public function detail(Request $request)
    {
        $data = collect();
        switch ($request->tab) {
            case self::ORIGINALBOOK:
                break;

            case self::APTITUDE:
                $data = $this->getAptitude($request->ledger_id);
                break;

            case self::TEST:
                break;

            case self::FIRSTSKILL:
                break;

            case self::SECONDSKILL:
                break;

            case self::FIRSTSUBJECT:
                break;

            case self::SECONDSUBJECT:
                break;

            case self::COMPLETION:
                break;

            default:
                break;
        }
        return view('tablet.student.detail', ['data' => $data, 'tab' => $request->tab, 'ledger_id' => $request->ledger_id]);
    }

    private function getAptitude($ledgerId)
    {
        $data = AptitudeDriving::with(['schoolStaffs'])->where('ledger_id', $ledgerId)
            ->where('status', Status::ENABLED)->orderBy('test_date', 'desc')->get();
        return $data;
    }
}
