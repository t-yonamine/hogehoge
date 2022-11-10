<x-tablet.layout>
    {{-- {{dump($lessonItemMastery)}} --}}
    <form method="POST" autocomplete="off">
        <div id="cantainer">
            <div id="left">
                <div id="left_logo">
                    <a href="index.html"><img src="/images/left_logo.png" alt="教習原簿"></a>
                </div>
                <nav>
                    <ul>
                        <li>
                            <a href="today.html"><img src="/images/menu_gyoumu.png" alt="">本日の業務</a>
                        </li>
                        <li>
                            <a href=""><img src="/images/menu_haisha.png" alt="">配車表</a>
                        </li>
                        <li>
                            <a href="search.html"><img src="/images/menu_search.png" alt="">教習性検索</a>
                        </li>
                        <li id="info">
                            <a href="#modal_info" class="modalOpen"><img src="/images/menu_info.png" alt="">お知らせ
                                <div class="notice_number">21</div>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>

            <div id="header">
                <div id="header_title">本日の業務</div>

                <div id="header_data">
                    <div id="header_instructor">
                        <em><img src="/images/tag.png" alt="">指導員</em> 自動太郎
                    </div>
                    <div id="header_exit">
                        <a href="index.html"><img src="/images/header_exit.png" alt=""></a>
                    </div>
                </div>
            </div>

            <div id="breadcrumb"><a href="">ホーム</a>　＞　本日の業務（時限詳細）</div>

            <div id="content">
                <div id="datetime">
                    <div id="date"><input type="text" class="datepicker" readonly="readonly"></div>
                    <div id="time">
                        <div>
                            <div>
                                <div class="time">0限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                            <div>
                                <div class="time">1限目</div>
                                <div class="outline"><a href="today_kentei.html">修了検定</a></div>
                            </div>
                            <div>
                                <div class="time">2限目</div>
                                <div class="outline"><a href="today_kentei.html">修了検定</a></div>
                            </div>
                            <div>
                                <div class="time">3限目</div>
                                <div class="outline"><a href="today_ginou.html">所内AT [1]</a></div>
                            </div>
                            <div>
                                <div class="time">4限目</div>
                                <div class="outline"><a href="today_ginou.html">1段階1</a></div>
                            </div>
                            <div>
                                <div class="time">5限目</div>
                                <div class="outline now"><a href="today_ginou.html">自主経路</a></div>
                            </div>
                            <div>
                                <div class="time">6限目</div>
                                <div class="outline"><a href="today_gakka.html">学科2</a></div>
                            </div>
                            <div>
                                <div class="time">7限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                            <div>
                                <div class="time">8限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                            <div>
                                <div class="time">9限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                            <div>
                                <div class="time">10限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                            <div>
                                <div class="time">11限目</div>
                                <div class="outline"><a href="today_yasumi.html">休み</a></div>
                            </div>
                        </div>
                    </div>
                </div>



                <div id="today_detail">
                    <div id="today_detail_date">
                        {{ substr($period->period_date, 0, 4) }}年<em>{{ substr($period->period_date, 5, 8) }}</em></div>
                    <div id="today_detail_time">
                        <em>{{ $period->period_num }}</em>{{ $period?->schoolPeriodM?->period_name }}
                    </div>
                    <div id="today_detail_title">
                        <div id="today_detail_title_outline">{{ $typeCode->cd_text }}</div>
                        <div id="today_detail_title_body">
                            <div id="today_detail_title_body_title">{{ $schoolCode->cd_text }}</div>
                            <div id="today_detail_title_body_button"><button>編集</button></div>
                        </div>
                    </div>
                </div>


                <div id="students">
                    @foreach ($lessonAttend as $item)
                        <article>
                            <div class="photo"><img src="/sampleimages/student_1.png" alt=""></div>
                            <div class="profile">
                                <div class="no"><a href="student.html">{{ $item?->admCheckItems?->student_no }}</a>
                                </div>
                                <div class="name">{{ $item?->admCheckItems?->name_kana }}</div>
                                <div class="mikiwame active">[みきわめ]</div>
                                <div class="car">{{ $item?->dispatchCars?->lessonCars?->name }}</div>
                                <div class="fuzai"><input name="is_absent" id="is_absent" type="checkbox">
                                    <label for="is_absent">不在</label>
                                </div>
                                <div class="check"><button disabled>開始時チェック</button></div>
                            </div>
                            <div class="data">
                                <div class="icon"></div>
                                <div class="dankai">段階　時限／<em>{{ $item->stage }} {{ $item->stage_la_num }}</em></div>
                                <div class="jisshi">実施／<em>{{ $item?->lessonItemMastery?->lesson_item_num }}</em>
                                </div>
                                <div class="fukushu">
                                    復習／<em>{{ $item?->lessonItemMastery?->lesson_item_num }}{{ $item?->lessonItemMastery?->re_lesson }}</em>
                                </div>
                                <div class="moushiokuri">申し送り／<em>{{ $item?->lessonComments?->comment_text }}</em>
                                </div>
                                <div class="nippou"><button disabled>日報</button></div>
                                <div class="shujuku"><button disabled>習熟</button></div>
                            </div>
                        </article>
                    @endforeach
                </div>


                <div id="student_register"><button onclick="modalOpen('#modal_registration');">教習生を登録する</button></div>


                <div id="student_comment">
                    <div id="student_comment_title">備考</div>
                    <div id="student_comment_input">
                        <textarea placeholder="全体の申し送り事項があれば入力する"></textarea>
                    </div>
                </div>

            </div>

            <div id="footer">
                <button>完了</button>
            </div>
        </div>
    </form>

    @push('css-customs')
        <link href="{{ asset('/tablet/css/base.css') }}" rel="stylesheet" type="text/css">
        <link href="{{ asset('/tablet/css/today_ginou.css') }}" rel="stylesheet" type="text/css">
    @endpush
</x-tablet.layout>
