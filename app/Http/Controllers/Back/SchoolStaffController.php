<?php

namespace App\Http\Controllers\Back;

use App\Enums\Status;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SchoolStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;
use App\Http\Requests\SchoolStaff\SchoolStaffRequest;
use Closure;

class SchoolStaffController extends Controller
{
    public function __construct()
    {
        //     ・権限チェック		
        // ログインユーザーが選択教習所のシステム管理者の役割を持っていることを確認	
        // システム管理者の役割を持ってない場合、403 Error. Forbden.	
        // 役割のチェックはビット演算でチェックする	
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            // ログインユーザー.role == 1:システム管理者
            Helper::checkRole($user->schoolStaff->role);
            if (!$user->school_id) {
                abort(403);
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
     * @Route('/', method: 'GET', name: 'school-staff.index')
     */
    public function index(Request $request)
    {

        $request->validate(
            [
                'school_staff_no' => 'nullable|regex:/^[a-zA-Z0-9]+$/'
            ],
            [
                'school_staff_no' => __('messages.MSE00004', ['label' => '職員番号'])
            ]
        );

        $school_id =  $request->session()->get('school_id');
        if (!$school_id) {
            return abort(403);
        }
        $user = Auth::user();
        Helper::checkRole($user->schoolStaff->role);

        $data = SchoolStaff::buildQuery($request->input())->where('school_id', $school_id)->where('status', Status::ENABLED())
            ->orderBy('school_staff_no', 'ASC')->paginate();

        return  view('back.school-staff.index', ['data' => $data]);
    }

    /**
     * @Route('/school-staff/{id}', method: 'DELETE', name: 'school-staff.delete')
     */
    public function delete($id)
    {
        $model = SchoolStaff::where('id', $id)->first();
        $authUser = Auth::user();
        $user = User::where('id', $id)->first();
        Helper::checkRole($authUser->schoolStaff->role);
        if (!$model) {
            return redirect()->route('school-staff.index')->with('error', Lang::get('messages.MSE00002'));
        } else {
            SchoolStaff::handleDelete($model, $user, $authUser);
        }
        return redirect()->route('school-staff.index')->with('success', Lang::get('messages.MSI00002'));
    }

    /**
     * @Route('/school-staff/{id}', method: 'GET', name: 'school-staff.show')
     */
    public function show($id)
    {
        $user = User::where('id', $id)->where('status', Status::ENABLED)->first();

        if (empty($user) || empty($user->schoolStaff)) {
            abort(404);
        }

        return view('back.school-staff.update', ['data' => $user->schoolStaff, 'user' => $user]);
    }

    /**
     * @Route('/school-staff/{id}', method: 'PUT', name: 'school-staff.update')
     */
    public function update(SchoolStaffRequest $request, $id)
    {
        try {
            $user = Auth::user();
            $schoolStaff = SchoolStaff::where('id', $id)->first();
            $userById = User::where('id', $id)->first();
            if (!$schoolStaff) {
                return back()->withErrors('school_staff_no', Lang::get('messages.MSE00002'));
            }
            if (!$userById) {
                return back()->withErrors('login_id', Lang::get('messages.MSE00002'));
            }
            SchoolStaff::handleSave($request->input(), $user->id, $userById, $schoolStaff);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('school-staff.show', [$id])->with('success', Lang::get('messages.MSI00002'));
    }
}
