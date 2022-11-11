<x-tablet.layout>
    <div id="datetime">
        <div id="date"><input type="text" class="datepicker" readonly="readonly" value="2022/01/02">
        </div>
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

    @switch($period->period_type)
        @case(App\Enums\PeriodType::WORK())
            <x-tablet.today.today-gyomu :period="$period" :codePeriod="$codePeriod" :codeWord="$codeWord"
                action="{{ route('frt.today.update') }}">
            </x-tablet.today.today-gyomu>
        @break

        @case(App\Enums\PeriodType::DRV_LESSON())
            <x-tablet.today.today-ginou :period="$period" :codePeriod="$codePeriod" :lessonAttend="$lessonAttend" :schoolCode="$schoolCode"
                :isEnableForm="$isEnableForm" action="{{ route('frt.today.update') }}">
            </x-tablet.today.today-ginou>
        @break

        @default
    @endswitch

    {{-- add file css  --}}
    @push('css-customs')
        <link href="{{ asset('/tablet/css/today_ginou.css') }}" rel="stylesheet" type="text/css">
    @endpush
</x-tablet.layout>
