<?php

namespace App\Http\Controllers\operation;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\AccountsCreateRequest;
use App\Http\Requests\Accounts\AccountsUpdateRequest;
use App\Models\Staff;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

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
            return back()->withErrors(['login_id' => 'ログインIDは既に存在します。']);
        }

        // 運営側に同じ担当者番号が存在する場合はエラー
        $existStaff = Staff::where('staff_no', $request->staff_no)->first();
        if ($existStaff) {
            return back()->withErrors(['staff_no' => '担当者番号は既に存在します。']);
        }

        try {
            $dataUser['login_id'] = $request->login_id;
            $dataUser['password'] = Hash::make($request->password);
            $dataUser['status'] = Status::ENABLE;
            $dataUser['created_user_id'] = $user->id;

            $dataStaff['staff_no'] = $request->staff_no;
            $dataStaff['name'] = $request->name;
            $dataStaff['role'] = Role::STAFF_MANAGER;
            $dataStaff['status'] = Status::ENABLE;
            $dataStaff['created_user_id'] = $user->id;

            Staff::handleCreate($dataUser, $dataStaff, null, null);
        } catch (\Throwable $th) {
            throw $th;
        }
        return redirect()->route('accounts.index')->with(['success' => '登録しました。']);
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user = User::where('id', $id)->where('status', Status::ENABLE)->first();

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
                return back()->withErrors(['login_id' => 'ログインIDは既に存在します。']);
            }
            //　　運営側に選択ユーザー以外に同じ担当者番号が存在する場合はエラー
            $existStaff = Staff::where('staff_no', $request->staff_no)
                ->where('id', '!=', $id)->first();
            if ($existStaff) {
                return back()->withErrors(['staff_no' => '担当者番号は既に存在します。']);
            }
            Staff::handleSave($request->input(), $id, $user->id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return back()->with('success', '編集しました。');
    }
    /**
     * @Route('/accounts', method: 'GET', name: 'accounts.index')
     *　データ取得
     *　DBアクセスイメージ	
     */
    public function index()
    {
        $data = Staff::with(['user'])->where('status', Status::ENABLE)
            ->where('role', Role::STAFF_MANAGER)->orderBy('staff_no')->paginate();
        return view('operation.accounts.index', ['data' => $data]);
    }

    /**
     * @Route('/accounts/{$id}', method: 'DELETE', name: 'accounts.delete', parameters: {$id})
     */
    public function delete($id)
    {
        $model = Staff::with(['user'])->where('id', $id)->first();
        if (!$model || !$model->user) {
            return redirect()->route('accounts.index')->with('error', 'データは削除されました。または存在していません。');
        } else {
            Staff::handleDelete($model);
        }
        return redirect()->route('accounts.index')->with('success', '削除しました。');
    }
}
