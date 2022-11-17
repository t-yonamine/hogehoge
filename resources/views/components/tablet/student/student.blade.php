@push('breadcrumb')
    @php
        $breadcrumb = [['label' => 'ホーム', 'url' => route('frt.index')], ['label' => '教習生検索', 'url' => '#'], ['label' => '教習生情報', 'url' => '']];
    @endphp
    <x-tablet.partials.breadcrumb :breadcrumb="$breadcrumb" />
@endpush
<div id="student_header">
    <div id="step">
        <div class="on"><em>1</em>
            <div>教習前</div>
        </div>
        <div class="on"><em>2</em>
            <div>１段階教習中</div>
        </div>
        <div class="on"><em>3</em>
            <div>修検待機</div>
        </div>
        <div class="on"><em>4</em>
            <div>仮免待機</div>
        </div>
        <div class="on"><em>5</em>
            <div>２段階教習中</div>
        </div>
        <div><em>6</em>
            <div>卒検待機</div>
        </div>
        <div><em>7</em>
            <div>卒業</div>
        </div>
    </div>
    <div id="header_date">
        <table>
            <tr>
                <th>教習期限</th>
                <td>2023/10/5</td>
            </tr>
            <tr>
                <th>仮免許有効期限</th>
                <td>2023/10/5</td>
            </tr>
            <tr>
                <th>卒業検定受検期限</th>
                <td>2023/10/5</td>
            </tr>
        </table>
    </div>
</div>
<div id="student_profile">
    <table>
        <tr class="r1">
            <th class="h1 c1">教習番号</th>
            <td class="b1 c2">1234567</td>
            <th class="h1 c1">教習車種</th>
            <td class="b1 c2">普通自動車MT</td>
            <td rowspan="4" class="photo c3">
                <div><img src="sampleimages/student_1.png" alt=""></div>
                <div id="tokkijikou_title">特記事項</div>
                <div id="tokkijikou_body">特例教習受講済み</div>
            </td>
        </tr>
        <tr class="r2">
            <th class="h2 ">氏名（カナ）</th>
            <td class="b2"><ruby>乃井万<rt>ノイマン</rt></ruby>　<ruby>太郎<rt>タロウ</rt></ruby></td>
            <th class="h2">生年月日</th>
            <td class="b3">平成15年10月10日生　（<em>17歳</em>）男</td>
        </tr>
        <tr class="r3">
            <th class="h2">住所</th>
            <td class="b3" colspan="3">千葉県銚子市*********</td>
        </tr>
        <tr class="r4">
            <td colspan="4" class="step">
                <div id="student_profile_step">
                    <div class="h">第1<br>段階</div>
                    <div class="ginou">技<br>能</div>
                    <div class="b">
                        <div><em>10</em>時間</div>
                    </div>
                    <div class="gakka">学<br>科</div>
                    <div class="b">
                        <div><em>13</em>時間</div>
                    </div>
                    <div class="h">第2<br>段階</div>
                    <div class="ginou">技<br>能</div>
                    <div class="b">
                        <div><em>3</em>時間</div>
                    </div>
                    <div class="gakka">学<br>科</div>
                    <div class="b">
                        <div><em>5</em>時間</div>
                    </div>
                    <div class="hl">所持免許<br>有効期限</div>
                    <div class="limit">原付免許<br>2023/10/5</div>
                </div>
            </td>
        </tr>
    </table>
</div>
