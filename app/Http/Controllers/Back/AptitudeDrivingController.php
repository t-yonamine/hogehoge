<?php

namespace App\Http\Controllers\Back;

use App\Enums\ResultCharacter;
use App\Enums\TestType;
use App\Enums\Seq;
use App\Enums\SchoolStaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\AptitudeDriving\AptitudeDrivingRequest;
use App\Models\AdmCheckItem;
use App\Models\AptitudeDriving;
use App\Models\Ledger;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;

class AptitudeDrivingController extends Controller
{
    protected const MAX_FILE_SIZE = 100 * 1024; // 100kb
    protected const HEADER = [
        '実施日', '整理番号', '氏名', '性別', '年齢', '性格パターン１', '性格パターン２',
        '運転適性度', '安全運転度', '特異反応', '注意力', '判断力', '柔軟性', '決断力', '緻密性', '動作の安定性', '適応性', '身体的健康度', '精神的健康度', '社会的成熟度',
        '情緒不安定性', '衝迫性・暴発性', '自己中心性', '神経質・過敏性', '虚飾性', '運転マナー', '処理区分'
    ];
    protected const SEPARATOR = ',';
    protected const NEWLINE = "\n";
    protected const ARRAY = [
        'date', 'student_no', 'name', 'gender', 'age', 'od_persty_pattern_1', 'od_persty_pattern_2', 'od_drv_aptitude', 'od_safe_aptitude', 'od_specific_rxn',
        'od_a', 'od_b', 'od_c', 'od_d', 'od_e', 'od_f', 'od_g', 'od_h', 'od_i', 'od_j', 'od_k', 'od_l', 'od_m', 'od_n', 'od_o', 'od_p'
    ];

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $role = $user->schoolStaff->role;

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
    public function upload(Request $request)
    {
        $user = Auth::user();
        $placeFirst = 0;
        $dataGet = [];
        //CSVを読み込み入力チェックをする
        $file = $request->file('files');
        $request->validate(
            [
                'files' => 'required|file|mimes:csv'
            ],
            [
                'files' => __('messages.MSE00006')
            ]
        );
        //取込の上限は、100kbとする。サイズは要相談
        if (filesize($file) >= self::MAX_FILE_SIZE) {
            return back()->withErrors(['files' => Lang::get('messages.MSE00005', ['label' => '100'])]);
        }
        $content = File::get($file);
        if (!$content) {
            return redirect()->route('aptitude-driving.importFile')->with('error', Lang::get('messages.MSE00007'));
        }
        $data = str_getcsv($content, self::NEWLINE);
        foreach ($data as $key => $item) {
            $arrChildData = str_getcsv($item, self::SEPARATOR);
            //先頭行はヘッダーとして扱う
            if ($key === $placeFirst) {
                //ヘッダーの項目数をチェック
                if (count($arrChildData) !== count(self::HEADER)) {
                    return back()->withErrors(['files' => Lang::get('messages.MSE00010', ['label' => 'File'])]);
                }
                foreach ($arrChildData as $val) {
                    if (!in_array($val, self::HEADER)) {
                        return back()->withErrors(['files' => Lang::get('messages.MSE00010', ['label' => 'File'])]);
                    }
                }
            } else {
                $childData = [];
                $validate = [];
                $msgError = [];
                $arrMsg = '';
                //読み込んだCSVを一覧の各項目に値をセットする
                foreach (self::ARRAY as $key => $value) {
                    $childData[$value] = $arrChildData[$key] ?? '';
                }
                //各項目のチェック
                //各項目のサイズ
                if (count($arrChildData) !== count(self::HEADER)) {
                    array_push($validate, 'error data');
                } else if ($childData['student_no']) {
                    //各項目のサイズ、形式、文字種などをチェックする
                    $error = $this->validator($childData);
                    if ($error->fails()) {
                        $arrMsg = $error->errors()->first('date');
                        array_push($msgError, Lang::get('messages.MSE00008'));
                    }
                    // 整理番号に一致する教習生の存在をチェック
                    $id = $childData['student_no'];
                    $checkIdLedger = Ledger::where('school_id', $user->schoolStaff->school_id)->where('student_no', $id)->where('status', Status::ENABLED)->first();
                    if (!$checkIdLedger) {
                        array_push($msgError, Lang::get('messages.MSE00009'));
                    }
                }

                $childData['error'] = join(self::NEWLINE, $msgError);
                if (!$arrMsg) {
                    $childData['date'] = date('Y/m/d', strtotime($childData['date']));
                }
                $childData['is_save'] = $childData['error'] ? true : false;
                array_push($dataGet, $childData);
            }
        }
        return view('back.aptitude-driving.import-file', ['data' => $dataGet, 'fileName' => $file->getClientOriginalName()]);
    }

