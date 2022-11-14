@props(['infor'])
@php
$informationAdmision = [
['key'=> '入所年月日', 'value'=> old('admission_date', $infor->adm_check_item->admission_date?->format('Y-m-d')), 'name' => 'admission_date', 'type' => 'date'],
['key'=> '教習開始年月日', 'value'=> old('lesson_start_date', $infor->adm_check_item->lesson_start_date?->format('Y-m-d')), 'name' => 'lesson_start_date', 'type' => 'date'],
['key'=> '教習期限', 'value'=> old('lesson_limit', $infor->adm_check_item->lesson_limit?->format('Y-m-d')), 'name' => 'lesson_limit', 'type' => 'text'],
['key'=> '教習修了年月日', 'value'=> old('lesson_end_date', $infor->adm_check_item->lesson_end_date?->format('Y-m-d')), 'name' => 'lesson_end_date', 'type' => 'date'],

['key'=> '検定期限', 'value'=> old('test_limit', $infor->adm_check_item->test_limit?->format('Y-m-d')), 'name' => 'test_limit', 'type' => ''],
['key'=> '修了証明書発行年月日', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '修了証明書番号', 'value'=>'' ,'name' => '', 'type' => 'date'],
['key'=> '卒業年月日', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '卒業証明書番号', 'value'=> '', 'name' => '', 'type' => 'date'],

['key'=> '転出年月日', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> '転入年月日', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> '転退所年月日', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> '仮免許交付年月日1', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> '仮免許有効期限1', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> '仮免許証番号1', 'value'=> old(''), 'name' => '', 'type' => 'date'],
['key'=> ':', 'value'=> old(''), 'name' => '', 'type' => 'date'],
];

$posseionInformation = [
['key'=> '交付年月日', 'value'=> '', 'name' => 'admission_date', 'type' => 'date'],
['key'=> '有効期限', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '免許証番号', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '管轄警察署', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '免許証番号', 'value'=> '', 'name' => '', 'type' => 'date'],
];

$provisionalLicense = [
['key'=> '大型', 'value'=> '', 'name' => ''],
['key'=> '中型', 'value'=> '', 'name' => ''],
['key'=> '準中型', 'value'=> '', 'name' => ''],
['key'=> '普通', 'value'=> '', 'name' => ''],
];

$classTwoLicense =[
['key'=> '大型二', 'value'=> '', 'name' => ''],
['key'=> '中型二', 'value'=> '', 'name' => ''],
['key'=> '普通二', 'value'=> '', 'name' => ''],
['key'=> '大特二', 'value'=> '', 'name' => ''],
['key'=> '牽引二', 'value'=> '', 'name' => ''],
];

$oneClassLicense =[
['key'=> '大型', 'value'=> '', 'name' => ''],
['key'=> '中型', 'value'=> '', 'name' => ''],
['key'=> '準中型', 'value'=> '', 'name' => ''],
['key'=> '普通', 'value'=> '', 'name' => ''],
['key'=> '大特', 'value'=> '', 'name' => ''],
['key'=> '大自二', 'value'=> '', 'name' => ''],
['key'=> '普自二', 'value'=> '', 'name' => ''],
['key'=> '小型', 'value'=> '', 'name' => ''],
['key'=> '原付', 'value'=> '', 'name' => ''],
['key'=> '牽引', 'value'=> '', 'name' => ''],
];

$posseionInformation = [
['key'=> '交付年月日', 'value'=> '', 'name' => 'admission_date', 'type' => 'date'],
['key'=> '有効期限', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '免許証番号', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '管轄警察署', 'value'=> '', 'name' => '', 'type' => 'date'],
['key'=> '免許証番号', 'value'=> '', 'name' => '', 'type' => 'date'],
];

