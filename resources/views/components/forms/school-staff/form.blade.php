@php
    $role = App\Enums\SchoolStaffRole::getValues();
    $arrRole = [
        ['value' => App\Enums\SchoolStaffRole::CLERK_ONE, 'id' => 'clerk_1', 'name' => '事務員1'],
        ['value' => App\Enums\SchoolStaffRole::CLERK_TWO, 'id' => 'clerk_2', 'name' => '事務員2'],
        ['value' => App\Enums\SchoolStaffRole::APTITUDE_TESTER, 'id' => 'aptitude_tester', 'name' => '適性検査員'],
        ['value' => App\Enums\SchoolStaffRole::INSTRUCTOR, 'id' => 'intructor', 'name' => '指導員'],
        ['value' => App\Enums\SchoolStaffRole::EXAMINER, 'id' => 'examiner', 'name' => '検定員'],
        ['value' => App\Enums\SchoolStaffRole::SUB_ADMINISTRATOR, 'id' => 'sub_admin', 'name' => '副管理者'],
        ['value' => App\Enums\SchoolStaffRole::ADMINISTRATOR, 'id' => 'admin', 'name' => '管理者']];
    $isRole = $errors->any() ? old('role') ?? [] : App\Helpers\Helper::getListRole($data->role, $role);
    $arrChecked = [
        ['name' => '取消処分者講習資格', 'id' => '1', 'value' => old('is_revoked', $data->is_revoked), 'key' => 'is_revoked'],
        ['name' => '初心', 'id' => '2', 'value' => old('is_beginner', $data->is_beginner), 'key' => 'is_beginner'],
        ['name' => '高齢者講習資格', 'id' => '3', 'value' => old('is_senior', $data->is_senior), 'key' => 'is_senior'],
        ['name' => '応急資格1種', 'id' => '4', 'value' => old('is_first_aid_1', $data->is_first_aid_1), 'key' => 'is_first_aid_1'],
        ['name' => '応急資格2種', 'id' => '5', 'value' => old('is_first_aid_2', $data->is_first_aid_2), 'key' => 'is_first_aid_2'],
        ['name' => '4輪シミュレータ', 'id' => '6', 'value' => old('is_sim_4', $data->is_sim_4), 'key' => 'is_sim_4'],
        ['name' => '2輪シミュレータ', 'id' => '7', 'value' => old('is_sim_2', $data->is_sim_2), 'key' => 'is_sim_2'],
        ['name' => '適性検査資格_1種', 'id' => '8', 'value' => old('is_aptitude_1', $data->is_aptitude_1), 'key' => 'is_aptitude_1'],
        ['name' => '適性検査資格_2種', 'id' => '9', 'value' => old('is_aptitude_2', $data->is_aptitude_2), 'key' => 'is_aptitude_2'],
        ['name' => '高速資格', 'id' => '10', 'value' => old('is_highway', $data->is_highway), 'key' => 'is_highway'],
        ['name' => '路上資格', 'id' => '11', 'value' => old('is_road', $data->is_road), 'key' => 'is_road'],
        ['name' => '無線資格', 'id' => '12', 'value' => old('is_wireless', $data->is_wireless), 'key' => 'is_wireless'],
    ];

    $arrDegree = [
        ['name' => '大型教習資格', 'id' => '1', 'value' => $data->lic_l_mvl, 'key' => 'lic_l_mvl'],
        ['name' => '中型教習資格', 'id' => '2', 'value' => $data->lic_m_mvl, 'key' => 'lic_m_mvl'],
        ['name' => '普通教習資格', 'id' => '3', 'value' => $data->lic_s_mvl, 'key' => 'lic_s_mvl'],
        ['name' => '大特教習資格', 'id' => '4', 'value' => $data->lic_sl_mvl, 'key' => 'lic_sl_mvl'],
        ['name' => '大型2輪教習資格', 'id' => '5', 'value' => $data->lic_l_ml, 'key' => 'lic_l_ml'],
        ['name' => '普通2輪教習資格', 'id' => '6', 'value' => $data->lic_s_ml, 'key' => 'lic_s_ml'],
        ['name' => '準中型教習資格', 'id' => '7', 'value' => $data->lic_sm_mvl, 'key' => 'lic_sm_mvl'],
        ['name' => '牽引教習資格', 'id' => '8', 'value' => $data->lic_towing, 'key' => 'lic_towing'],
        ['name' => '大型2種教習資格', 'id' => '9', 'value' => $data->lic_l_mvl_2, 'key' => 'lic_l_mvl_2'],
        ['name' => '中型2種教習資格', 'id' => '10', 'value' => $data->lic_m_mvl_2, 'key' => 'lic_m_mvl_2'],
        ['name' => '普通2種教習資格', 'id' => '11', 'value' => $data->lic_s_mvl_2, 'key' => 'lic_s_mvl_2'],
    ];
