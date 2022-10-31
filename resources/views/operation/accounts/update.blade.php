@extends('adminlte::page')

@section('title', '運営アカウント詳細')

@section('content_header')
    <h1>運営アカウント詳細</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div>
                <div class="card">
                    <form class="flex flex-col w-full" method="post" autocomplete="off"
                        action="{{ route('accounts.update', [$dataStaff->id]) }}">
                        @csrf
                        <div class="card-body">
                            <table class="table table-bordered table-view">
                                <tbody>
                                    <tr>ユーザー情報</tr>
                                    <tr>
                                        <th class="w-20">ログインID</th>
                                        <td><input name="login_id" type="text" class="form-control" placeholder=""
                                                value="{{ old('login_id', $dataUser->login_id) }}" disabled></td>
                                    </tr>
                                    <tr>
                                        <th class="w-20">パスワード</th>
                                        <td>
                                            <input name="password" type="password"
                                                class="form-control  @error('password') is-invalid @enderror" placeholder=""
                                                maxlength="20" autocomplete="off">
                                            @error('password')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-body">
                            <table class="table table-bordered table-view">
                                <tbody>
                                    <tr>担当者情報</tr>
                                    <tr>
                                        <th class="w-20">担当者番号
                                            <x-text-required />
                                        </th>
                                        <td><input name="staff_no" type="text"
                                                class="form-control @error('staff_no') is-invalid @enderror" placeholder=""
                                                value="{{ old('staff_no', $dataStaff->staff_no) }}" maxlength="10">
                                            @error('staff_no')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="w-20">氏名
                                            <x-text-required />
                                        </th>
                                        <td><input name="name" type="text"
                                                class="form-control @error('name') is-invalid @enderror" placeholder=""
                                                value="{{ old('name', $dataStaff->name) }}" maxlength="128">
                                            @error('name')
                                                <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="col text-right p-0">
                                <button class="btn btn-sm btn-secondary" type="submit">
                                    保存
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop
