<?php

namespace App\Http\Controllers\Front;

use App\Enums\SchoolStaffRole;
use App\Http\Controllers\Controller;
use App\Models\SchoolPeriodM;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    protected const CODETYPE = 'course_type';

    public function __construct()
    {
        // バリデーションチェック(サイズ、形式、文字種等)を行う。
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            if (!$user->school_id ||$user->school_id !== session('school_id')) {
                abort(403);
            }
            $role = $user->schoolStaff->role;
            // 本画面は、指導員か検定員の権限が必要。
            if (($role & (SchoolStaffRole::INSTRUCTOR + SchoolStaffRole::EXAMINER)) == 0) {
                abort(403);
            }
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    public function index(Request $request)
    {
        $request->validate([
            'datepicker' => 'nullable|max:10|date',
        ]);
        //1. 入力パラメータ																										
        //   A. セッション情報 共通ロジック/セッション情報																										
        //   2. 日付 に当日を設定して、日付処理へ。																										
        $date = $request->input('datepicker') ?? date('Y-m-d');
        $user = Auth::user();
        $sessSchoolStaffId = $request->session()->get('school_staff_id');
        $periodM = SchoolPeriodM::with([
            'period' => function ($q) use ($user, $date) {
                $q->where('school_id', $user->schoolStaff->school_id)
                    ->where('period_date', $date)->where('school_staff_id', $user->schoolStaff->id);
            },
            'period.lessonAttend',
            'period.lessonAttend.admCheckItem',
            'period.lessonAttend.lessonItemMastery',
            'period.codes' => function ($q) {
                $q->where('cd_name', self::CODETYPE);
            },
            'period.lessonAttend.dsipatchCar',
            'period.lessonAttend.dsipatchCar.lessonCar'
        ])->orderBy('period_num')->get();

        return view('tablet.home.index', ['period_m' => $periodM, 'datepicker' => $date, 'sessSchoolStaffId' => $sessSchoolStaffId]);
    }
}
