<div class="tab-pane" id="tabs-3" role="tabpanel">
    <div class="m-2">
        <!-- table 1 -->
        <h5>修了検定</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '検定員'], ['key'=> '結果']]">
        </x-forms.student.table>
    </div>
    <div class="m-2">
        <!-- table 2 -->
        <h5>仮免学科試験</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '管理者'], ['key'=> '結果']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 3 -->
        <h5>修了検定補修教習</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '指導員'], ['key'=> '補修項目等']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 4 -->
        <h5>卒業検定</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '検定員'], ['key'=> '結果']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 5 -->
        <h5>卒業検定補修教習</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '指導員'], ['key'=> '補修項目等']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 6 -->
        <h5>任意教習</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '指導員'], ['key'=> '教習項目等']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 7 -->
        <h5>自由教習</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '実施日'], ['key'=> '指導員'], ['key'=> '教習項目等']]">
        </x-forms.student.table>

    </div>
    <div class="m-2">
        <!-- table 8 -->
        <h5>技能教習時限数</h5>
        <x-forms.student.table :data="[]" :listKey="[['key'=> '第一段階'], ['key'=> '第二段階'], ['key'=> '小計'], ['key'=> '修検補修'], ['key'=> '卒検補修'], ['key'=> 'その他'], ['key'=> '合計']]">
        </x-forms.student.table>
    </div>
</div>