@endphp
<div class="tab-pane active" id="tabs-0" role="tabpanel" >
    <h5 class="mt-2 mb-2">入所時の記録</h5>
    <table class="table table-bordered table-view">
        <tbody>
            @foreach($informationAdmision as $item)
            <tr>
                <th class="w-20">{{ $item['key'] }}</th>
                <td>
                    <input name="{{ $item['name']}}" type="@php( $item['type'] == 'date) 'date' ? 'text @endphp" class="form-control" value="{{ $item['value'] }}" disabled>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <h5 class="mt-2 mb-2">所持免許情報</h5>
    <table class="table table-bordered table-view">
        <tbody>
            @foreach($posseionInformation as $item)
            <tr>
                <th class="w-20">{{ $item['key'] }}</th>
                <td>
                    <input name="{{ $item['name']}}" class="form-control" value="{{ $item['value'] }}">
                </td>
            </tr>
            @endforeach
            <tr>
                <th class="w-20">仮免許</th>
                <td>
                    @foreach($provisionalLicense as $item)
                    <div class="form-check m-2">
                        <input name="{{ $item['name']}}" class="form-check-input" type="checkbox" value="{{ $item['value'] }}">
                        <label class="form-check-label">
                            {{ $item['key'] }}
                        </label>
                    </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="w-20">一種免許</th>
                <td>
                    @foreach($oneClassLicense as $item)
                    <div class="form-check m-2">
                        <input name="{{ $item['name']}}" class="form-check-input" type="checkbox" value="{{ $item['value'] }}">
                        <label class="form-check-label">
                            {{ $item['key'] }}
                        </label>
                    </div>
                    @endforeach
                </td>
            </tr>
            <tr>
                <th class="w-20">二種免許</th>
                <td>
                    @foreach($classTwoLicense as $item)
                    <div class="form-check m-2">
                        <input name="{{ $item['name']}}" class="form-check-input" type="checkbox" value="{{ $item['value'] }}">
                        <label class="form-check-label">
                            {{ $item['key'] }}
                        </label>
                    </div>
                    @endforeach
                </td>
            </tr>
        </tbody>
    </table>
    <h5 class="mt-2 mb-2">教習条件</h5>
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">教習条件</th>
                <td>
                    <div class="form-check m-2">
                        <input name="test_date" class="form-check-input " type="checkbox" value="{{ old('test_date') }}">
                        <label class="form-check-label">
                            眼鏡等
                        </label>
                    </div>
                    <div class="form-check m-2">
                        <input name="test_date" class="form-check-input " type="checkbox" value="{{ old('test_date') }}">
                        <label class="form-check-label">
                            コンタクト
                        </label>
                    </div>
                    <div class="form-check m-2">
                        <input name="test_date" class="form-check-input " type="checkbox" value="{{ old('test_date') }}">
                        <label class="form-check-label">
                            補聴器
                        </label>
                    </div>
                </td>
            </tr>
            <tr>
                <th>応急救護</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                有
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                無
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
        </tbody>
    </table>
    <h5 class="mt-2 mb-2">入所時身体的適性検査</h5>
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">視力裸眼（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視力矯正（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視野（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>色彩識別</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>聴力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                第１号
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                第２号
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>運動能力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>検査日</th>
                <td>
                    <input class="form-control" name="exampleRadios" id="exampleRadios2">
                </td>
            </tr>
            <tr>
                <th>検査者</th>
                <td>
                    <input class="form-control" name="exampleRadios" id="exampleRadios2">
                </td>
            </tr>
        </tbody>
    </table>
    <h5 class="mt-2 mb-2">修了検定時時身体的適性検査</h5>
    <table class="table table-bordered table-view">
        <tbody>
            <tr>
                <th class="w-20">視力裸眼（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視力矯正（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th class="w-20">視野（左・右・両）</th>
                <td>
                    <div class="row">
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                        <div class="col">
                            <input name="test_date" class="form-control" value="{{ old('test_date') }}">
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>色彩識別</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>聴力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                第１号
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                第２号
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>運動能力</th>
                <td>
                    <div class="d-flex">
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios1" value="option1" checked>
                            <label class="form-check-label" for="exampleRadios1">
                                適
                            </label>
                        </div>
                        <div class="form-check m-2">
                            <input class="form-check-input" type="radio" name="exampleRadios" id="exampleRadios2" value="option2">
                            <label class="form-check-label" for="exampleRadios2">
                                否
                            </label>
                        </div>
                    </div>
                </td>
            </tr>
            <tr>
                <th>検査日</th>
                <td>
                    <input class="form-control" name="exampleRadios" id="exampleRadios2">
                </td>
            </tr>
            <tr>
                <th>検査者</th>
                <td>
                    <input class="form-control" name="exampleRadios" id="exampleRadios2">
                </td>
            </tr>
        </tbody>
    </table>
</div>