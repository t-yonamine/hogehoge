<?php

namespace App\Http\Controllers\Back;

use App\Enums\LaType;
use App\Enums\ResultType;
use App\Enums\SchoolStaffRole;
use App\Enums\StageType;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\EffectMeasurements\EffectMeasurementRequest;
use App\Http\Requests\ImportCsvRequest;
use App\Models\Ledger;
use App\Models\LessonAttend;
use App\Models\SchoolStaff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use SplFileObject;

class EffectMeasurementController extends Controller
{
    protected const MAX_FILE_SIZE = 100 * 1024; // 100kb
    protected const MIN_FILE_SIZE = 0; // 0kb

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            if ($request->routeIs('effect-measurement.import', 'effect-measurement.import.upload', 'effect-measurement.import.insert')) {
                $user = Auth::user();
                // 効果測定の登録には、事務員2以上の権限が必要
                if (($user->schoolStaff->role & (SchoolStaffRole::CLERK_TWO + SchoolStaffRole::APTITUDE_TESTER + SchoolStaffRole::INSTRUCTOR + SchoolStaffRole::EXAMINER + SchoolStaffRole::SUB_ADMINISTRATOR + SchoolStaffRole::ADMINISTRATOR)) == 0) {
                    abort(403);
                }
            }
            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke()
    {
        return  view('back.effect-measurement.index');
    }

