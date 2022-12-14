@props([
    'period' => null,
    'optionCode' => [],
    'optionSchoolCode' => [],
    'optionCarModel' => [],
    'optionNumberCar' => [],
    'selectDisabled' => false,
])
@push('js')
    <script>
        $("#modal_add").on("change", "#period_type_l", function(e) {

            let optionCode = @json($optionCode);
            let optionSchoolCode = @json($optionSchoolCode);

            var value = this.value;
            let subTaskEle = $(this).parents("td").find('select[name="sub_task"]');
            let roomCdEle = $(this).parents("#nippou_table").find('select[name="room_cd"]');
            $(subTaskEle).html("");
            $(roomCdEle).html("");

            if (value == {{ App\Enums\PeriodTypeL::DRV_LESSON }}) {
                optionSchoolCode.forEach(item => {
                    if (item.cd_name == "drl_type") {
                        $(subTaskEle).append(
                            `<option value = "${item.cd_value}">${item.cd_text}</option>`);
                    }
                });
                optionCode.forEach(item => {
                    if (item.cd_name == "course_type") {
                        $(roomCdEle).append(
                            `<option value = "${item.cd_value}">${item.cd_text}</option>`);
                    }
                });
            } else if (value == {{ App\Enums\PeriodTypeL::LECTURE }}) {
                optionSchoolCode.forEach(item => {
                    if (item.cd_name == "room_cd") {
                        $(roomCdEle).append(
                            `<option value = "${item.cd_value}">${item.cd_text}</option>`);
                    }
                });
            } else if (value == {{ App\Enums\PeriodTypeL::TEST }}) {
                optionCode.forEach(item => {
                    if (item.cd_name == "course_type") {
                        $(roomCdEle).append(
                            `<option value = "${item.cd_value}">${item.cd_text}</option>`);
                    }
                });
            } else if (value == {{ App\Enums\PeriodTypeL::WORK }}) {
                optionCode.forEach(item => {
                    if (item.cd_name == "work_type") {
                        $(subTaskEle).append(
                            `<option value = "${item.cd_value}">${item.cd_text}</option>`);
                    }
                });
            }
        });

        $("#modal_add").on("change", "#car_model", function(e) {
            let optionCarModel = @json($optionCarModel);
            let optionNumberCar = @json($optionNumberCar);
            var value = this.value;

            let carModel = $(this).parents("td").find('select[name="car_model"]');
            let numberCar = $(this).parents("#nippou_table").find('select[name="number_car"]');
            $(numberCar).html("");

            optionNumberCar.forEach(item => {
                if (item.car_type_cd == value) {
                    $(numberCar).append(
                        `<option value = "${item.id}">${item.lesson_car_num}</option>`);
                }
            });

        });
    </script>
@endpush
<div class="modal" id="modal_add">
    <div class="modal_inner">
        <div class="modal_close"><img src="{{ asset('/tablet/images/modal_close.png') }}" alt=""></div>
        <div class="modal_content">
            <div class="modal_date"><em>???</em>???????????????</div>
            <div class="modal_title">
                {{ App\Helpers\Helper::getStringFormatDate($period?->period_date, 'Y/d/m') }}???{{ $period?->schoolPeriodM->period_name }}??????
            </div>
            <div class="modal_text">
                <form action="{{ route('frt.today.newPeriod') }}" method="POST">
                    @csrf
                    <input name="period_date" value="{{ $period?->period_date }}" hidden />
                    <input name="period_num" value="{{ $period?->period_num }}" hidden />
                    <input name="period_id" value="{{ $period->id }}" hidden />
                    <table id="nippou_table">
                        <tr>
                            <th>????????????<em>??????</em></th>
                            <td>
                                <div>
                                    <select id="period_type_l" name="period_type_l" @disabled($selectDisabled)>
                                        @foreach ($optionCode as $key => $item)
                                            @if ($item->cd_name == 'period_type_l')
                                                <option value="{{ $item->cd_value }}"
                                                    @if (old('period_type_l', $period?->period_type) == $item->cd_value) selected @endif>
                                                    {{ $item->cd_text }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                    <select name="sub_task" @disabled($selectDisabled)>
                                        @if ($period->drl_type)
                                            @foreach ($optionSchoolCode as $key => $item)
                                                @if ($item->cd_name == 'drl_type')
                                                    <option value="{{ $item->cd_value }}"
                                                        @if (old('sub_task', $period?->drl_type) == $item->cd_value) selected @endif>
                                                        {{ $item->cd_text }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @elseif($period->work_type)
                                            @foreach ($optionCode as $key => $item)
                                                @if ($item->cd_name == 'work_type')
                                                    <option value="{{ $item->cd_value }}"
                                                        @if (old('sub_task', $period?->work_type) == $item->cd_value) selected @endif>
                                                        {{ $item->cd_text }}
                                                    </option>
                                                @endif
                                            @endforeach
                                        @endif

                                    </select>
                                </div>
                                <div class="form-input">
                                    @error('period_type_l')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @error('sub_task')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <th>??????????????????</th>
                            <td>
                                <select id="room_cd" name="room_cd" @disabled($selectDisabled)>
                                    @if ($period->room_cd)
                                        @foreach ($optionSchoolCode as $key => $item)
                                            @if ($item->cd_name == 'room_cd')
                                                <option value="{{ $item->cd_value }}"
                                                    @if (old('room_cd', $period->room_cd) == $item->cd_value) selected @endif>
                                                    {{ $item->cd_text }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @else
                                        @foreach ($optionCode as $key => $item)
                                            @if ($item->cd_name == 'course_type' && $period->course_type_cd)
                                                <option value="{{ $item->cd_value }}"
                                                    @if (old('room_cd', $period->course_type_cd) == $item->cd_value) selected @endif>
                                                    {{ $item->cd_text }}
                                                </option>
                                            @endif
                                        @endforeach
                                    @endif
                                </select>
                                @error('room_cd')
                                    <span class="invalid-feedback error-message" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </td>
                        </tr>
                        <tr>
                            <th>???????????????</th>
                            <td>
                                <div>
                                    <select id="car_model" name="car_model" @disabled($selectDisabled)>
                                        @foreach ($optionCarModel as $key => $item)
                                            <option value="{{ $item->car_type_cd }}"
                                                @if (old('car_model', $period?->dispatchCars?->lessonCar->car_type_cd) == $item->cd_value) selected @endif>
                                                {{ $item->cd_text }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <select name="number_car" @disabled($selectDisabled)>
                                        @foreach ($optionNumberCar as $key => $item)
                                            @if ($item->car_type_cd == $period->dispatchCars?->lessonCar?->car_type_cd)
                                                <option value="{{ $item->id }}"
                                                    @if (old('car_model', $period?->dispatchCars?->lessonCar?->lesson_car_num) == $item->lesson_car_num) selected @endif>
                                                    {{ $item->lesson_car_num }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-input">
                                    @error('car_model')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                    @error('number_car')
                                        <span class="invalid-feedback error-message" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </td>
                        </tr>
                    </table>
                    @if (!$selectDisabled)
                        <div id="nippou_button"><button type="submit" class="button180x50">????????????</button></div>
                    @endif
                </form>
            </div>
        </div>
    </div>
</div>
