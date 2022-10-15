@extends('adminlte::page')

@section('title', 'スタッフ')

@section('content_header')
<h1>スタッフ</h1>
@stop

@section('content')

<div class="row">
    <div class="col-12">

        <div class="card">

            <form class="flex flex-col w-full" method="GET" id="form-search" action="" autocomplete="off">
                <div class="card-body">
                    <table class="table table-bordered table-view">
                        <tbody>
                            <tr>
                                <th class="w-20">指導員番号</th>
                                <td><input name="staff_no" type="text" class="form-control" placeholder="" maxlength="4"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col text-center">
                        <button class="btn btn-sm btn-secondary" type="submit"><i class="fa fa-btn fa-search"></i> 検索</button>
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
                                <tr>
                                    <td>99..9</td>
                                    <td>XX..X</td>
                                    <td>XX..X</td>
                                    <td>
                                        <a href="#" class="btn btn-sm btn-secondary">編集</a>
                                        <a href="#" class="btn btn-sm btn-secondary">削除</a>
                                    </td>
                                </tr>
                                <tbody>
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

@section('css')
{{-- ページごとCSSの指定
    <link rel="stylesheet" href="/css/xxx.css">
    --}}
@stop

@section('js')
<script>
    console.log('ページごとJSの記述');
</script>
@stop