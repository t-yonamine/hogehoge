<div class="modal" id="modal_nippou">
    <div class="modal_inner">
        <div class="modal_close"><img src="{{ asset('/tablet/images/modal_close.png') }}" alt=""></div>
        <div class="modal_content">
            <div class="modal_date">
                <em>■</em>学科教習の場合
            </div>
            <div id="nippou" class="modal_title"></div>
            <div class="modal_text">
                <form action="{{ route('frt.today.comment') }}" method="POST">
                    @csrf
                    <input type="text" class="ledger_id" name="ledger_id" value="" hidden />
                    <input type="text" class="comment_id" name="comment_id" value="" hidden />
                    <input type="text" class="lesson_attend_id" name="lesson_attend_id" value="" hidden />
                    <input type="text" class="period_date" name="period_date" value="{{request()->query('period_date')}}" hidden />
                    <input type="text" class="period_num" name="period_num" value="{{request()->query('period_num')}}" hidden />
                    <table id="nippou_table">
                        <tr>
                            <th>申し送り事項</th>
                            <td><input class="comment_text" type="text" placeholder="申し送り事項を記載して下さい。" name="comment_text"
                                    value="{{ old('comment_text') }}" required></td>
                        </tr>
                    </table>
                    <div id="nippou_button">
                        <button class="button180x50">登録する</button>
                    </div>
                </form>
            </div>

        </div>
    </div>
</div>
