<div>
    <div class="modal fade" id="examinerAllocationModal" tabindex="-1" role="dialog"
        aria-labelledby="examinerAllocationModal" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="card">
                    <div class="card-body">
                        <table class="table table-bordered table-view">
                            <tbody>
                                <tr>
                                    <th class="w-20">指導員番号</th>
                                    <td><input name="school_staff_no" class="form-control" value="" type="text"
                                            class="form-control" placeholder="" maxlength="16">
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card  modal-body">
                    <div class="card-body">
                        <div class="dataTables_wrapper dt-bootstrap4">
                            <div class="row">
                                <div class="col-sm-12 tbl-overflow">
                                    <table class="table table-bordered table-hover dataTable dtr-inline">
                                        <thead>
                                            <tr style="position: sticky; top: -2px; z-index: 1;">
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">指導員番号
                                                </th>
                                                <th class="sorting" rowspan="1" colspan="1" aria-label="">指導員名
                                                </th>
                                                <th></th>
                                            </tr>
                                        </thead>
                                        <tbody id="tbl-examiner">
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<form id="formErr" method="POST" action="{{route('apply-test.error-page')}}">
    @csrf
    <input type="hidden" name="status_code">
</form>

@section('js')
    <script>
        $(function() {
            var dataTable = [];
            var lessonAttendId;
            $('.examiner-allocation-button').on('click', function() {
                lessonAttendId = $(this).attr('data-id');
                let url = '{{ route('apply-test.examiner-allocation-regis.ajax') }}';
                $('#tbl-examiner').html(``);
                $.ajax({
                    type: "GET",
                    url: url,
                    async: false,
                    data: {
                        lesson_attend_id: lessonAttendId
                    },
                    cache: false,
                    success: function(res) {
                        $('#examinerAllocationModal').modal('show');
                        if (res.data) {
                            var existSchoolStaff = res.exist_school_staff_id;
                            dataTable = res.data;
                            dataTable.forEach(element => {
                                var selected = existSchoolStaff.findIndex(
                                    schoolStaffId =>
                                    schoolStaffId == element.id);
                                $('#tbl-examiner').append(appendRowTbl(element,
                                    selected >= 0 ?
                                    'disabled' : ''));
                            });
                        } else {
                            $('#tbl-examiner').append(`<tr>
                                                            <td colspan="100%" class="text-center">データがありません。</td>
                                                    </tr>`);
                        }
                    },
                    error: function(error) {
                        if (error) {
                            $('#formErr input[name="status_code"]').val(error.status);
                            $('#formErr').submit();
                        }
                    }
                });
            });

            $('#examinerAllocationModal input[name="school_staff_no"]').on('keyup', function(event) {
                $('#tbl-examiner').html('');
                const value = $(this).val().trim();
                if (!value || value == '') {
                    dataTable.forEach(element => {
                        $('#tbl-examiner').append(appendRowTbl(element));
                    });
                } else {
                    var dataTmp = dataTable.filter((row) => row.name === value);
                    if (dataTmp && dataTmp.length > 0) {
                        dataTmp.forEach(element => {
                            $('#tbl-examiner').append(appendRowTbl(element));
                        });
                    } else {
                        $('#tbl-examiner').append(`<tr>
                                                                <td colspan="100%" class="text-center">データがありません。</td>
                                                    </tr>`);
                    }
                }
                $(this).val(value);
            });

            $('#tbl-examiner').on('click', '.selection-button', function() {
                let element = $(this);
                let url = '{{ route('apply-test.examiner-allocation-regis.ajax-save') }}';

                $.ajax({
                    type: "POST",
                    url: url,
                    async: false,
                    data: {
                        lesson_attend_id: lessonAttendId,
                        id_selected: element.attr('data-id')
                    },
                    cache: false,
                    success: function(res) {
                        $('.selection-button').prop('disabled', false);
                        element.prop('disabled', true);
                        $('#examinerAllocationModal').modal('hide');
                        dataTable = [];
                        lessonAttendId = '';
                        window.location.reload();
                    },
                    error: function(error) {
                        if (error) {
                            $('#formErr input[name="status_code"]').val(error.status);
                        }
                    }
                });
            });

            function appendRowTbl(row, disabled) {
                return `<tr id=${row.id}>
                            <td>${row.school_staff_no}</td>
                            <td class="staff-name">${row.name}</td>
                            <td class="text-center d-flex justify-content-center">
                                <button type="button" ${disabled}
                                    class="btn btn-sm btn-secondary selection-button ml-1"
                                    data-id='${row.id}'>選択
                                </button>
                            </td>
                        </tr>`;
            }
        })
    </script>
@stop
