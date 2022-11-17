@extends('adminlte::page')

@section('title', '教習生一覧')

@section('content_header')
    <h1>教習生一覧</h1>
@stop
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            {{ $data }}
            <div class="card">
                @csrf
                <form class="flex flex-col w-full" method="GET" id="form-search" action="{{ route('student.index') }}"
                    autocomplete="off">
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <input type="hidden" name="is_search" value="true">
                                <tr>
                                    <th class="w-20">教習生番号</th>
                                    <td><input name="student_no"
                                            class="form-control @error('student_no') is-invalid @enderror"
                                            value="{{ old('student_no', request()->query('student_no')) }}" type="text"
                                            class="form-control" placeholder="" maxlength="10">
                                        @error('student_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">フリガナ</th>
                                    <td><input name="name_kana"
                                            value="{{ old('name_kana', request()->query('name_kana')) }}" type="text"
                                            class="form-control @error('name_kana') is-invalid @enderror" placeholder=""
                                            maxlength="100">
                                        @error('name_kana')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">車種</th>
                                    <td>
                                        <select class="form-control w-12" name="cd_value">
                                            <option value="">
                                                指定なし
                                            </option>
                                            @foreach ($codeOptions as $key => $item)
                                                <option value="{{ $item->cd_value }}" @selected(old('cd_value', request()->query('cd_value')) == $item->cd_value)>
                                                    {{ $item->cd_text }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">日付検索方法</th>
                                    <td><select class="form-control w-12" name="">
                                            <option value="">
                                                XXXXX
                                            </option>
                                        </select></td>
                                </tr>
                                <tr>
                                    <th class="w-20">日付検索</th>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <input name="" type="date" class="form-control mr-1" placeholder=""
                                                maxlength="10">
                                            〜
                                            <input name="" type="date" class="form-control ml-1" placeholder=""
                                                maxlength="10">
                                        </div>
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">在籍</th>
                                    <td>
                                        <input name="lesson_sts" class="form-control h-25 width-check-box" type="checkbox"
                                            @if ($lessonSts) checked @endif>
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
                    </div>
                </form>
            </div>

            <div class="card">
                <div class="card-body">
                    <x-datatable.pagination :showTotal="true" :paginator="$data">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <thead>
                                            <tr>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">氏名</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">進捗</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">車種(所持免許)
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">直近教習日</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">直近教習内容</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">仮免有効期限</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習期限</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">
                                                    卒業検定受験期限
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">入所日
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">卒業日
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($data->isEmpty())
                                                <tr>
                                                    <td colspan="100%" class="text-center">データがありません。</td>
                                                </tr>
                                            @else
                                                @foreach ($data as $item)
                                                    <tr>
                                                        <td>
                                                            {{ $item->student_no }}
                                                        </td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>
                                                            @foreach ($codes as $code)
                                                                @if ($item->ledger?->lesson_sts == $code->cd_value)
                                                                    {{ $code->cd_text }}
                                                                @endif
                                                            @endforeach
                                                        </td>
                                                        <td></td>
                                                        <td></td>
                                                        <td></td>
                                                        <td>
                                                            <x-tooltip.tooltip :title="$item->expy_date?->format('Y/m/d')">
                                                                {{ $item->expy_date?->format('y/m/d') }}
                                                            </x-tooltip.tooltip>
                                                        <td>
                                                            <x-tooltip.tooltip :title="$item->lesson_limit?->format('Y/m/d')">
                                                                {{ $item->lesson_limit?->format('y/m/d') }}
                                                            </x-tooltip.tooltip>
                                                        </td>
                                                        <td></td>
                                                        <td>
                                                            <x-tooltip.tooltip :title="$item->admission_date?->format('Y/m/d')">
                                                                {{ $item->admission_date?->format('y/m/d') }}
                                                            </x-tooltip.tooltip>
                                                        </td>
                                                        <td>
                                                            <x-tooltip.tooltip :title="$item->ledger?->gradtst_date?->format('Y/m/d')">
                                                                {{ $item->ledger?->gradtst_date?->format('y/m/d') }}
                                                            </x-tooltip.tooltip>
                                                        </td>
                                                        <td>
                                                            <div
                                                                class="d-flex justify-content-center flex-wrap cell-action">
                                                                <a href="{{ route('student.detail', ['id' => $item->id]) }}" class="btn btn-sm btn-secondary">詳細</a>
                                                                <button class="btn btn-sm btn-secondary" type="button">
                                                                    効果測定
                                                                </button>
                                                                <button class="btn btn-sm btn-secondary" type="button">
                                                                    運転適性
                                                                </button>
                                                                <button class="btn btn-sm btn-secondary" type="button">
                                                                    任意項目
                                                                </button>
                                                                <button class="btn btn-sm btn-secondary" type="button">
                                                                    原簿
                                                                </button>
                                                            </div>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            @endif
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </x-datatable.pagination>
                </div>
            </div>
        </div>
        <x-modal.delete />
    </div>
@stop
