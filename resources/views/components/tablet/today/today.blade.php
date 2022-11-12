@props(['data' => [], 'datepicker' => '', 'schoolStaffId' => '', 'periodNum' => ''])

@push('js')
    <script>
        $(function() {
            let date = "{{ $datepicker }}";
            if (date) {
                $(".datepicker").datepicker("setDate", new Date(date));
            }
        })
    </script>
@endpush

<div id="breadcrumb"><a href="{{ route('frt.index', ['datepicker' => $datepicker]) }}">ホーム</a>　＞　本日の業務（時限詳細）</div>
<div id="datetime">
    <form action="{{ route('frt.today.index') }}" method="GET">
        <div id="date">
            <input type="text" class="datepicker" readonly="readonly" name="period_date" for="datepicker"
                onchange='this.form.submit()'>
            <input type="hidden" name="period_num" value="{{ $periodNum }}">
        </div>
    </form>
    <div id="time">
        <div>
            @foreach ($data as $item)
                <div>
                    <div class="time">{{ $item?->period_name }}限目</div>
                    @foreach ($item->period as $period)
                        <div class="outline @if ($item->period_num == $periodNum) {{ 'now' }} @endif ">
                            <a
                                href="{{ route('frt.today.index', ['period_date' => $datepicker, 'period_num' => $item->period_num]) }}">
                                @if ($period?->period_type?->value == App\Enums\PeriodType::DRV_LESSON)
                                    {{ $period?->drlType?->cd_text }}
                                @elseif($period?->period_type?->value == App\Enums\PeriodType::LECTURE)
                                    {{ $period->stage }}段階{{ $period->curriculum_num }}
                                @elseif($period?->period_type?->value == App\Enums\PeriodType::TEST)
                                    @php
                                        $numOfParPeriods = App\Models\LessonAttend::countParticipants($period->test_id, $schoolStaffId)->groupBy('la_type');
                                    @endphp
                                    {{ $numOfParPeriods->map(function ($rs) {return $rs[0]->la_type->description;})->implode('、') }}
                                @elseif($period?->period_type?->value == App\Enums\PeriodType::WORK)
                                    {{ $period?->workType->cd_text }}
                                @endif
                            </a>
                        </div>
                    @endforeach
                </div>
            @endforeach
        </div>
    </div>
</div>
