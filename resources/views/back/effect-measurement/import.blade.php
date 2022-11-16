@extends('adminlte::page')

@section('title', '効果測定結果インポート')

@section('content_header')
    <h1>効果測定結果インポート</h1>
@stop

@section('css')
    <style>
        .custom-file-button input[type="file"] {
            margin-left: -2px !important;
        }

        .custom-file-button input[type="file"]::-webkit-file-upload-button {
            display: none;
        }

        .custom-file-button input[type="file"]::file-selector-button {
            display: none;
        }

        .custom-file-button:hover label {
            background-color: #dde0e3;
            cursor: pointer;
        }
    </style>
@stop
@php
    $header = ['行番号', '教習生番号', 'フリガナ', '実施日付', '問題番号', '点数', '合否', 'テスト区分', '満点種別', '除外', 'メッセージ'];
    $arrCalldata = ['student_no', 'name_kana', 'period_date_text', 'question_num', 'score', 'result_text', 'la_type_text', 'perfect_score_text'];
@endphp
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            @if (!isset($data))
                <div class="card">
                    <form class="flex flex-col w-full" enctype="multipart/form-data" method="post"
                        action="{{ route('effect-measurement.import.upload') }}" validate>
                        @csrf
                        <div class="card-body">
                            <table class="table table-bordered table-view">
                                <tr>
                                    <th class="w-20">ファイル</th>
                                    <td>
                                        <div class="input-group custom-file-button">
                                            <label class="input-group-text rounded-0" for="file-upload">参照</label>
                                            <input type="button" id="inputFile"
                                                class="form-control @error('files') is-invalid @enderror file-show"
                                                style="text-align: left">
                                            <input id="file-upload" type="file" class="d-none" title=""
                                                name="files" accept=".csv" required>
                                            <span class="invalid-feedback" style="margin-left: 55px;" role="alert">
                                                <strong class="error-size"
                                                    hidden>{{ __('messages.MSE00005', ['label' => '100']) }}</strong>
                                                <strong class="error-file" hidden>{{ __('messages.MSE00006') }}</strong>
                                                @error('files')
                                                    <strong>{{ $message }}</strong>
                                                @enderror
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="card-footer">
                            <div class="col text-center">
                                <button class="btn btn-sm btn-secondary btn-import" type="submit">
                                    取込
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            @else
                <div class="card" id="block-b">
                    <div class="flex flex-col w-full">
                        <div class="card-body">
                            <div class="dataTables_wrapper dt-bootstrap4">
                                <div class="row">
                                    <div class="col-sm-12">
                                        <table class="table table-bordered table-hover dataTable dtr-inline table-import">
                                            <thead>
                                                <tr>
                                                    @foreach ($header as $itemHeader)
                                                        <th class="sorting" rowspan="1" colspan="1" aria-label="">
                                                            {{ $itemHeader }}</th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @if (!isset($data))
                                                    <tr>
                                                        <td colspan="100%" class="text-center">データがありません。</td>
                                                    </tr>
                                                @else
                                                    @foreach ($data as $key => $item)
                                                        <tr>
                                                            <td class="text-center">{{ $key + 1 }}</td>
                                                            @foreach ($arrCalldata as $val)
                                                                <td class="align-middle {{ $val }}">
                                                                    {{ isset($item[$val]) ? trim($item[$val]) : '' }}
                                                                </td>
                                                            @endforeach
                                                            <td class="text-center align-middle checkitem">
                                                                <input type="checkbox" name="disabled" class="isCheckbox"
                                                                    id="{{ $key }}" @disabled($item['disabled'])
                                                                    @checked($item['disabled'])>
                                                            </td>
                                                            <td class="message">
                                                                @if (isset($item['error']))
                                                                    <p class="text-danger mb-0">
                                                                        {!! nl2br(e($item['error'])) !!}
                                                                    </p>
                                                                @endif
                                                                @if (isset($item['success']))
                                                                    <p class="text-success mb-0">
                                                                        {!! nl2br(e($item['success'])) !!}
                                                                    </p>
                                                                @endif
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
                        <div class="card-footer">
                            <div class="col text-center">
                                <a class="btn btn-sm btn-secondary cursor-pointer"
                                    href="{{ route('effect-measurement.import') }}">
                                    キャンセル
                                </a>
                                <button class="btn btn-sm btn-primary cursor-pointer" type="button"
                                    onclick="submitInsert()">
                                    保存
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card d-none" id="block-c">
                    <div class="flex flex-col w-full">
                        <div class="card-body">
                            【登録結果】
                            <table class="table table-bordered table-view">
                                <tr>
                                    <th class="w-20">登録行数:</th>
                                    <td id="number_of_registered_lines">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">除外行数: </th>
                                    <td id="excluded_rows">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">エラー行数: </th>
                                    <td id="number_of_error_lines">
                                    </td>
                                </tr>
                                <tr>
                                    <th class="w-20">合計行数: </th>
                                    <td id="total_rows">
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
@stop
@section('js')
    <script>
        const req = @json($data);
        const fileName = @json($fileName);
        var MAX_FILE_SIZE = 100 * 1024; // 100kb
        $('#file-upload').on('change', function(event) {
            $('#inputFile').attr('value', $(this)?.val().split('\\').pop())
            var validExts = 'csv';
            var fileExt = $(this).val().split('.').pop();
            if (fileExt == validExts) {
                $('.error-file').attr('hidden', true);
                const {
                    files
                } = event.target;
                if (files[0].size > MAX_FILE_SIZE) {
                    $(this).addClass('is-invalid');
                    $('.error-size').removeAttr('hidden');
                } else {
                    $(this).removeClass('is-invalid');
                    $('.error-size').attr('hidden', true);
                    $('.btn-import').attr('disabled', false);
                }
            } else {
                $(this).addClass('is-invalid');
                $('.error-size').attr('hidden', true);
                $('.error-file').removeAttr('hidden');
            }
        })

        $('.isCheckbox:checkbox').on('change', function(event) {
            const data = event.target;
            req.forEach((element, key) => {
                if (key == data.id) {
                    req[key].disabled = data.checked;
                }
            });
        })

        function submitInsert() {
            var uploadurl = '{{ route('effect-measurement.import.insert') }}';
            $.ajaxSetup({
                url: uploadurl,
                type: 'post',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                data: {
                    req,
                    fileName
                },
                cache: false,
                success: function(response) {
                    if (response.status == 200) {
                        $('#block-c').removeClass('d-none');
                        $('#block-c').addClass('d-block');
                        $('#block-b').addClass('d-none');
                        $('#number_of_registered_lines').html(`${response.data['number_of_registered_lines']}`);
                        $('#excluded_rows').html(`${response.data['excluded_rows']}`);
                        $('#number_of_error_lines').html(`${response.data['number_of_error_lines']}`);
                        $('#total_rows').html(`${response.data['total_rows']}`);
                    }
                }
            });
        }
        $('#inputFile').on('click', function() {
            $('#file-upload').click();
        })
    </script>
@stop
