@extends('adminlte::page')

@section('title', '効果測定登録')

@section('content_header')
    <h1>
        効果測定登録</h1>
@stop

@php
    $laTypeText = old('la_type', $laType) > App\Enums\LaType::PRE_EXAMINATION ? '卒検前' : '仮免前';
@endphp
@section('content')

    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="flex flex-col w-full" method="post" id="form-search"
                    action="{{ route('effect-measurement.store') }}" autocomplete="off">
                    @csrf
                    <input name="ledger_id" type="hidden" class="form-control" placeholder=""
                        value="{{ old('name', $data->id) }}">
                    <input name="la_type" type="hidden" class="form-control" placeholder=""
                        value="{{ old('name', $laType) }}">
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <tr>
                                    <th class="w-20">教習生番号</th>
                                    <td><input name="student_no" type="text" class="form-control" placeholder=""
                                            value="{{ old('student_no', $data->student_no) }}" disabled></td>
                                </tr>
                                <tr>
                                    <th class="w-20">氏名</th>
                                    <td><input name="name" type="text" class="form-control" placeholder=""
                                            value="{{ old('name', $data->admCheckItem->name) }}" disabled></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <tr>
                                    <th class="w-20">テスト区分</th>
                                    <td><input name="la_type" type="text" class="form-control" placeholder=""
                                            value="{{ $laTypeText }}" disabled></td>
                                </tr>
                                <tr>
                                    <th class="w-20">実施日付
                                        <x-text-required />
                                    </th>
                                    <td><input name="period_date" type="date"
                                            class="form-control @error('period_date') is-invalid @enderror" placeholder=""
                                            value="{{ old('period_date') }}" maxlength="10">
                                        @error('period_date')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">実施時間
                                        <x-text-required />
                                    </th>
                                    <td><input name="period_from" type="time"
                                            class="form-control @error('period_from') is-invalid @enderror" placeholder=""
                                            value="{{ old('period_from') }}">
                                        @error('period_from')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">問題番号
                                        <x-text-required />
                                    </th>
                                    <td><input name="question_num" type="text" maxlength="3"
                                            class="form-control @error('question_num') is-invalid @enderror" placeholder=""
                                            value="{{ old('question_num') }}">
                                        @error('question_num')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">点数
                                        <x-text-required />
                                    </th>
                                    <td><input name="score" type="text" maxlength="3"
                                            class="form-control @error('score') is-invalid @enderror" placeholder=""
                                            value="{{ old('score') }}">
                                        @error('score')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">合否
                                        <x-text-required />
                                    </th>
                                    <td>
                                        <div class="d-flex">
                                            <input name="result" class="" id="OK" type="radio"
                                                class="form-control" placeholder="" value="1" checked>
                                            <span class="mb-0 ml-1 mr-3" for="OK">合格</span>
                                            <input name="result" class="" id="NG" type="radio"
                                                class="form-control" placeholder="" value="0">
                                            <span class="m-0 mx-1" for="NG">不合格</span>
                                        </div>
                                        @error('result')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">メモ
                                        <x-text-required />
                                    </th>
                                    <td><input name="remarks" type="text"
                                            class="form-control @error('remarks') is-invalid @enderror" placeholder=""
                                            maxlength="100" value="{{ old('remarks') }}">
                                        @error('remarks')
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
                        <div class="col text-center">
                            <a href="{{ route('effect-measurement.index', [$data->id]) }}"
                                class="btn btn-sm btn-secondary">
                                キャンセル
                            </a>
                            <button class="btn btn-sm btn-primary" type="submit">保存</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
@stop
