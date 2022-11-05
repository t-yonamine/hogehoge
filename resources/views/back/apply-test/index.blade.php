@extends('adminlte::page')

@section('title', '検定申込一覧')

@section('content_header')
    <h1>検定申込一覧</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                @csrf
                <form class="flex flex-col w-full" method="GET" id="form-search" action="" autocomplete="off">
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <tr>
                                    <th class="w-20">検定種別</th>
                                    <th>
                                        <div class="d-flex">
                                            <input name="la_type" class="" id="COMPLTST" type="radio" $laType
                                                class="form-control" placeholder="" value="{{ App\Enums\LaType::COMPLTST }}"
                                                @if (old('la_type', $laType) == App\Enums\LaType::COMPLTST) checked @endif>
                                            <span class="mb-0 ml-1 mr-3" for="COMPLTST">修了検定</span>

                                            <input name="la_type" class="" id="PL_TEST" type="radio"
                                                class="form-control" placeholder="" value="{{ App\Enums\LaType::PL_TEST }}"
                                                @if (old('la_type', $laType) == App\Enums\LaType::PL_TEST) checked @endif>
                                            <span class="m-0 mx-1 mr-3" for="PL_TEST">仮免許</span>

                                            <input name="la_type" class="" id="GRADTST" type="radio"
                                                class="form-control" placeholder="" value="{{ App\Enums\LaType::GRADTST }}"
                                                @if (old('la_type', $laType) == App\Enums\LaType::GRADTST) checked @endif>
                                            <span class="mb-0 ml-1 mr-3" for="GRADTST">卒業検定</span>

                                            <input name="la_type" class="" id="DRVSKLTST" type="radio"
                                                class="form-control" placeholder=""
                                                value="{{ App\Enums\LaType::DRVSKLTST }}"
                                                @if (old('la_type', $laType) == App\Enums\LaType::DRVSKLTST) checked @endif>
                                            <span class="m-0 mx-1" for="DRVSKLTST">技能審査</span>
                                        </div>
                                    </th>
                                </tr>
                                <tr>
                                    <th class="w-20">検定日</th>
                                    <td>
                                        <div class="d-flex">
                                            <input name="test_date" type="date" id="test_date" class="form-control w-25"
                                                placeholder="" value="{{ old('test_date', request()->query('test_date')) }}"
                                                maxlength="10">
                                            <button class="btn btn-sm btn-secondary ml-1" type="button"
                                                onclick="addAndMinusDate(-1)">
                                                < </button>
                                                    <button class="btn btn-sm btn-secondary ml-1" type="button"
                                                        onclick="addAndMinusDate(+1)">></button>
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">実施回</th>
                                    <td>
                                        <select class="form-control" name="num_of_days">
                                            @foreach ($numberMax as $value)
                                                <option value="{{ $value }}"
                                                    @if (old('num_of_days', request()->query('num_of_days')) == $value) selected @endif>
                                                    {{ $value }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">実施時限</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <select class="form-control mr-1">
                                                @foreach ($dataOptionPeriod as $value)
                                                    <option value="{{ $value->period_num }}">
                                                        {{ $value->period_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            〜
                                            <select class="form-control ml-1">
                                                @foreach ($dataOptionPeriod as $value)
                                                    <option value="{{ $value->period_num }}">
                                                        {{ $value->period_name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <div class="col text-center">
                            <button class="btn btn-sm btn-secondary" type="submit"><i class="fa fa-btn fa-search"></i>
                                検索</button>
                        </div>
                        <div class="col">
                            <button class="btn btn-sm btn-secondary" type="submit">
                                教習生追加</button>
                            <button class="btn btn-sm btn-secondary" type="submit">
                                教習原簿PDFダウンロード
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            @if ($checkLaType == 1)
                <div class="card">
                    <div class="card-header">
                        <div class="text-left">
                            仮免許申込一覧
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <thead>
                                            <tr>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生名
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">車種</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">問題番号
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">問題言語
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">得点</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">合否</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label=""></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data->isEmpty())
                                                <tr>
                                                    <td colspan="100%" class="text-center">データがありません。</td>
                                                </tr>
                                            @else
                                                @foreach ($data as $item)
                                                    <form class="flex flex-col w-full" method="POST" autocomplete="off"
                                                        action="{{ route('apply-test.post', ['id' => $item->id]) }}">
                                                        @csrf
                                                        <tr>
                                                            <td>{{ $item->test_num }}</td>
                                                            <td>{{ $item->student_no }}
                                                            </td>
                                                            <td>{{ $item->name_kana }}
                                                            </td>

                                                            <td>{{ $item->target_license_names }}
                                                            </td>
                                                            <td><input name="question_num" type="text" maxlength="20"
                                                                    class="form-control"
                                                                    value="{{ old('question_num', $item->question_num) }}"
                                                                    @if (($role & App\Enums\SchoolStaffRole::ADMINISTRATOR) == 0) disabled @endif />
                                                            </td>

                                                            <td><input name="lang" type="text" class="form-control"
                                                                    maxlength="20" value="{{ old('lang', $item->lang) }}"
                                                                    @if (($role & App\Enums\SchoolStaffRole::ADMINISTRATOR) == 0) disabled @endif />
                                                            </td>
                                                            <td><input name="score" type="text"
                                                                    class="form-control @error('score') is-invalid @enderror"
                                                                    maxlength="3"
                                                                    value="{{ old('score', $item->score) }}"
                                                                    @if (($role & App\Enums\SchoolStaffRole::ADMINISTRATOR) == 0) disabled @endif />
                                                                @error('score')
                                                                    <span class="invalid-feedback" role="alert">
                                                                        <strong>{{ $message }}</strong>
                                                                    </span>
                                                                @enderror
                                                            </td>
                                                            <td>
                                                                <select class="form-control mr-1" name="result">
                                                                    <option value="{{ null }}"
                                                                        @if ($item->result == null) selected @endif>
                                                                    </option>
                                                                    <option value="{{ App\Enums\ResultType::NG() }}"
                                                                        @if ($item->result == App\Enums\ResultType::NG()) selected @endif>
                                                                        {{ App\Enums\ResultType::NG()->description }}
                                                                    </option>
                                                                    <option value="{{ App\Enums\ResultType::OK() }}"
                                                                        @if ($item->result == App\Enums\ResultType::OK()) selected @endif>
                                                                        {{ App\Enums\ResultType::OK()->description }}
                                                                    </option>
                                                                    <option value="{{ App\Enums\ResultType::CANCEL() }}"
                                                                        @if ($item->result == App\Enums\ResultType::CANCEL()) selected @endif>
                                                                        {{ App\Enums\ResultType::CANCEL()->description }}
                                                                    </option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <div
                                                                    class="d-flex justify-content-center flex-wrap cell-action">
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action"
                                                                        value="LICENSE_CONFIRM"
                                                                        @if (($role & App\Enums\SchoolStaffRole::ADMINISTRATOR) == 0) disabled @endif>
                                                                        確定</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action"
                                                                        value="DELETE_APPPLICATION"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        削除</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="TOP_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        先頭へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="UP_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        上へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="DOWN_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        下へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="END_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        末尾へ</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </form>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if ($checkLaType == 2)
                <div class="card">
                    <div class="card-header">
                        <div class="text-left">
                            {{ $titleType }} 申込一覧
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <thead>
                                            <tr>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生番号
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生名
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">車種</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">検定員
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">得点</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">合否</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label=""></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data->isEmpty())
                                                <tr>
                                                    <td colspan="100%" class="text-center">データがありません。</td>
                                                </tr>
                                            @else
                                                @foreach ($data as $item)
                                                    <form class="flex flex-col w-full" method="POST" autocomplete="off"
                                                        action="{{ route('apply-test.post', ['id' => $item->id]) }}">
                                                        @csrf
                                                        <tr>
                                                            <td>{{ $item->test_num }}</td>
                                                            <td>{{ $item->student_no }}</td>
                                                            <td>{{ $item->name_kana }}</td>
                                                            <td>{{ $item->target_license_names }}</td>
                                                            <td>{{ $item->schoolStaff->name }}</td>
                                                            <td>{{ $item->score }}</td>
                                                            <td>{{ $item->result->description }}
                                                            </td>
                                                            <td>
                                                                <div
                                                                    class="d-flex justify-content-center flex-wrap cell-action">
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="OPEN_MODAL"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING() ||
                                                                            ($role & App\Enums\SchoolStaffRole::ADMINISTRATOR) == 0) disabled @endif>
                                                                        検定員
                                                                    </button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        削除</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="TOP_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        先頭へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="UP_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        上へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="DOWN_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        下へ</button>
                                                                    <button class="btn btn-sm btn-secondary"
                                                                        type="submit" name="action" value="END_BUTTON"
                                                                        @if ($item->status >= App\Enums\LessonAttendStatus::PENDING()) disabled @endif>
                                                                        末尾へ</button>
                                                                </div>
                                                            </td>
                                                        </tr>
                                                    </form>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop

<script>
    function addAndMinusDate($value) {
        let currentDate = document.getElementById('test_date').value;
        const date = new Date(currentDate);
        date.setDate(date.getDate() + $value);
        let dateformat = date.toISOString().split('T')[0];
        document.getElementById('test_date').value = dateformat;
    }
</script>
