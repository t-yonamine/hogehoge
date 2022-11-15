<!-- 学科-第1タブ -->
<div class="tab-pane" id="tabs-5" role="tabpanel">
    <div class="m-2">
        <!-- table 1 -->
        <h5>学科-第1タブ</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '教程番号'], ['key'=> '実施日'], ['key'=> '教習項目'], ['key'=> '教習項目'], ['key'=> '指導員'], ['key'=> '申し送り事項等']]">
        </x-forms.student.table>
    </div>
    <div class="m-2">
        <!-- table 2 -->
        <h5>修了資格確認管理者</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '確認日'], ['key'=> '確認日']]">
        </x-forms.student.table>
    </div>
</div>