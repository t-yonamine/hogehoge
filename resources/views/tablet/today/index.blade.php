<x-tablet.layout>
    {{-- add file css  --}}
    @push('css-customs')
        <link href="{{ asset('/tablet/css/today_ginou.css') }}" rel="stylesheet" type="text/css">
    @endpush
    <x-tablet.today.today :data="$periodM" :datepicker="$period_date" :schoolStaffId="$schoolStaffId" :periodNum="$periodNum" />

    @switch($period->period_type)
        @case(App\Enums\PeriodType::WORK())
            <x-tablet.today.today-gyomu :period="$period" :codePeriod="$codePeriod" :codeWord="$codeWord" :disabled="$disabled"
                action="{{ route('frt.today.update') }}">
            </x-tablet.today.today-gyomu>
        @break

        @case(App\Enums\PeriodType::DRV_LESSON())
            <x-tablet.today.today-ginou :period="$period" :codePeriod="$codePeriod" :lessonAttend="$lessonAttend" :schoolCode="$schoolCode"
                :disabled="$disabled" action="{{ route('frt.today.update') }}">
            </x-tablet.today.today-ginou>
        @break
    @endswitch

</x-tablet.layout>
