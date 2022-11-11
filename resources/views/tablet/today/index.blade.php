<x-tablet.layout>
    {{-- add file css  --}}
    @push('css-customs')
        <link href="{{ asset('/tablet/css/today_ginou.css') }}" rel="stylesheet" type="text/css">
    @endpush
    <x-tablet.today.today :data="$periodM" :datepicker="$period_date" :schoolStaffId="$schoolStaffId" :periodNum="$periodNum" />
    <x-tablet.today.today-gyomu :period="$period" :codePeriod="$codePeriod" :codeWord="$codeWord" :disabled="$disabled"
        action="{{ route('frt.today.update') }}">
    </x-tablet.today.today-gyomu>

</x-tablet.layout>
