<?php

namespace App\Http\Controllers\Back;

use App\Enums\CertType;
use App\Enums\CodeName;
use App\Enums\SchoolStaffRole;
use App\Enums\Seq;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Students\StudentRequest;
use App\Models\AdmCheckItem;
use App\Models\AptitudePhysical;
use App\Models\Certificate;
use App\Models\Code;
use App\Models\Ledger;
use App\Models\SchoolCode;
use App\Models\SchoolStaff;
use App\Models\SystemValue;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
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
    /**
     * @Route('/{id}', method: 'GET', name: 'student.detail')
     */
    public function detail(Request $request)
    {
        $schoolId =  $request->session()->get('school_id');
        $ledgerId = $request->id;
        $checkIsLedgerId = Ledger::where('id', $ledgerId)->first();
        if (!$checkIsLedgerId) {
            return abort(404);
        }
        $student = Ledger::where('school_id', $schoolId)->where('id', $ledgerId)->first();
        if (!$student) {
            return abort(401);
        }

        $personalInfor = AdmCheckItem::with(['certificates'])->where('ledger_id', $ledgerId)->where('school_id', $schoolId)->first();
        $disable = true;
        $infor = (object)[
            'student_no' => $student->student_no,
            'adm_check_item' => $personalInfor,
            'id' => $ledgerId,
            'disable' => $disable
        ];
        return view('back.student.detail', ['infor' => $infor]);
    }

    /**
     * @Route('/update', method: 'POST', name: 'student.update')
     */
    public function show(Request $request)
    {
        try {
            $schoolId =  $request->session()->get('school_id');
            $ledgerId = $request->id;
            $checkIsLedgerId = Ledger::where('id', $ledgerId)->first();
            if (!$checkIsLedgerId) {
                return abort(404);
            }

            $student = Ledger::where('school_id', $schoolId)->where('id', $ledgerId)->first();
            if (!$student) {
                return abort(401);
            }
            $personalInfor = AdmCheckItem::with(
                [
                    'certificates', 
                    'curLicTypes', 
                    'aptitudePhys', 
                    'aptitudePhys.confirmationRecs'
                ])
            ->where('ledger_id', $ledgerId)->where('school_id', $schoolId)->orderBy('created_at', 'DESC')->first();
    
            $cerificateOfCompletion = new Certificate();
            $cerificateGraduation = new Certificate();
            $cerificateProvisionalLicense = [];

            foreach ($personalInfor->certificates as $item) {
                if ($item->cert_type == CertType::CERTIFICATE_COMPLETION) {
                    $cerificateOfCompletion = $item;
                    continue;
                }
                if ($item->cert_type == CertType::GRADUATION_CERTIFICATE) {
                    $cerificateGraduation = $item;
                    continue;
                }
                if ($item->cert_type == CertType::PROVISIONAL_LICENSE) {
                    $cerificateProvisionalLicense[] = $item;
                    continue;
                }
            }

            $aptitudePhys = $personalInfor->aptitudePhys;

            $aptitudePhyFirst1 = new AptitudePhysical();
            $aptitudePhyFirst2 = new AptitudePhysical();

            foreach ($aptitudePhys as $item) {
                if ($item->seq == Seq::FIRST1()) {
                    $aptitudePhyFirst1 = $item;
                    continue;
                }
                if ($item->seq == Seq::FIRST2()) {
                    $aptitudePhyFirst2 = $item;
                    continue;
                }
            }

            $lessonLimitMonth = SystemValue::where('sv_key', AdmCheckItem::LESSON_LIMIT_MONTH_KEY)->first();
            $policeDepartment = Code::where('cd_name', AdmCheckItem::PSC_CD)->get();
            $testerList = SchoolStaff::where('role', '&' , SchoolStaffRole::APTITUDE_TESTER)->where('status', Status::ENABLED())->get();

            $infor = (object)[
                'studentNo' => $student->student_no,
                'admCheckItem' => $personalInfor,
                'id' => $ledgerId,
                'certificates' => $personalInfor->certificates,
                'cerificateOfCompletion' => $cerificateOfCompletion,
                'cerificateGraduation' => $cerificateGraduation,
                'cerificateProvisionalLicense' => $cerificateProvisionalLicense,
                'aptitudePhys' => $aptitudePhys,
                'aptitudePhyFirst1' => $aptitudePhyFirst1,
                'aptitudePhyFirst2' => $aptitudePhyFirst2,
                'lessonLimitMonth' => $lessonLimitMonth,
                'policeDepartment' => $policeDepartment,
                'testerList' => $testerList
            ];
            return view('back.student.update', ['infor' => $infor]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }

    /**
     * @Route('/update', method: 'POST', name: 'student.update')
     */
    public function update(StudentRequest $request)
    {
        try {

            $input = $request->input();
            $schoolId =  $request->session()->get('school_id');
            $ledgerId = $request->id;
            $checkIsLedgerId = Ledger::where('id', $ledgerId)->first();
            if (!$checkIsLedgerId) {
                return abort(404);
            }
            $student = Ledger::where('school_id', $schoolId)->where('id', $ledgerId)->first();
            if (!$student) {
                return abort(401);
            }
            $personalInfor = AdmCheckItem::with(['certificates', 'aptitudePhys', 'curLicTypes'])
            ->where('ledger_id', $ledgerId)->where('school_id', $schoolId)->orderBy('created_at', 'DESC')->first();
            
            //免許証番号を入力したとき、先頭2桁でgcodesのpsc_cdを検索、設定。
            $existCode = Code::where('cd_name', AdmCheckItem::PSC_CD)->where('cd_value', $input['lic_num'])->first();
            if (!$existCode) {
                $input['lic_psc_name'] = '';
                return back()->withErrors(['lic_num' =>  Lang::get('messages.MSE00002')])->withInput($input);
            }

            // handle save
            $input['lic_psc_name'] = $existCode->cd_text;
            AdmCheckItem::handleUpdateStudent($input, $personalInfor);

            // return view('back.student.update', ['infor' => $infor]);
            return redirect()->route('student.update', $ledgerId)->with(['success' => Lang::get('messages.MSI00003')]);
        } catch (\Throwable $th) {
            throw $th;
        }
    }
}
