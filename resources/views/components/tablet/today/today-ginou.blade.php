@props(['period' => null, 'codePeriod' => null, 'lessonAttend' => null, 'schoolCode' => null, 'route' => '', 'isEnableForm' => false])
<form method="POST" autocomplete="off">
    @csrf
    @method('PUT')

    <x-tablet.partials.period-header :period="$period" :codePeriod="$codePeriod" :cdText="$schoolCode->cd_text" :action="App\Enums\PeriodAction::REDIRECT_LINK" />
    <div id="students">
        @foreach ($lessonAttend as $item)
            <article>
                <input name="lessonAttendIds[]" type="hidden" value="{{ $item->id }}">
                <div class="photo"><img src="{{ $item?->image->image_info }}" alt=""></div>
                <div class="profile">
                    <div class="no"><a href="student.html">{{ $item?->admCheckItem?->student_no }}</a>
                    </div>
                    <div class="name">{{ $item?->admCheckItem?->name_kana }}</div>
                    <div class="mikiwame active">[みきわめ]</div>
                    <div class="car">{{ $item?->dsipatchCar->first()?->lessonCar?->name }}</div>
                    <div class="fuzai"><input name="{{ 'is_absent_' . $item->id }}" id="{{ 'is_absent_' . $item->id }}"
                            type="checkbox" @checked($item?->is_absent->value)>
                        <label for="{{ 'is_absent_' . $item->id }}">不在</label>
                    </div>
                    <div class="check"><button>開始時チェック</button></div>
                </div>
                <div class="data">
                    <div class="icon"></div>
                    <div class="dankai">段階　時限／<em>{{ $item->stage->description }} {{ $item->stage_la_num }}時限</em></div>
                    <div class="jisshi">
                        実施／<em>{{ $item?->lessonItemMastery->map(function ($res) {return $res->lesson_item_num;})->implode(' ') }}</em>
                    </div>
                    <div class="fukushu">
                        復習／<em>{{ $item?->lessonItemMastery->map(function ($res) {
                                return $res->re_lesson ? $res->lesson_item_num : '';
                            })->implode(' ') }}</em>
                    </div>
                    <div class="moushiokuri">申し送り／<em
                            class="text-truncate width-moushiokuri">{{ $item?->lessonComments?->comment_text }}</em>
                    </div>
                    <div class="nippou"><button>日報</button></div>
                    <div class="shujuku"><button disabled>習熟</button></div>
                </div>
            </article>
        @endforeach
    </div>
    <div id="student_register"><button onclick="modalOpen('#modal_registration');">教習生を登録する</button></div>
    <div id="student_comment">
        <div id="student_comment_title">備考</div>
        <div id="student_comment_input">
            <x-tablet.forms.textarea name="remarks" value="{{ old('remarks', $period->remarks) }}"
                disabled="{{ !$isEnableForm }}" />
        </div>
    </div>

    <x-tablet.partials.footer :action="App\Enums\PeriodAction::UPDATE_LESSON" disabled="{{ !$isEnableForm }}" />

</form>