    /**
     * @Route('/effect-measurement/create/{ledger_id}', method: 'GET', name: 'effect-measurement.create')
     */
    public function create(Request $request, $ledger_id)
    {
        // 1. 入力パラメータ
        //   A. セッション情報 共通ロジック/セッション情報#1-3
        $school_staff_id =  $request->session()->get('school_staff_id');
        $school_id =  $request->session()->get('school_id');
        $school_cd =  $request->session()->get('school_cd');
        if (empty($school_staff_id) || empty($school_id) || empty($school_cd)) {
            abort(403);
        }

        //   B. param/ledger_id 遷移元から渡された教習原簿ID
        //   C. param/la_type 受講区分。遷移元で指定。仮免新規:2211、卒検新規:2221
        if (!isset($ledger_id) || !isset($request->la_type) || ($request->la_type != LaType::EFF_MEAS_1N && $request->la_type != LaType::EFF_MEAS_2N)) {
            abort(404);
        }
        // 2. 存在チェック
        //   A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3
        $data = Ledger::with('admCheckItem')->whereHas('admCheckItem', function ($q) {
            $q->where('status', Status::ENABLED());
        })->where('id', $ledger_id)->first();

        if (!isset($data)) {
            return abort(404);
        }

        // 3.権限チェック
        //   A. 教習原簿とセッションの教習所一致確認 共通ロジック
        if ($school_id != $data->school_id) {
            return abort(403);
        }

        //   B. ログインした人の役割チェック。事務員2以上が操作可能。
        $schoolStaff = SchoolStaff::find($school_staff_id);
        if (!isset($schoolStaff) || $schoolStaff->role < SchoolStaffRole::CLERK_TWO) {
            return abort(403);
        }

        return view('back.effect-measurement.create', ['data' => $data, 'laType' => $request->la_type, 'result' => ResultType::OK()->value]);
    }
    /**
     * @Route('/effect-measurement/create', method: 'POST', name: 'effect-measurement.store')
     */
    public function store(EffectMeasurementRequest $request)
    {
        // 1. 入力パラメータ
        //   A. セッション情報 共通ロジック/セッション情報#1-3
        $school_staff_id =  $request->session()->get('school_staff_id');
        $school_id =  $request->session()->get('school_id');
        if (empty($school_id) || empty($school_staff_id)) {
            abort(403);
        }

        $schoolStaff = SchoolStaff::where('id', $school_staff_id)->first();
        if (!$schoolStaff) {
            abort(403);
        }

        // 2.存在チェック
        //    A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3
        $ledger = Ledger::find($request->ledger_id);
        if (!$ledger) {
            abort(404);
        }

        try {
            $data = $request->input();
            $data['school_id'] = $school_id;
            $data['stage'] = $request->la_type > LaType::EFF_MEAS_1N ? StageType::STAGE_2() : StageType::STAGE_1();
            $data['school_staff_id'] = $schoolStaff->id;
            $data['period_to'] = $request->period_from;
            $data['created_user_id'] = $schoolStaff->id;
            $data['updated_user_id'] = $schoolStaff->id;

            LessonAttend::handleSave($data, $ledger, null);
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->route('effect-measurement.index', ['ledger_id' => $ledger->id])->with(['success' => Lang::get('messages.MSI00004')]);
    }

    /**
     * 入力パラメータ
     *    A. セッション情報 共通ロジック/セッション情報#1-3
     *    B. param/ledger_id 遷移元から渡された教習原簿ID
     * @Route('/effect-measurement/{ledger_id}', method: 'GET', name: 'effect-measurement.index')
     *
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
        $data = Ledger::with(['admCheckItem', 'lessonAttend' => function ($q) {
            $q->with('schoolStaff')->where('la_type', '>=', LaType::EFF_MEAS_MIN())
                ->where('la_type', '<=', LaType::EFF_MEAS_MAX())
                ->orderBy('period_date');
        }])->whereHas('admCheckItem', function ($q) {
            $q->where('status', Status::ENABLED());
        })->where('id', $id)->first();
        if (empty($data)) {
            abort(404);
        }
        return view('back.effect-measurement.index', ['data' => $data, 'lesson_attends' => $data->lessonAttend]);
    }

    /**
     * @Route('/effect-measurement/{ledger_id}', method: 'DELETE', name: 'effect-measurement.delete')
     */
    // 削除ボタン処理
    public function delete($id)
    {
        // 教習原簿IDの存在チェック
        $lessonAttend = LessonAttend::where('id', $id)->first();
        if (empty($lessonAttend)) {
            return back()->with('error', Lang::get('messages.MSE00002'));
        }
        //ボタン処理
        try {
            LessonAttend::handleDelete($lessonAttend);
        } catch (\Throwable $th) {
            throw $th;
        }
        return back()->with('success', Lang::get('messages.MSI00002'));
    }

    /**
     * @Route('/effect-measurement/import', method: 'GET', name: 'effect-measurement.import')
     */
    // 削除ボタン処理
    public function import($data = null, $fileName = '')
    {

        return view('back.effect-measurement.import', ['data' => $data, 'fileName' => $fileName]);
    }

    /**
     * @Route('/effect-measurement/upload', method: 'POST', name: 'effect-measurement.import.upload')
     */
    public function upload(ImportCsvRequest $request)
    {
        //CSVを読み込み入力チェックをする
        $file = $request->file('files');
        //取込の上限は、100kbとする。サイズは要相談
        if (filesize($file) >= self::MAX_FILE_SIZE || filesize($file) == self::MIN_FILE_SIZE) {
            return back()->withErrors(['files' => Lang::get('messages.MSE00005', ['label' => '100'])]);
        }

        $files = new SplFileObject($file->getPathName());
        $files->setFlags(
            SplFileObject::DROP_NEW_LINE |
                SplFileObject::READ_AHEAD |
                SplFileObject::SKIP_EMPTY |
                SplFileObject::READ_CSV
        );

        try {
            // read csv file
            $data = LessonAttend::readCsv($files);
        } catch (\Throwable $th) {
            return back()->withErrors(['files' => Lang::get('messages.MSE00010', ['label' => 'File'])]);
        }
        return view('back.effect-measurement.import', ['data' => $data, 'fileName' => $file->getClientOriginalName()]);
    }

    /**
     * @Route('/effect-measurement/insert', method: 'POST', name: 'effect-measurement.import.insert')
     * 保存処理
     */
    public function insert(Request $request)
    {
        try {
            if (!$request->req || !$request->fileName) {
                return abort(404);
            }

            $data = LessonAttend::insertFromTable($request->req, $request->fileName);
        } catch (\Throwable $th) {
            throw  $th;
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
