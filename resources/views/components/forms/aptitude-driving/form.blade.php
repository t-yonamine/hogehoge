@php
    $data = $data ?? null;
    $seq = $data->seq->value ?? App\Enums\Seq::FIRST1;
    $testtype = $data->test_type->value ?? App\Enums\TestType::OD;
    $resultOneToFive = App\Enums\ResultNumber::getInstances();
    
    $resultOneToThree = collect($resultOneToFive)
        ->filter(function ($value) {
            return $value->notIn([App\Enums\ResultNumber::FOUR, App\Enums\ResultNumber::FIVE]);
        })
        ->toArray();
    
    $resultCharacter = App\Enums\ResultCharacter::getInstances();
    
    $resultAToE = collect($resultCharacter)
        ->filter(function ($value) {
            return $value->notIn([App\Enums\ResultCharacter::DMINUS, App\Enums\ResultCharacter::EMINUS]);
        })
        ->toArray();
    
    $resultAToC = collect($resultCharacter)
        ->filter(function ($value) {
            return $value->notIn([App\Enums\ResultCharacter::DMINUS, App\Enums\ResultCharacter::EMINUS, App\Enums\ResultCharacter::D, App\Enums\ResultCharacter::E]);
        })
        ->toArray();
@endphp

<form class="flex flex-col w-full" method="{{ $method }}" autocomplete="off" action="{{ $route }}">
    @csrf
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>
                    <th class="w-20">教習生番号</th>
                    <td>
                        <input name="student_no" type="text" class="form-control "
                            value="{{ old('student_no', $model->student_no) }}" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">氏名</th>
                    <td>
                        <input name="name" type="text" class="form-control "
                            value="{{ old('name', $model->name) }}" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">回数
                        <x-text-required />
                    </th>
                    <td>
                        <div class="d-flex">
                            <input name="seq" class="" id="OK" type="radio" class="form-control"
                                value="{{ App\Enums\Seq::FIRST1 }}" @if (old('seq', $seq) == App\Enums\Seq::FIRST1) checked @endif>
                            <label class="mb-0 ml-1 mr-3 font-weight-normal"
                                for="OK">{{ App\Enums\Seq::getDescription(App\Enums\Seq::FIRST1) }}</label>
                            <input name="seq" class="" id="NG" type="radio"
                                value="{{ App\Enums\Seq::FIRST2 }}" @if (old('seq', $seq ?? 0) == App\Enums\Seq::FIRST2) checked @endif>
                            <label class="m-0 mx-1 font-weight-normal"
                                for="NG">{{ App\Enums\Seq::getDescription(App\Enums\Seq::FIRST2) }}</label>
                        </div>
                        @error('seq')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">検査方式
                        <x-text-required />
                    </th>
                    <td>
                        <div class="d-flex">
                            <input name="test_type" class="" id="OD" type="radio" class="form-control"
                                value="{{ App\Enums\TestType::OD }}" @if (old('test_type', $testtype) == App\Enums\TestType::OD) checked @endif>
                            <label class="mb-0 ml-1 mr-3 font-weight-normal"
                                for="OD">{{ App\Enums\TestType::getDescription(App\Enums\TestType::OD) }}</label>
                            <input name="test_type" class="" id="K2" type="radio" class="form-control"
                                disabled value="{{ App\Enums\TestType::K2 }}"
                                @if (old('test_type', $testtype ?? 0) == App\Enums\TestType::K2) checked @endif>
                            <label class="m-0 mx-1 font-weight-normal"
                                for="K2">{{ App\Enums\TestType::getDescription(App\Enums\TestType::K2) }}</label>
                        </div>
                        @error('test_type')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">実施日
                        <x-text-required />
                    </th>
                    <td class=""><input name="test_date" type="date"
                            class="w-auto form-control @error('test_date') is-invalid @enderror" maxlength="10"
                            value={{ old('test_date', $data?->test_date) }}>
                        @error('test_date')
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
                <tr>OD式</tr>
                <tr>
                    <th class="w-20">性格パターン１
                        <x-text-required />
                    </th>
                    <td><input name="od_persty_pattern_1" type="text"
                            class="form-control @error('od_persty_pattern_1') is-invalid @enderror"
                            value="{{ old('od_persty_pattern_1', $data?->od_persty_pattern_1) }}" maxlength="3">
                        @error('od_persty_pattern_1')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">性格パターン２
                        <x-text-required />
                    </th>
                    <td>
                        <input name="od_persty_pattern_2" type="text"
                            class="form-control @error('od_persty_pattern_2') is-invalid @enderror"
                            value="{{ old('od_persty_pattern_2', $data?->od_persty_pattern_2) }}" maxlength="3">
                        @error('od_persty_pattern_2')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">運転適性度
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control w-12" name="od_drv_aptitude">
                            @foreach ($resultOneToFive as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_drv_aptitude', $data?->od_drv_aptitude) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_drv_aptitude')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">安全運転度
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_safe_aptitude">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_safe_aptitude', $data?->od_safe_aptitude) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_safe_aptitude')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">特異反応
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_specific_rxn">
                            @foreach ($resultOneToThree as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_specific_rxn', $data?->od_specific_rxn) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_specific_rxn')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">A.注意力
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_a">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_a', $data?->od_a) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_a')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">B.判断力
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_b">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_b', $data?->od_b) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_b')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">C.柔軟性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_c">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_c', $data?->od_c) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_c')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">D.決断力
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_d">
                            @foreach ($resultCharacter as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_d', $data?->od_d) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_d')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">E.緻密性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_e">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_e', $data?->od_e) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_e')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">F.動作の安定性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_f">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_f', $data?->od_f) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_f')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">G.適応性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_g">
                            @foreach ($resultAToE as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_g', $data?->od_g) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_g')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">H.身体的健康度
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_h">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_h', $data?->od_h) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_h')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">I.精神的健康度
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_i">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_i', $data?->od_i) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_i')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">J.社会的成熟度
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_j">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_j', $data?->od_j) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_j')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">K.情緒不安定
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_k">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_k', $data?->od_k) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_k')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">L.衝迫性・暴発性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_l">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_l', $data?->od_l) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_l')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">M.自己中心性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_m">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_m', $data?->od_m) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_m')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">N.神経質・過敏性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_n">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_n', $data?->od_n) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_n')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">O.虚飾性
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_o">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_o', $data?->od_o) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_o')
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </td>
                </tr>
                <tr>
                    <th class="w-20">P.運転マナー
                        <x-text-required />
                    </th>
                    <td>
                        <select class="form-control" name="od_p">
                            @foreach ($resultAToC as $key => $item)
                                <option value="{{ $item }}" @selected(old('od_p', $data?->od_p) == $item)>
                                    {{ $item }}
                                </option>
                            @endforeach
                        </select>
                        @error('od_p')
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
        <div class="col text-right p-0 d-flex justify-content-center">
            <a type="button" class="m-1 btn btn-sm btn-secondary" href='#'>
                キャンセル
            </a>
            <button class="m-1 btn btn-sm btn-primary" type="submit">
                保存
            </button>
            @if (!$isCreate)
                <button type="button" class="btn btn-sm btn-secondary delete-button m-1"
                    data-action="{{ route('aptitude-driving.delete', ['aptitude_drv_id' => $data->id]) }}"
                    data-id='{{ $data->id }}'>削除</button>
            @endif
        </div>
    </div>
</form>
<x-modal.delete />
