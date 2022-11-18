@push('content_header')
    <h1>教習生情報</h1>
@endpush
@push('css-customs')
    <link href="{{ asset('/tablet/css/student.css') }}" rel="stylesheet" type="text/css">
@endpush
<x-tablet.layout>
    <x-tablet.student.student />
    <div id="student_data">
        @php
            $tabs = [['label' => '原簿', 'key' => 'original-book'], ['label' => '適性検査', 'key' => 'aptitude'], ['label' => '検定', 'key' => 'test'], ['label' => '技能-第1', 'key' => 'skills-1st'], ['label' => '学科-第1', 'key' => 'subject-1st'], ['label' => '技能-第2', 'key' => 'skills-2nd'], ['label' => '学科-第2', 'key' => 'subject-1nd'], ['label' => '履修証明', 'key' => 'completion']];
        @endphp
        <div id="student_tabs">
            @foreach ($tabs as $itemTab)
                <a href="{{ route('frt.student.detail', ['ledger_id' => $ledger_id, 'tab' => $itemTab['key']]) }}"
                    @class(['now' => $itemTab['key'] == $tab])>{{ $itemTab['label'] }}</a>
            @endforeach
        </div>
        <x-tablet.student.aptitude-test :data="$data" />
    </div>
</x-tablet.layout>
