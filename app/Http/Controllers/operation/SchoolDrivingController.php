<?php

namespace App\Http\Controllers\operation;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\SchoolDriving\SchoolDrivingRequest;
use App\Http\Requests\SchoolDriving\SchoolDrivingCreateRequest;
use App\Models\School;
use App\Models\SchoolStaff;
use App\Models\User;
use Illuminate\Http\Request;

class SchoolDrivingController extends Controller
{
    const PASSWORD_DEFAULT = '';

    /**
     * @Route('/school-driving', method: 'GET', name: 'school-driving.index')
     */
    public function index(Request $request)
    {
        $models = School::buildQuery($request->input())->where('status', Status::ENABLE)
            ->orderBy('school_cd')->paginate();
        return view('operation.school-driving.index', ['models' => $models]);
    }

    /**
     * @Route('/school-driving/{id}', method: 'DELETE', name: 'school-driving.delete')
     */
    public function delete($id)
    {
        $model = School::where('id', $id)->first();
        if (!$model) {
            return redirect()->route('school-driving.index')->with('error', 'データは削除されました。または存在していません。');
        } else {
            try {
                School::handleDelete($model);
            } catch (\Throwable $th) {
                throw $th;
            }
        }
        return redirect()->route('school-driving.index')->with('success', 'データを削除しました。');
    }

    /**
     * @Route('/school-driving', method: 'GET', name: 'school-driving.detail')
     */
    public function detail($id)
    {
        // ・教習所情報取得
        $school = School::where('id', $id)->where('status', Status::ENABLE)->first();
        if (!$school) {
            abort(404);
        }

        // get info システム管理者
        $schoolStaff = SchoolStaff::where('school_id', $school->id)->where('role', Role::SYS_ADMINISTRATOR)->first();
        if (!$schoolStaff) {
            abort(404);
        }

        $user = User::where('id', $schoolStaff->id)->first();
        if (!$user) {
            abort(404);
        }

        $modelResponse = [
            'id' =>  $school->id,
            'school_cd' =>  $school->school_cd,
            'name' =>  $school->name,
            'name_kana' =>  $school->name_kana,
            'user_id' =>  $schoolStaff->id,
            'login_id' =>  $user->login_id,
            'password' => self::PASSWORD_DEFAULT,
            'school_staff_no' => $schoolStaff->school_staff_no,
            'school_staff_name' => $schoolStaff->name
        ];

        return view('operation.school-driving.edit', ['model' => $modelResponse]);
    }

    /**
     * @Route('/school-driving', method: 'PUT', name: 'school-driving.edit')
     */
    public function edit(SchoolDrivingRequest $request)
    {
        // ・教習所情報取得
        $school = School::where('id', '<>', $request->id)->where('school_cd', $request->school_cd)->first();
        if ($school) {
            return back()->withErrors(['school_cd' => '同じ教習所CDは既に存在します。']);
        }
        $schoolModel = School::where('id', $request->id)->first();
        if (!$schoolModel) {
            abort(404);
        }

        // get info システム管理者 for gusers
        $user = User::where('id', $request->user_id)->first();
        if (!$user) {
            abort(404);
        }

        // get info システム管理者 for gschool_staff
        $schoolStaff = SchoolStaff::where('id', $request->user_id)->first();
        if (!$schoolStaff) {
            abort(404);
        }

        try {
            School::handleSave($request->input(), $user, $schoolStaff, $schoolModel);
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->route('school-driving.index')->with(['success' => '編集しました。']);
    }
    /**
     * @Route('/school-driving/create', method: 'GET', name: 'school-driving.create')
     */
    public function create()
    {
        $modelResponse = [
            'id' =>  null,
            'school_cd' =>  null,
            'name' =>  null,
            'name_kana' =>  null,
            'user_id' =>  null,
            'login_id' =>  null,
            'password' => null,
            'school_staff_no' => null,
            'school_staff_name' => null
        ];
        return view('operation.school-driving.create', ['model' => $modelResponse]);
    }

    /**
     * @Route('/school-driving/store', method: 'POST', name: 'school-driving.store')
     */
    public function store(SchoolDrivingCreateRequest $request)
    {
        // ・存在チェック
        $school = School::where('school_cd', $request->school_cd)->first();
        if ($school) {
            return back()->withErrors(['school_cd' => '教習所CDは既に存在します。']);
        }

        try {
            School::handleCreate($request->input());
        } catch (\Throwable $th) {
            throw $th;
        }

        return redirect()->route('school-driving.index')->with(['success' => '登録しました。']);
    }
}
