@php
$drivingAptitude = [
['key'=> '実施日', 'type' => 'date'],
['key'=> '実施者'],
['key'=> '検査方式'],
['key'=> '総合'],
['key'=> '性格1'],
['key'=> '性格2'],
['key'=> 'A'],
['key'=> 'B'],
['key'=> 'C'],
['key'=> 'D'],
['key'=> 'E'],
['key'=> 'F'],
['key'=> 'G'],
['key'=> 'H'],
['key'=> 'I'],
['key'=> 'J'],
['key'=> 'K'],
['key'=> 'L'],
['key'=> 'M'],
['key'=> 'N'],
['key'=> 'O'],
['key'=> 'P'],
['key' => '']
];
@endphp

<div class="tab-pane" id="tabs-1" role="tabpanel">
    <div class="m-2">
        <button class="btn btn-primary mb-2 float-right">新規登録</button>
        <!-- OD式 -->
        <x-forms.student.table :data="[]" :listKey="$drivingAptitude" :hideButon="true">
        </x-forms.student.table>
        <!-- K2式 todo -->
    </div>
</div>