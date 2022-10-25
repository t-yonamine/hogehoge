@extends('adminlte::page')

@section('title', '教習所一覧')

@section('content_header')
    <h1>教習所一覧</h1>
@stop



@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="flex flex-col w-full" method="GET" id="form-search" action="{{ route('school-driving.index') }}"
                    autocomplete="off">
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <tr>
                                    <th class="w-20">教習所CD</th>
                                    <td><input name="school_cd"
                                            value="{{ old('school_cd', request()->query('school_cd')) }}" type="text"
                                            class="form-control" placeholder="" maxlength="4"></td>
                                </tr>
                                <tr>
                                    <th class="w-20">フリガナ</th>
                                    <td><input name="name_kana"
                                            value="{{ old('name_kana', request()->query('name_kana')) }}" type="text"
                                            class="form-control" placeholder="" maxlength="64"></td>
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
                <div class="card-header">
                    <div class="card-tools">
                        <a href="#" class="btn btn-sm btn-secondary"><i class="fa fa-btn fa-plus-circle"></i> 新規登録</a>
                    </div>
                </div>
                <div class="card-body">
                    <x-datatable.pagination :showTotal="true" :paginator="$models">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <tbody>
                                            <thead>
                                                <tr>
                                                    <th class="sorting" rowspan="1" colspan="1" aria-label="">教習所CD
                                                    </th>
                                                    <th class="sorting" rowspan="1" colspan="1" aria-label="">教習所名
                                                    </th>
                                                    <th class="sorting" rowspan="1" colspan="1" aria-label="">フリガナ
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </thead>
                                            @if ($models->isEmpty())
                                                <div class="text-center">データがありません</div>
                                            @else
                                                @foreach ($models as $model)
                                                    <tr>
                                                        <td>{{ $model->school_cd }}</td>
                                                        <td>{{ $model->name }}</td>
                                                        <td>{{ $model->name_kana }}</td>
                                                        <td class="text-center d-flex justify-content-center">
                                                            <form id='formSub'
                                                                action="{{ route('school-driving.detail', ['id' => $model->id]) }}"
                                                                method="POST">
                                                                @method('get')
                                                                @csrf
                                                                <button type="submit"
                                                                    class="btn btn-sm btn-secondary">詳細</button>
                                                            </form>
                                                            <button type="button"
                                                                class="btn btn-sm btn-secondary delete-button ml-1"
                                                                data-action="{{ route('school-driving.delete', ['id' => $model->id]) }}"
                                                                data-id='{{ $model->id }}'>削除</button>
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
        <div>

            <!-- Modal -->
            <div class="modal fade" id="modelDelete" tabindex="-1" role="dialog" aria-labelledby="modelDeleteTitle"
                aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="exampleModalLabel">消去</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            削除しますか？
                        </div>
                        <div class="modal-footer">
                            <form id='formSub' action="" method="POST">
                                @method('DELETE')
                                @csrf
                                <button type="button" class="btn btn-secondary" data-dismiss="modal">キャンセル</button>
                                <button type="submit" class="btn btn-primary">はい</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@stop
@section('js')
    <script src="{{ asset('/js/tem-script.js') }}" type="text/javascript"></script>
@endsection
