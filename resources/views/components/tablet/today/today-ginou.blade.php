@props(['period' => null, 'codePeriod' => null, 'lessonAttend' => null, 'cdText' => '', 'route' => '', 'disabled' => false])
@push('js')
    <script>
        let errorComment = "{{ $errors->has('comment_text') }}";
        if (errorComment) {
            $('#modal_nippou').fadeIn(300);
        }
    </script>
@endpush
<form method="POST" autocomplete="off">
    @csrf
    @method('PUT')

    <x-tablet.partials.period-header :period="$period" :codePeriod="$codePeriod" :cdText="$cdText" :action="App\Enums\PeriodAction::REDIRECT_LINK" />
    <div id="students">
        @foreach ($lessonAttend as $item)
            <article>
                <input name="lessonAttendIds[]" type="hidden" value="{{ $item->id }}">
                <div class="photo"><img src="{{ $item?->image->image_info }}" alt=""></div>
                <div class="profile">
                    <div class="no"><a href="#">{{ $item?->admCheckItem?->student_no }}</a>
                    </div>
                    <div class="name">{{ $item?->admCheckItem?->name_kana }}</div>
                    <div class="mikiwame active">
                        @if ($item?->is_show_mikiwame)
                            [みきわめ]
                        @endif
                        @if ($item?->is_show_good)
                            良好
                        @endif
                    </div>
                    <div class="car">{{ $item?->dispatchCar->first()?->lessonCar?->name }}</div>
                    <div class="fuzai"><input name="{{ 'is_absent_' . $item->id }}" id="{{ 'is_absent_' . $item->id }}"
                            type="checkbox" @checked($item?->is_absent->value)>
                        <label for="{{ 'is_absent_' . $item->id }}">不在</label>
                    </div>
                    <div class="check"><button>開始時チェック</button></div>
                </div>
                <div class="data">
                    <div class="icon"></div>
                    <div class="dankai">段階　時限／<em>{{ $item->stage->description }} {{ $item->stage_la_num }}時限</em>
                    </div>
                    <div class="jisshi">
                        実施／<em>{{ $item?->lessonItemMastery->map(function ($res) {return $res->lesson_item_num;})->implode(' ') }}</em>
                    </div>
                    <div class="fukushu">
                        復習／<em>{{ $item?->lessonItemMastery->map(function ($res) {
                                return $res->re_lesson ? $res->lesson_item_num : '';
                            })->implode(' ') }}</em>
                    </div>
                    <div class="moushiokuri">申し送り／<em
                            class="text-truncate width-moushiokuri comment-text">{{ $item?->lessonComments?->comment_text }}</em>
                    </div>
                    <div class="nippou"><button type="button" class="modalOpen" id="#modal_nippou">日報</button></div>
                    <div class="shujuku"><button>習熟</button></div>
                    <input hidden disabled name="ledger_id" value="{{ $item->ledger_id }}">
                    <input hidden disabled name="lesson_attend_id" value="{{ $item->id }}">
                    <input hidden disabled name="comment_id" value="{{ $item->lessonComments?->id }}">
                </div>
            </article>
        @endforeach
    </div>
    <div id="student_register"><button onclick="modalOpen('#modal_registration');">教習生を登録する</button></div>
    <div id="student_comment">
        <div id="student_comment_title">備考</div>
        <div id="student_comment_input">
            <x-tablet.forms.textarea name="remarks" value="{{ old('remarks', $period->remarks) }}"
                disabled="{{ $disabled }}" />
        </div>
    </div>

    <x-tablet.partials.footer :action="App\Enums\PeriodAction::UPDATE_LESSON" disabled="{{ $disabled }}" />

</form>
{{-- modal nippou  --}}
<div class="modal" id="modal_nippou">
    <div class="modal_inner">
        <div class="modal_close"><img src="{{ asset('/tablet/images/modal_close.png') }}" alt=""></div>
        <div class="modal_content">
            <div class="modal_date">
                <em>■</em>学科教習の場合
            </div>
            <div id="nippou" class="modal_title">{{ old('name') }}</div>
            <div class="modal_text">
                <form action="{{ route('frt.today.comment') }}" method="POST">
                    @csrf
                    <input type="text" class="name" name="name" value="{{ old('name') }}" hidden />
                    <input type="text" class="ledger_id" name="ledger_id" value="{{ old('ledger_id') }}" hidden />
                    <input type="text" class="comment_id" name="comment_id" value="{{ old('comment_id') }}"
                        hidden />
                    <input type="text" class="lesson_attend_id" name="lesson_attend_id"
                        value="{{ old('lesson_attend_id') }}" hidden />
                    <input type="text" class="period_date" name="period_date"
                        value="{{ request()->query('period_date') }}" hidden />
                    <input type="text" class="period_num" name="period_num"
                        value="{{ request()->query('period_num') }}" hidden />
        
                    <table id="nippou_table">
                        <tr>
                            <th>申し送り事項</th>
                            <td>
                                <x-tablet.forms.input name="comment_text" maxlength="100"
                                    placeholder="申し送り事項を記載して下さい。" />
                            </td>
                        </tr>

                    </table>

                    <div class="mt-5" id="nippou_button">
                        <button class="button180x50 btn-comment">登録する</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