    // 各項目のサイズ、形式、文字種などをチェックする
    private function validator($data)
    {
        $validAtoC = join(self::SEPARATOR, ResultCharacter::getKeys([ResultCharacter::A, ResultCharacter::B, ResultCharacter::C]));
        $validAtoE = $validAtoC . join(self::SEPARATOR, ResultCharacter::getKeys([ResultCharacter::D, ResultCharacter::E]));
        $rule = [
            'date' => 'required|date_format:"Ymd"',
            'student_no' => 'required|numeric|max:8|min:1',
            'name' => 'nullable|string|max:128',
            'gender' => 'nullable|string|in:男,女',
            'age' => 'nullable|int',
            'od_persty_pattern_1' => 'required|int|max:2',
            'od_persty_pattern_2' => 'required|int|max:2',
            'od_drv_aptitude' => 'required|in:1,2,3,4,5',
            'od_safe_aptitude' => 'required|in:' . $validAtoE,
            'od_specific_rxn' => 'required|in:1,2,3',
            'od_a', 'od_b', 'od_c', 'od_d', 'od_e', 'od_f', 'od_g' => 'required|in:' . $validAtoE,
            'od_h', 'od_i', 'od_j', 'od_k', 'od_l', 'od_m', 'od_n', 'od_o', 'od_p' => 'required|in:' . $validAtoC,
        ];
        $attributes = [
            'date' => '実施日',
            'student_no' => '整理番号',
            'name' => '氏名',
            'gender' => '性別',
            'age' => '年齢',
            'od_persty_pattern_1' => '性格パターン1',
            'od_persty_pattern_2' => '性格パターン2',
            'od_drv_aptitude' => '運転適性度',
            'od_safe_aptitude' => '安全運転度',
            'od_specific_rxn' => '特異反応',
            'od_a' => '注意力',
            'od_b' => '判断力',
            'od_c' => '柔軟性',
            'od_d' => '決断力',
            'od_e' => '緻密性',
            'od_f' => '動作の安定性',
            'od_g' => '適応性',
            'od_h' => '身体的健康度',
            'od_i' => '精神的健康度',
            'od_j' => '社会的成熟度',
            'od_k' => '情緒不安定性',
            'od_l' => '衝迫性・暴発性',
            'od_m' => '自己中心性',
            'od_n' => '神経質・過敏性',
            'od_o' => '虚飾性',
            'od_p' => '運転マナー',
        ];
        $validator = Validator::make($data, $rule, [], $attributes);
        return $validator;
    }

    /**
     * @Route('/aptitude-driving/insert', method: 'POST', name: 'aptitude-driving.insert')
     * 保存処理
     */
    public function insert(Request $request)
    {
        $data = [];
        $user = Auth::user();
        if (!$request->req) {
            return abort(404);
        }
        foreach ($request->req as $item) {
            $dataTemp = $item;
            //除外のチェックがONのレコードは保存しない
            //除外のチェックがOFFのレコードが保存対象データ
            if ($item['is_save'] == "false") {
                unset($item['error']);
                unset($item['is_save']);
                $item['date'] = date('Ymd', strtotime($item['date']));
                //各項目のチェック
                $validate = $this->validator($item);
                $checkIdLedger = Ledger::where('student_no', $item['student_no'])->where('status', Status::ENABLED)->first();
                if ($validate->fails()) {
                    $dataTemp['error'] = Lang::get('messages.MSE00008');
                } else if ($checkIdLedger) {
                    //不要なアイテムの追加と削除
                    $item['ledger_id'] = $checkIdLedger->id;
                    $item['seq'] = Seq::FIRST1;
                    $item['school_id'] = $checkIdLedger->school_id;
                    $item['test_type'] = TestType::OD;
                    $item['test_date'] = date('Y/m/d', strtotime($item['date']));
                    $item['score'] = $item['od_drv_aptitude'] . $item['od_safe_aptitude'];
                    $item['status'] = Status::ENABLED;
                    unset($item['date']);
                    unset($item['name']);
                    unset($item['gender']);
                    unset($item['age']);
                    unset($item['student_no']);
                    //運転適性検査結果を保存する
                    AptitudeDriving::handleSaveFile($item, $user->id, null);
                    $dataTemp['success'] = Lang::get('messages.MSI00005');
                    $dataTemp['is_save'] = true;
                } else {
                    $dataTemp['error'] = Lang::get('messages.MSE00009');
                }
            }
            array_push($data, $dataTemp);
        }
        return response()->json(['status' => 200, 'data' => $data]);
    }
}
