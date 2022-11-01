@extends('adminlte::page')

@section('title', 'スタッフ一覧')

@section('content_header')
    <h1>スタッフ一覧</h1>
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
                                    <th class="w-20">職員番号</th>
                                    <td><input name="school_staff_no"
                                            class="form-control @error('school_staff_no') is-invalid @enderror"
                                            value="{{ old('school_staff_no', request()->query('school_staff_no')) }}"
                                            type="text" class="form-control" placeholder="" maxlength="16">
                                        @error('school_staff_no')
                                            <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">氏名</th>
                                    <td><input name="name" value="{{ old('name', request()->query('name')) }}"
                                            type="text" class="form-control" placeholder="" maxlength="128"></td>
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
                    <x-datatable.pagination :showTotal="true" :paginator="$data">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <thead>
                                            <tr>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">職員番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">氏名</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">役割</th>
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
                                                        <td>{{ $item->school_staff_no }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ Helper::getRoleName($item->role) }}</td>
                                                        <td class="text-center d-flex justify-content-center">
                                                            <a href="{{ route('school-staff.update', ['id' => $item->id]) }}" class="btn btn-sm btn-secondary">編集</a>
                                                            <button type="button"
                                                                class="btn btn-sm btn-secondary delete-button ml-1"
                                                                data-action="{{ route('school-staff.delete', ['id' => $item->id]) }}"
                                                                data-id='{{ $item->id }}'>削除</button>
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

