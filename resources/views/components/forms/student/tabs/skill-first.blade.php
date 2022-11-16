<!-- 技能-第1タブ -->
<div class="tab-pane" id="tabs-4" role="tabpanel">
    <div class="m-2">
        <!-- table 1 -->
        <h5>技能-第１</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '時限'], ['key'=> '実施日'], ['key'=> '実施項目'], ['key'=> '復讐項目'], ['key'=> '指導員'], ['key'=> '申し送り事項等']]">
        </x-forms.student.table>
    </div>
    <div class="m-2">
        <!-- table 2 -->
        <h5>みきわめ</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '実施者'], ['key'=> '結果']]">
        </x-forms.student.table>
    </div>
</div>