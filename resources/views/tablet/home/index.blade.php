@push('content_header')
    <h1>本日の業務</h1>
@endpush
@push('css-customs')
    <link href="{{ asset('/tablet/css/today.css') }}" rel="stylesheet" type="text/css">
@endpush

@push('js')
    <script>
        $(function() {
            let date = '{{ $datepicker }}';
            if (date) {
                $('.datepicker').datepicker('setDate', date);
            }
            $('.next').on('click', function() {
                var date = $('.datepicker').datepicker('getDate')
                date.setDate(date.getDate() + 1)
                $('.datepicker').datepicker('setDate', date);
            })
            $('.prev').on('click', function() {
                var date = $('.datepicker').datepicker('getDate')
                date.setDate(date.getDate() - 1)
                $('.datepicker').datepicker('setDate', date);
            })
        })
    </script>
@endpush

<x-tablet.layout>
    @php
        $haveLearned = 1;
        $haveDispatched = 1;
    @endphp
    <table id="today_tt">
        <tr>
            <td class="fixed" colspan="2"></td>
            <form action="{{ route('frt.index') }}" method="GET">
                <td class="header fixed" colspan="2">
                    <div class="w-100">
                        <button class="prev">前日</button>
                        <input type="text" class="datepicker" readonly="readonly" name="datepicker" for="datepicker"
                            onchange='this.form.submit()'>
                        <button class="next">翌日</button>
                    </div>
                </td>
            </form>
        </tr>

        @if (isset($period_m))
            @foreach ($period_m as $periodMItem)
                <tr>
                    <th>
                        <em>{{ $periodMItem->period_name }}限目</em>
                        {{ $periodMItem->period_from->format('H:i') }} - {{ $periodMItem->period_to->format('H:i') }}
                    </th>
                    @if ($periodMItem->period->isEmpty())
                        <td colspan="100%"></td>
                    @else
                        @foreach ($periodMItem->period as $periodVal)
                            <td @class([
                                'category',
                                'gyoumu' => $periodVal->period_type->value === App\Enums\PeriodType::WORK,
                                'kentei' => $periodVal->period_type->value === App\Enums\PeriodType::TEST,
                                'gakkakyoushu' =>
                                    $periodVal->period_type->value === App\Enums\PeriodType::LECTURE,
                                'ginoukyoushu' =>
                                    $periodVal->period_type->value === App\Enums\PeriodType::DRV_LESSON,
                            ])>
                                {{ $periodVal->period_type->description }}</td>
                            <td class="outline">
                                <a href="#">
                                    @if ($periodVal->period_type->value === App\Enums\PeriodType::WORK)
                                        {{ $periodVal->work_type ? $periodVal->work_type->description : '' }}
                                    @elseif ($periodVal->period_type->value === App\Enums\PeriodType::TEST)
                                        @php
                                            $numOfParPeriods = App\Models\LessonAttend::countParticipants($periodVal->test_id, $sessSchoolStaffId)->groupBy('la_type');
                                        @endphp
                                        {{ $numOfParPeriods->map(function ($rs) {return $rs[0]->la_type->description;})->implode('、') }}
                                    @elseif ($periodVal->period_type->value === App\Enums\PeriodType::LECTURE)
                                        {{ $periodVal->stage }}段階{{ $periodVal->curriculum_num }}
                                    @elseif ($periodVal->period_type->value === App\Enums\PeriodType::DRV_LESSON)
                                        {{ $periodVal->drl_type ? $periodVal->drl_type->description : '' }}
                                    @endif
                                </a>
                            </td>
                            <td class="detail">
                                @if ($periodVal->period_type->value === App\Enums\PeriodType::WORK)
                                    <div class="detail_line">
                                        <div class="detail_data">{{ $periodVal->remarks }}</div>
                                    </div>
                                @elseif ($periodVal->period_type->value === App\Enums\PeriodType::TEST)
                                    <div class="detail_line">
                                        <div class="detail_data">
                                            {{-- 2人（AT：1人）、3人（MT：2人、大型1人） --}}
                                            {{ $numOfParPeriods->map(function ($rs) {
                                                    $total = count($rs) . '人';
                                                    $licencedetails =
                                                        '（' .
                                                        $rs->map(function ($res) {
                                                                return App\Enums\LicenseType::getDescription((int) $res->target_license_cd) .
                                                                    '：' .
                                                                    $res->total .
                                                                    '人';
                                                            })->implode('、') .
                                                        '）';
                                                    return $total . $licencedetails;
                                                })->implode('、') }}

                                        </div>
                                        <div class="detail_status jisshizumi">実施済</div>
                                    </div>
                                @elseif ($periodVal->period_type->value === App\Enums\PeriodType::LECTURE)
                                    @foreach ($periodVal->lessonAttend as $lessonAtt)
                                        <div class="detail_line">
                                            <div class="detail_data">運転者の心得　{{ $lessonAtt->period_id }}名</div>
                                            {{-- <div class="detail_status uketsukechu">受付中</div> --}}
                                        </div>
                                    @endforeach
                                @elseif ($periodVal->period_type->value === App\Enums\PeriodType::DRV_LESSON)
                                    @foreach ($periodVal->lessonAttend as $lessonAtt)
                                        @php
                                            $lessonItemMastery = $lessonAtt->lessonItemMastery->filter(function ($res) use ($lessonAtt) {
                                                return $res->stage == $lessonAtt->stage->value;
                                            });
                                        @endphp
                                        <div class="detail_line">
                                            <div class="detail_data">
                                                <a href="#">
                                                    {{ $lessonAtt->admCheckItem?->student_no }}
                                                </a>
                                                {{ $lessonAtt->admCheckItem?->name_kana }}
                                                {{-- F-4 進捗 --}}
                                                {{ '【' . $lessonAtt->stage . '-' . $lessonAtt->curriculum_num . '】' }}
                                                {{-- F-5 教習項目 --}}
                                                {{ count($lessonItemMastery) > 0? $lessonAtt->stage .'‐' .$lessonItemMastery->map(function ($res) {return $res->lesson_item_num;})->implode('、'): '' }}
                                                {{-- F-6 復習項目 --}}
                                                {{ $lessonItemMastery->map(function ($res) use($haveLearned) {return $res->re_lesson == $haveLearned ? '(' . $res->lesson_item_num . ')' : '';})->implode('') }}
                                                {{-- F-7 コース区分 --}}
                                                {{ $periodVal->codes?->cd_text }}
                                                {{-- F-8 教習車名 --}}
                                                {{ $lessonAtt->dsipatchCar->map(function ($dsip) {return $dsip->lessonCar->name;})->implode('、') }}
                                            </div>
                                            @if ($lessonAtt->status->value >= App\Enums\LessonAttendStatus::COMPLETED)
                                                <div class="detail_status haishazumi">
                                                    乗車済
                                                </div>
                                            @elseif ($lessonAtt->status->value < App\Enums\LessonAttendStatus::COMPLETED)
                                                <div class="detail_status mihaisha">
                                                    {{ $lessonAtt->dsipatchCar?->count('lesson_attends_id') >= $haveDispatched ? '配車済' : '未配車' }}
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                @endif
                            </td>
                        @endforeach
                    @endif

                </tr>
            @endforeach
        @endif
    </table>
</x-tablet.layout>
