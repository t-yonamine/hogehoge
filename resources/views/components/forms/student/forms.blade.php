@php
    $personalInformation = [
        ['key' => '氏名', 'value' => old('name', $infor->admCheckItem?->name), 'name' => 'name', 'type' => 'text', 'maxlength' => '128'],
        ['key' => 'カナ', 'value' => old('name_kana', $infor->admCheckItem->name_kana), 'name' => 'name_kana', 'type' => 'text' , 'maxlength' => '128'], 
        ['key' => '生年月日', 'value' => old('birth_date', $infor->admCheckItem->birth_date->format('Y-m-d')), 'name' => 'birth_date', 'type' => 'date', 'maxlength' => '10'], 
        ['key' => '性別', 'value' => old('gender', $infor->admCheckItem->gender->value), 'name' => 'gender', 'type' => 'text', 'maxlength' => '1'], 
        ['key' => '住所（郵便番号）', 'value' => old('zip_code', $infor->admCheckItem->zip_code), 'name' => 'zip_code', 'type' => 'text', 'maxlength' => '7'], 
        ['key' => '住所（住所）', 'value' => old('address', $infor->admCheckItem->address), 'name' => 'address', 'type' => 'text', 'maxlength' => '200']
    ];
    
    $tabItems = [
        ['key' => '表紙'], 
        ['key' => '運転適性'], 
        ['key' => '効果測定'], 
        ['key' => '検定'], 
        ['key' => '技能-第1'], 
        ['key' => '学科-第1'], 
        ['key' => '技能-第2'], 
        ['key' => '学科-第2'], 
        ['key' => '任意項目']
    ];
    $disabled = $action == 'show';
@endphp
<form class="flex flex-col w-full" method="POST" autocomplete="off" action="{{ $route }}">
    @csrf
    @method($method)
    <div class="card-body">
        <h4>教習情報</h4>
        <table class="table table-bordered table-view">
            <tbody>
                <tr>
                    <th class="w-20">教習生番号</th>
                    <td>
                        <input name="student_no" class="form-control" value="{{ old('student_no', $infor->studentNo) }}"
                            disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">教習車種</th>
                    <td>
                        <input name="target_license_cd" class="form-control"
                            value="{{ old('target_license_cd', $infor->admCheckItem->licenseType->name_s) }}"
                            disabled>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <h4>個人情報</h4>
        <table class="table table-bordered table-view">
            <tbody>
                @foreach ($personalInformation as $item)
                    <tr>
                        <th class="w-20">{{ $item['key'] }}</th>
                        <td>
                            @if ($item['name'] == 'gender' && !$disabled)
                                <select class="form-control" name="{{$item['name']}}">
                                    <option value="1"  @selected($item["value"] == 1)>男</option>
                                    <option value="2"  @selected($item["value"] == 2)>女</option>
                                </select>
                            @else
                                <x-pc.forms.input name='{{$item["name"]}}' maxlength='{{$item["maxlength"]}}' type='{{$item["type"] ?? "text"}}' disabled='{{$disabled}}'  
                                    value='{{ $item["value"] }}'/>
                            @endif
                        </td>
                    </tr>
                @endforeach
                <tr>
                    <th class="w-20">顔写真</th>
                    <td>
                        <div class="row">
                            <div class="input-group custom-file-button">
                                <label class="input-group-text rounded-0" for="file-upload">ファイルを選択</label>

                                <input id="file-upload" type="file" class="d-none" title="" name="files"
                                    accept=".csv">
                                <span class="invalid-feedback" style="margin-left: 130px;" role="alert">
                                    <strong class="error-size"
                                        hidden>{{ __('messages.MSE00005', ['label' => '100']) }}</strong>
                                    <strong class="error-file" hidden>{{ __('messages.MSE00006') }}</strong>
                                    @error('files')
                                        <strong>{{ $message }}</strong>
                                    @enderror
                                </span>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">確認資料
                    </th>
                    <td>
                        <div class="row">
                            <div class="col-4 d-flex align-items-center">
                                <div class="form-check m-2">
                                    <input name="citizen_card_check_sw" class="form-check-input" type="checkbox"
                                        @checked(old('citizen_card_check_sw', $infor->admCheckItem->citizen_card_check_sw) == 1)>
                                    <label class="form-check-label" for="citizen_card_check_sw" @check>
                                        住民票
                                    </label>
                                </div>
                                <div class="form-check m-2">
                                    <input name="license_check_sw" class="form-check-input " type="checkbox"
                                        @checked(old('license_check_sw', $infor->admCheckItem->license_check_sw) == 1)>
                                    <label class="form-check-label" for="license_check_sw">
                                        免許証
                                    </label>
                                </div>
                                <div class="form-check m-2 col-4">
                                    <input name="other_check_sw" class="form-check-input " type="checkbox"
                                        id="other_check_sw" @checked(old('other_check_sw', $infor->admCheckItem->other_check_sw) == 1)>
                                    <label class="form-check-label" for="other_check_sw">
                                        その他
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <x-pc.forms.input name='other_check_text' type='text' disabled='{{old("other_check_sw", $infor->admCheckItem->other_check_sw) != 1}}'  value='{{ old("other_check_text", $infor->admCheckItem?->other_check_text) }}'/>
                            </div>
                        </div>
                    </td>
                    <td>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-body">
        @if ($disabled)
        
        {{-- detail screen --}}
            <x-forms.student.nav-tabs :data="$tabItems" disabled="{{$disabled}}" :infor="$infor"></x-forms.student.nav-tabs>
        @else
        {{-- edit screen --}}
            <x-forms.student.nav-tabs disabled="{{$disabled}}" :infor="$infor"></x-forms.student.nav-tabs>
        @endif
    </div>
    </div>
    @if ($action != 'show')
    <div class="m-2"><button type="submit" class="btn btn-secondary float-right">保存</button></div>
    @endif
</form>
