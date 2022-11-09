<form class="flex flex-col w-full" method="POST" id="form-search" autocomplete="off" action="{{ $route }}">
    @csrf
    @method($method)
    <input name="la_type" class="form-control" value="{{ request()->query('la_type') }}" hidden>
    <input name="test_date" class="form-control" type="date" value="{{ request()->query('test_date') }}" hidden>
    <input name="num_of_days" type="text" class="form-control" value="{{ request()->query('num_of_days') }}" hidden>
    <input name="period_num_to" type="text" class="form-control" value="{{ request()->query('period_num_to') }}"
        hidden>
    <input name="period_num_from" type="text" class="form-control" value="{{ request()->query('period_num_from') }}"
        hidden>
    <div class="card-body">
        <table class="table table-bordered table-view">
            <tbody>
                <tr>
                    <th class="w-20">検定種別</th>
                    <td>
                        <input name="la_type" class="form-control" value="{{ old('la_type', $data->la_type) }}"
                            disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">検定日</th>
                    <td>
                        <input name="test_date" class="form-control" type="date"
                            value="{{ old('test_date', $data->test_date) }}" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">実施回</th>
                    <td>
                        <input name="num_of_days" type="text" class="form-control"
                            value="{{ old('num_of_days', $data->num_of_days) }}" disabled>
                    </td>
                </tr>
                <tr>
                    <th class="w-20">実施時限</th>
                    <td>
                        <input name="period_num" type="text" class="form-control"
                            value="{{ $data->period_name_from . '～' . $data->period_name_to }}" disabled>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <div class="card-body">
        <div class="dataTables_wrapper dt-bootstrap4">
            <div class="row">
                <div class="col-sm-12">
                    <table class="table table-bordered table-hover dataTable dtr-inline">
                        <thead>
                            <tr>
                                <th class="sorting" rowspan="1" colspan="1" aria-label="">登録</th>
                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生番号</th>
                                <th class="sorting" rowspan="1" colspan="1" aria-label="">教習生名</th>
                                <th class="sorting" rowspan="1" colspan="1" aria-label="">車種</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if ($data->list_student->isEmpty())
                                <tr>
                                    <td colspan="100%" class="text-center">データがありません。</td>
                                </tr>
                            @else
                                @foreach ($data->list_student as $item)
                                    <tr>
                                        <td><input type="checkbox" name="ledger_id[]"
                                                value="{{ $item->admCheckItem->first()->ledger_id }}"></td>
                                        <td>{{ $item->admCheckItem->first()->student_no }}</td>
                                        <td>{{ $item->admCheckItem->first()->name_kana }}</td>
                                        <td>{{ $item->admCheckItem->first()->target_license_names }}</td>
                                    </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
    <div class="card-footer">
        <div class="col text-center">
            <a href="{{ route('apply-test.index', request()->only(['la_type', 'test_date', 'num_of_days', 'period_num_from', 'period_num_to'])) }}"
                class="btn btn-sm btn-secondary">キャンセル</a>
            <button type="submit" class="btn btn-sm btn-primary ml-1" @disabled($data->list_student->isEmpty())>保存</button>
        </div>
    </div>
</form>
