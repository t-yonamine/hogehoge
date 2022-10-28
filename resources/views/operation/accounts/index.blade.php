@extends('adminlte::page')

@section('title', '運営アカウント一覧')

@section('content_header')
    <h1>運営アカウント一覧</h1>
@stop

@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-tools">
                        {{-- 教習所アカウント登録画面へ遷移 --}}
                        <a href="{{ route('accounts.create') }}" class="btn btn-sm btn-secondary"><i
                                class="fa fa-btn fa-plus-circle"></i> 新規登録</a>
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
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">担当者番号</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">氏名</th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">ログインID</th>
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
                                                        <td>{{ $item->staff_no }}</td>
                                                        <td>{{ $item->name }}</td>
                                                        <td>{{ $item->user->login_id }}</td>
                                                        <td class="text-center d-flex justify-content-center">
                                                            {{-- 教習所アカウント詳細画面へ遷移 --}}
                                                            <<<<<<< HEAD <a
                                                                href="{{ route('accounts.update', ['id' => $item->id]) }}"
                                                                class="btn btn-sm btn-secondary mr-1">詳細</a>
                                                                =======
                                                                <a href="{{ route('accounts.update', ['id' => $item->id]) }}"
                                                                    class="btn btn-sm btn-secondary mr-1">詳細</a>
                                                                >>>>>>> 4e871f75ace16f5f35d3166afda09d46eb06c6f7
                                                                {{-- 削除確認モーダルを表示する。「削除しますか？」 --}}
                                                                <button type="submit"
                                                                    class='btn btn-sm btn-secondary delete-button'
                                                                    data-action='{{ route('accounts.delete', [$item]) }}'
                                                                    data-id='{{ $item->id }}'>
                                                                    削除
                                                                </button>
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
    </div>
    <x-modal.delete />
@stop
