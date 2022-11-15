@php
$effectMeasurement = [
['key'=> '実施日'],
['key'=> '実施者'],
['key'=> '合否'],
['key' => '']
];
@endphp

<div class="tab-pane" id="tabs-2" role="tabpanel">
    <div class="m-2">
        <div class="d-flex justify-content-between align-items-center">
            <h5>仮免前学科効果測定</h5>
            <button class="btn btn-primary mb-2">仮前新規</button>
        </div>
        <x-forms.student.table :data="[]" :listKey="$effectMeasurement" :hideButon="true">
        </x-forms.student.table>
    </div>
    <div class="m-2">
        <div class="d-flex justify-content-between align-items-center">
            <h5>卒検前学科効果測定</h5>
            <button class="btn btn-primary mb-2">卒前新規</button>
        </div>
        <x-forms.student.table :data="[]" :listKey="$effectMeasurement" :hideButon="true">
        </x-forms.student.table>
    </div>
</div>