@endphp
<form class="flex flex-col w-full" method="POST" id="form-search" autocomplete="off" action="{{ $route }}">
    @csrf
    @method($method)
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>ユーザー情報</tr>
                <tr>
                    <th class="w-20">ログインID
                        @if ($isCreate)
                            <x-text-required />
                        @endif
                    </th>
                    <td><input name="login_id" type="text" class="form-control @error('login_id') is-invalid @enderror"
                            @if (!$isCreate) disabled @endif placeholder="" maxlength="16"
                            value="{{ old('login_id', $user->login_id) }}">
                        @if ($isCreate)
                            @error('login_id')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        @endif
                    </td>
                </tr>
                <tr>
                    <th class="w-20">パスワード
                        @if ($isCreate)
                            <x-text-required />
                        @endif
                    </th>
                    <td>
                        <input name="password" type="password"
                            class="form-control  @error('password') is-invalid @enderror" placeholder="" maxlength="20"
                            autocomplete="off">
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
                <tr>職員情報</tr>
                <tr>
                    <th class="w-20">職員番号
                        @if ($isCreate)
                            <x-text-required />
                        @endif
                    </th>
                    <td><input name="school_staff_no"
                            class="form-control @error('school_staff_no') is-invalid @enderror"
                            @if (!$isCreate) disabled @endif type="text" maxlength="10"
                            placeholder="" value="{{ old('school_staff_no', $data->school_staff_no) }}">
                        @error('school_staff_no')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">職員氏名
                        <x-text-required />
                    </th>
                    <td>
                        <input name="name" type="name" class="form-control @error('name') is-invalid @enderror"
                            placeholder="" maxlength="128" autocomplete="off" value="{{ old('name', $data->name) }}">
                        @error('name')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">役割
                        <x-text-required />
                    </th>
                    <td>
                        <div class="border d-flex rounded @error('role') is-invalid @enderror">
                            @foreach ($arrRole as $item)
                                <div class="form-check m-2">
                                    <input class="form-check-input" type="checkbox" value="{{ $item['value'] }}"
                                        name="role[]" id={{ $item['id'] }} @checked(array_search($item['value'], $isRole) !== false)>
                                    <label class="form-check-label" for={{ $item['id'] }}>
                                        {{ $item['name'] }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('role')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">免許有効期限</th>
                    <td>
                        <input name="lic_expy_date" type="date"
                            class="form-control  @error('lic_expy_date') is-invalid @enderror w-25"
                            value={{ old('lic_expy_date', $data->lic_expy_date) }} placeholder="" maxlength="8"
                            autocomplete="off">
                        @error('lic_expy_date')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                @foreach ($arrDegree as $degree)
                    <tr>
                        <th class="w-20">{{ $degree['name'] }}
                            <x-text-required />
                        </th>
                        <td>
                            @php
                                $value = $errors->any() ? old($degree['key']) : App\Helpers\Helper::Degree($degree['value']);
                            @endphp
                            <x-forms.school-staff.degree :id="$degree['id']" :value="$value" :key="$degree['key']" />
                            @error($degree['key'])
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                    </tr>
                @endforeach
                @foreach ($arrChecked as $checked)
                    <tr>
                        <th class="w-20">{{ $checked['name'] }}
                            <x-text-required />
                        </th>
                        <td>
                            <div class="border d-flex rounded @error($checked['key']) is-invalid @enderror">
                                <div class="form-check m-2">
                                    <input class="form-check-input" type="radio" value="0"
                                        name={{ $checked['key'] }} id="no-{{ $checked['id'] }}"
                                        {{ $checked['value'] == 0 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="no-{{ $checked['id'] }}">
                                        なし
                                    </label>
                                </div>
                                <div class="form-check m-2">
                                    <input class="form-check-input" type="radio" value="1"
                                        name={{ $checked['key'] }} id="yes-{{ $checked['id'] }}"
                                        {{ $checked['value'] == 1 ? 'checked' : '' }}>
                                    <label class="form-check-label" for="yes-{{ $checked['id'] }}">
                                        有り
                                    </label>
                                </div>
                            </div>
                            @error($checked['key'])
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </td>
                    </tr>
                @endforeach
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
@section('js')
<script src="{{ asset('/js/tem-script.js') }}" type="text/javascript"></script>
@stop
