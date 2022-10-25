{{-- define common form for school-driving  --}}
<form class="flex flex-col w-full" method="POST" id="form-search" action="{{ $route }}" autocomplete="off">
    @method('PUT')
    @csrf
    <input type="hidden" name="id" value="{{ old('id', $model['id']) }}">
    {{-- Driving school information --}}
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>教習所情報</tr>
                <tr>
                    <th class="w-20">教習所CD
                        <x-text-required />
                    </th>
                    <td>
                        <input name="school_cd" type="text"
                            class="form-control @error('school_cd') is-invalid @enderror" placeholder="" maxlength="4"
                            value="{{ old('school_cd', $model['school_cd']) }}">
                        @error('school_cd')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">教習所名
                        <x-text-required />
                    </th>
                    <td>
                        <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                            placeholder="" maxlength="32" value="{{ old('name', $model['name']) }}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">フリガナ
                        <x-text-required />
                    </th>
                    <td>
                        <input name="name_kana"type="text"
                            class="form-control @error('name_kana') is-invalid @enderror" placeholder="" maxlength="64"
                            value="{{ old('name_kana', $model['name_kana']) }}" autocomplete="off">
                        @error('name_kana')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- School system administrator --}}
    <input type="hidden" name="user_id" value="{{ old('user_id', $model['user_id']) }}">
    <input type="hidden" name="login_id" value="{{ old('login_id', $model['login_id']) }}">
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>教習所システム管理者</tr>
                <tr>
                    <th class="w-20">ログインID</th>
                    <td><input name="login_id" type="text" class="form-control" placeholder="" maxlength="10"
                            disabled value="{{ old('login_id', $model['login_id']) }}"></td>
                </tr>
                <tr>
                    <th class="w-20">パスワード</th>
                    <td>
                        {{ old('password', $model['password']) }}
                        <input name="password" type="password"
                            class="form-control @error('password') is-invalid @enderror" placeholder="" maxlength="8"
                            value="{{ old('password', $model['password']) }}">
                        @error('password')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">職員番号
                        <x-text-required />
                    </th>
                    <td>
                        <input name="school_staff_no"type="text"
                            class="form-control @error('school_staff_no') is-invalid @enderror" placeholder=""
                            maxlength="10" value="{{ old('school_staff_no', $model['school_staff_no']) }}">
                        @error('school_staff_no')
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
                    <td>
                        <input name="school_staff_name"type="text"
                            class="form-control @error('school_staff_name') is-invalid @enderror" placeholder=""
                            maxlength="128" value="{{ old('school_staff_name', $model['school_staff_name']) }}">
                        @error('school_staff_name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Setting information --}}
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>設定情報</tr>
                <tr>
                    <th class="w-20">教習生情報保存日数</th>
                    <td><input name="schools" type="text" class="form-control" placeholder="" maxlength="4"></td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <div class="col text-right p-0">
            <button class="btn btn-sm btn-secondary" type="submit"> 保存
            </button>
        </div>
    </div>
</form>
