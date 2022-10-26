<?php

namespace App\Http\Controllers\operation;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Http\Requests\Accounts\AccountsUpdateRequest;
use App\Models\Staff;
use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountsController extends Controller
{
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
                return back()->withErrors('staff_no', '担当者番号が存在する。');
            }
            //　　運営側に選択ユーザー以外に同じ担当者番号が存在する場合はエラー
            $existStaff = Staff::where('staff_no', $request->staff_no)
                ->where('id', '!=', $id)->first();
            if ($existStaff) {
                return back()->withErrors('staff_no', '担当者番号が存在する。');
            }
            Staff::handleSave($request->input(), $id, $user->id);
        } catch (\Throwable $th) {
            throw $th;
        }
        return back()->with('success', '編集しました。');
    }
}