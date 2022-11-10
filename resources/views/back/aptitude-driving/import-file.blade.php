@extends('adminlte::page')

@section('title', '運転適性検査インポート')

@section('content_header')
    <h1>運転適性検査インポート</h1>
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
    $header = ['実施日', '教習生番号', '氏名', '運転適性', '安全運転', '特異反応', '性格1', '性格2', 'A', 'B', 'C', 'D', 'E', 'F', 'G', 'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', '除外', 'メッセージ'];
    $arrCalldata = ['date', 'student_no', 'name', 'od_drv_aptitude', 'od_safe_aptitude', 'od_specific_rxn', 'od_persty_pattern_1', 'od_persty_pattern_2', 'od_a', 'od_b', 'od_c', 'od_d', 'od_e', 'od_f', 'od_g', 'od_h', 'od_i', 'od_j', 'od_k', 'od_l', 'od_m', 'od_n', 'od_o', 'od_p'];
@endphp
@section('content')
    <x-alert />
    <div class="row">
        <div class="col-12">
            <div class="card">
                <form class="flex flex-col w-full" action="{{ route('aptitude-driving.import.upload') }}"
                    enctype="multipart/form-data" method="post" validate>
                    @csrf
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tr>
                                <th class="w-20">ファイル</th>
                                <td>
                                    <div class="input-group custom-file-button">
                                        <label class="input-group-text rounded-0" for="file-upload">ファイルを選択</label>
                                        <input type="button" id="inputFile"
                                            class="form-control @error('files') is-invalid @enderror file-show"
                                            style="text-align: left" value="{{ old('files', $fileName) }}">
                                        <input id="file-upload" type="file" class="d-none" title="" name="files"
                                            accept=".csv" required>
                                        <span class="invalid-feedback" style="margin-left: 130px;" role="alert">
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
                            <button class="btn btn-sm btn-secondary btn-import" type="submit" @disabled(isset($data))>
                                取込
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="card">
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
                            <button class="btn btn-sm btn-primary cursor-pointer" onclick="submitInsert()"
                                @disabled(!isset($data))>
                                保存
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('js')
    <script>
        const req = @json($data);
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
            var uploadurl = '{{ route('aptitude-driving.import.insert') }}';
            $.ajaxSetup({
                url: uploadurl,
                type: 'post',
                headers: {
                    'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content'),
                }
            });
            $.ajax({
                data: {
                    req
                },
                cache: false,
                success: function(response) {
                    if (response.status == 200) {
                        $('.table-import').find('tbody tr').each((index, element) => {
                            let key = $(element).find('input[type="checkbox"]').attr('id');
                            let disabled = $(element).find('input[type="checkbox"]').attr('disabled');
                            let row = response.data.find(rs => rs.id == key);
                            let error = row?.error;
                            let success = row?.success;
                            if (error && disabled != 'disabled') {
                                $(element).find('.message').html(`<p class="text-danger mb-0">
                                                                    ${error}
                                                                </p>`);
                            } else if (success) {
                                $(element).find('.message').html(`<p class="text-success mb-0">
                                                                    ${success}
                                                                </p>`);
                                $(element).find('.checkitem, .isCheckbox').prop('disabled', 'true')
                                    .prop('checked', 'true');
                            }
                        })
                    }
                }
            });
        }
        $('#inputFile').on('click', function() {
            $('#file-upload').click();
        })
    </script>
@stop
