<?php

namespace App\Http\Controllers\Back;

use App\Enums\SchoolStaffRole;
use App\Enums\Seq;
use App\Enums\Status;
use App\Enums\TestType;
use App\Http\Controllers\Controller;
use App\Http\Requests\AptitudeDriving\AptitudeDrivingRequest;
use App\Http\Requests\ImportCsvRequest;
use App\Models\AdmCheckItem;
use App\Models\AptitudeDriving;
use App\Models\Ledger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use SplFileObject;

class AptitudeDrivingController extends Controller
{
    protected const MAX_FILE_SIZE = 100 * 1024; // 100kb

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $role = $user->schoolStaff?->role;

            if ($request->routeIs('aptitude-driving.new', 'aptitude-driving.create')) {
                // A. 教習原簿IDの存在チェック 共通ロジック/存在チェック#3
                $ledger = Ledger::where('id', $request->ledger_id)->first();
                if (!$ledger) {
                    abort(404);
                }
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

    /**
     * @Route('/aptitude-driving', method: 'GET', name: 'aptitude-driving.importFile')
     */

    public function importFile($data = null, $fileName = '')
    {
        return view('back.aptitude-driving.import-file', ['data' => $data, 'fileName' => $fileName]);
    }

    /**
     * @Route('/aptitude-driving', method: 'POST', name: 'aptitude-driving.upload')
     */
    public function upload(ImportCsvRequest $request)
    {
        //CSVを読み込み入力チェックをする
        $file = $request->file('files');
        //取込の上限は、100kbとする。サイズは要相談
        if (filesize($file) >= self::MAX_FILE_SIZE) {
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
            $data = AptitudeDriving::readCsv($files);
        } catch (\Throwable $th) {
            return back()->withErrors(['files' => Lang::get('messages.MSE00010', ['label' => 'File'])]);
        }
        return view('back.aptitude-driving.import-file', ['data' => $data, 'fileName' => $file->getClientOriginalName()]);
    }

    /**
     * @Route('/aptitude-driving/insert', method: 'POST', name: 'aptitude-driving.insert')
     * 保存処理
     */
    public function insert(Request $request)
    {
        try {
            if (!$request->req) {
                return abort(404);
            }
            $data = AptitudeDriving::insertFromTable($request->req);
        } catch (\Throwable $th) {
            throw  $th;
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
