<?php

namespace App\Http\Controllers\Back;

use App\Enums\CodeName;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\AdmCheckItem;
use App\Models\Code;
use App\Models\SchoolCode;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Rabianr\Validation\Japanese\Rules\Katakana;

class StudentController extends Controller
{
    private const GRADUATION = 7; // 7:卒業

    public function __construct()
    {
        // 1.権限チェック	
        $this->middleware(function (Request $request, Closure $next) {

            $user = Auth::user();

            //A. 教習原簿とログインユーザーの教習所一致確認 共通ロジック/権限チェック#2
            // B. ログインユーザーの役割がシステム管理者の役割のみしかない場合はエラー																
            $schoolId =  $request->session()->get('school_id');
            if (!$schoolId || $schoolId != $user->school_id || !($user->schoolStaff->role & SchoolStaffRole::SYS_ADMINISTRATOR)) {
                abort(403);
            }

            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * @Route('/', method: 'GET', name: 'student.index')
     */
    public function index(Request $request)
    {
        $school_id =  $request->session()->get('school_id');

        // 2.検索項目の車種を取得する
        $schoolCodes = SchoolCode::where('school_id', $school_id)
            ->where('cd_name', CodeName::LICENSE_TYPE)
            ->where('status', Status::ENABLED)
            ->orderBy('cd_value')->get();

        // custom validate student_no & name_kana
        $request->validate(
            [
                'student_no' => 'nullable|regex:/^[a-zA-Z0-9]+$/',
                'name_kana' => ['nullable', new Katakana],
            ],
            [
                'student_no' => __('messages.MSE00004', ['label' => '教習生番号']),
                'name_kana' => __('messages.MSE00004', ['label' => 'フリガナ']),
            ]
        );

        if ($request['is_search']) {
            if ($request['lesson_sts']) {
                $params = $request->input();
            } else {
                $params = array_merge([
                    'lesson_sts' => false,
                ], $request->input());
            }
        } else {
            $params = array_merge([
                'lesson_sts' => true,
            ], $request->input());
        }

        // 検索処理
        $models =  AdmCheckItem::buildQuery($request->input())
            ->join('gcertificates',  'gadm_check_items.id', '=', 'gcertificates.adm_check_items_id')
            ->whereHas('ledger', function ($q) use ($params) {
                $q->where('status', Status::ENABLED());
                if ($params['lesson_sts']) {
                    $q->where('lesson_sts', '<>', self::GRADUATION);
                } else {
                    $q->where('lesson_sts', self::GRADUATION);
                }
            })
            ->where('gadm_check_items.school_id', $school_id)
            ->where('gadm_check_items.status', Status::ENABLED())
            ->orderBy('gadm_check_items.admission_date')->paginate();

        $codes = Code::where('cd_name', CodeName::LESSON_STS)->where('status', Status::ENABLED)->get();

        return  view('back.student.index', ['codeOptions' => $schoolCodes, 'data' => $models, 'lessonSts' => $params['lesson_sts'], 'codes' => $codes]);
    }
}
