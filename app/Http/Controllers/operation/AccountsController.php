<?php

namespace App\Http\Controllers\operation;

use App\Enums\StaffRole;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\AccountsCreateRequest;
use App\Http\Requests\Accounts\AccountsUpdateRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Lang;

class AccountsController extends Controller
{
    const PASSWORD_DEFAULT = '';

    /**
     * @Route('/accounts/create', method: 'GET', name: 'accounts.create')
     */
    public function create()
    {
        $dataResponse = [
            'password' => self::PASSWORD_DEFAULT,
        ];
        return view('operation.accounts.create',  ['data' => $dataResponse]);
    }

    /**
     * @Route('/accounts/create', method: 'POST', name: 'accounts.store')
     */
    public function store(AccountsCreateRequest $request)
    {
        $user = Auth::user();

        // 運営側に同じログインIDが存在する場合はエラー
        $existUserLogin = User::whereNull('school_id')
            ->where('login_id', $request->login_id)->first();
        if ($existUserLogin) {
            return back()->withErrors(['login_id' => __('messages.MSE00001', ['label' => 'ログインID'])]);
        }

        // 運営側に同じ担当者番号が存在する場合はエラー
        $existStaff = Staff::where('staff_no', $request->staff_no)->first();
        if ($existStaff) {
            return back()->withErrors(['staff_no' => __('messages.MSE00001', ['label' => '担当者番号'])]);
        }

        try {
            $dataUser['login_id'] = $request->login_id;
            $dataUser['password'] = Hash::make($request->password);
            $dataUser['status'] = Status::ENABLED();
            $dataUser['created_user_id'] = $user->id;

            $dataStaff['staff_no'] = $request->staff_no;
            $dataStaff['name'] = $request->name;
            $dataStaff['role'] = StaffRole::MANAGER();
            $dataStaff['status'] = Status::ENABLED();
            $dataStaff['created_user_id'] = $user->id;

            Staff::handleCreate($dataUser, $dataStaff, null, null);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('accounts.index')->with(['success' => Lang::get('messages.MSI00004')]);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->where('status', Status::ENABLED())->first();

        if (empty($user) || empty($user->staff)) {
            abort(404);
        }

        return  view('operation.accounts.update', ['dataStaff' => $user->staff, 'dataUser' => $user]);
    }

    /**
     * @Route('/accounts/{id}', method: 'POST', name: 'accounts.update')
     */
    public function update(AccountsUpdateRequest $request, $id)
    {
        try {
            $user = Auth::user();
            //　存在チェック
            //　　運営側に選択ユーザー以外に同じログインIDが存在する場合はエラー
            $existUserLogin = User::whereNull('school_id')
                ->where('login_id', $request->login_id)
                ->where('id', '!=', $id)->first();
            if ($existUserLogin) {
                return back()->withErrors(['login_id' => __('messages.MSE00001', ['label' => 'ログインID'])]);
            }
            //　　運営側に選択ユーザー以外に同じ担当者番号が存在する場合はエラー
            $existStaff = Staff::where('staff_no', $request->staff_no)
                ->where('id', '!=', $id)->first();
            if ($existStaff) {
                return back()->withErrors(['staff_no' => __('messages.MSE00001', ['label' => '担当者番号'])]);
            }
            Staff::handleSave($request->input(), $id, $user->id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return back()->with('success', Lang::get('messages.MSI00003'));
    }
    /**
     * @Route('/accounts', method: 'GET', name: 'accounts.index')
     *　データ取得
     *　DBアクセスイメージ
     */
    public function index()
    {
        $data = Staff::with(['user'])->where('status', Status::ENABLED())
            ->where('role', StaffRole::MANAGER())->orderBy('staff_no')->paginate();
        return view('operation.accounts.index', ['data' => $data]);
    }

    /**
     * @Route('/accounts/{$id}', method: 'DELETE', name: 'accounts.delete', parameters: {$id})
     */
    public function delete($id)
    {
        $model = Staff::with(['user'])->where('id', $id)->first();
        if (!$model || !$model->user) {
            return redirect()->route('accounts.index')->with('error', Lang::get('messages.MSE00002'));
        } else {
            Staff::handleDelete($model);
        }

        return redirect()->route('accounts.index')->with('success', Lang::get('messages.MSI00002'));
    }
}
