@extends('adminlte::page')

@section('title', '効果測定一覧')

@section('content_header')
    <h1>効果測定一覧</h1>
@stop

@section('content')
    <x-alert/>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <table class="table table-bordered table-view">
                        <tbody>
                            <tr>
                                <th class="w-20">教習生番号</th>
                                <td>
                                    <input type="text" class="form-control" value="{{ $data->student_no }}" disabled="true">
                                </td>
                            </tr>
                            <tr>
                                <th class="w-20">氏名</th>
                                <td>
                                    <input type="text" class="form-control" value="{{ $data->admCheckItem->name }}"
                                        disabled="true">
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <div class="float-left card-tools">
                        <a class="btn btn-sm btn-secondary">仮免新規ボタン</a>
                        <a class="btn btn-sm btn-secondary">卒検新規ボタン</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="dataTables_wrapper dt-bootstrap4">
                        <div class="row">
                            <div class="col-sm-12">
                                <table class="table table-bordered table-hover dataTable dtr-inline">
                                    <thead>
                                        <tr>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">テスト区分</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">実施月日</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">実施時刻</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">実施者</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">問題番号</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">得点</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">合否</th>
                                            <th class="sorting" rowspan="1" colspan="1" aria-label="">メモ</th>
                                            <th></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if ($lesson_attends->isEmpty())
                                            <tr>
                                                <td colspan="100%" class="text-center">データがありません。</td>
                                            </tr>
                                        @else
                                            @foreach ($lesson_attends as $item)
                                                <tr>
                                                    <td>
                                                        @if ($item->la_type === 2210 || $item->la_type === 2211)
                                                            仮免前
                                                        @elseif ($item->la_type === 2220 || $item->la_type === 2221)
                                                            卒検前
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->period_date?->format('Y/m/d') }}</td>
                                                    <td>{{ $item->period_from?->format('h:i') }}</td>
                                                    <td>{{ $item->schoolStaff?->name }}</td>
                                                    <td>{{ $item->question_num }}</td>
                                                    <td>{{ $item->score }}</td>
                                                    <td>
                                                        @if ($item->result == 0)
                                                            合格
                                                        @elseif($item->result == 1)
                                                            不合格
                                                        @else
                                                        @endif
                                                    </td>
                                                    <td>{{ $item->remarks }}</td>
                                                    <td class="text-center">
                                                        <form action="{{ route('effect-measurement.delete', [$item]) }}"
                                                            method="POST">
                                                            @method('delete')
                                                            @csrf
                                                            <button type="submit" class="btn btn-sm btn-secondary">
                                                                削除
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
