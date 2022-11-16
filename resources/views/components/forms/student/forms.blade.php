@php
$personalInformation = [
['key'=> '氏名', 'value'=> old('target_license_cd', $infor->adm_check_item?->target_license_cd), 'name'=>
'target_license_cd' , 'type'=> 'text'],
['key'=> 'カナ', 'value'=> old('name', $infor->adm_check_item->name), 'name'=> 'name' , 'type'=> 'text'],
['key'=> '生年月日', 'value'=> old('birth_date', $infor->adm_check_item->birth_date->format('Y-m-d')), 'name'=> 'birth_date', 'type'=>
'date'],
['key'=> '性別', 'value'=> old('gender', $infor->adm_check_item->gender), 'name'=> 'gender' , 'type'=> 'text'],
['key'=> '住所（郵便番号）', 'value'=> old('zip_code', $infor->adm_check_item->zip_code), 'name'=> 'zip_code' , 'type'=>
'text'],
['key'=> '住所（住所）', 'value'=> old('zip_code', $infor->adm_check_item->zip_code), 'name'=> 'zip_code', 'type'=> 'text'],
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
['key' => '任意項目'],
];

@endphp
<form class="flex flex-col w-full" method="GET" autocomplete="off" action="{{ $route }}">
    @csrf
    @method($method)
    <div class="card-body">
        <h4>教習情報</h4>
        <table class="table table-bordered table-view">
            <tbody>
                <tr>
                    <th class="w-20">教習生番号</th>
                    <td>
                        <input name="student_no" class="form-control" value="{{ old('student_no', $infor->student_no) }}" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">教習車種</th>
                    <td>
                        <input name="target_license_cd" class="form-control" value="{{ old('target_license_cd',  $infor->adm_check_item->target_license_cd) }}" disabled>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <h4>個人情報</h4>
        <table class="table table-bordered table-view">
            <tbody>
                @foreach($personalInformation as $item)
                <tr>
                    <th class="w-20">{{ $item['key'] }}</th>
                    <td>
                        @if ($item['type'] == 'date')
                        <input name="{{ $item['name']}}" class="form-control" value="{{ $item['value'] }}" type="date" disabled>
                        @else
                        <input name="{{ $item['name']}}" class="form-control" value="{{ $item['value'] }}" disabled>
                        @endif

                    </td>
                </tr>
                @endforeach
                <tr>
                    <th class="w-20">顔写真</th>
                    <td>
                        <div class="row">
                            <div class="col-4">
                                <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                            </div>
                            <div class="col-8">
                                <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">確認資料</th>
                    <td>
                        <div class="row">
                            <div class="col-4 d-flex align-items-center">
                                <div class="form-check m-2">
                                    <input name="citizen_card_check_sw" class="form-check-input" type="checkbox" value="{{ old('citizen_card_check_sw', $infor->adm_check_item->citizen_card_check_sw) }}" @checked($infor->adm_check_item->citizen_card_check_sw == '1')
                                    >
                                    <label class="form-check-label" @check>
                                        住民票
                                    </label>
                                </div>
                                <div class="form-check m-2">
                                    <input name="license_check_sw" class="form-check-input " type="checkbox" value="{{ old('license_check_sw', $infor->adm_check_item->license_check_sw) }}" @checked($infor->adm_check_item->license_check_sw == '1')
                                    >
                                    <label class="form-check-label">
                                        免許証
                                    </label>
                                </div>
                                <div class="form-check m-2">
                                    <input name="other_check_sw" class="form-check-input " type="checkbox" value="{{ old('other_check_sw', $infor->adm_check_item->other_check_sw) }}" @checked($infor->adm_check_item->other_check_sw == '1')
                                    >
                                    <label class="form-check-label">
                                        その他
                                    </label>
                                </div>
                            </div>
                            <div class="col-8">
                                <input name="other_check_text" class="form-control" type="text" value="{{ old('other_check_text', $infor->adm_check_item->other_check_text) }}" disabled>
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
        <x-forms.student.nav-tabs :data="$tabItems" :infor="$infor"></x-forms.student.nav-tabs>
    </div>
    </div>
</form>