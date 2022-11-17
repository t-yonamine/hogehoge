@props(['data' => []])
@php
    $arrayTable = [['label' => 'A', 'key' => 'od_a'], ['label' => 'B', 'key' => 'od_b'], ['label' => 'C', 'key' => 'od_c'], ['label' => 'D', 'key' => 'od_d'], ['label' => 'E', 'key' => 'od_e'], ['label' => 'F', 'key' => 'od_f'], ['label' => 'G', 'key' => 'od_g'], ['label' => 'H', 'key' => 'od_h'], ['label' => 'I', 'key' => 'od_i'], ['label' => 'J', 'key' => 'od_j'], ['label' => 'K', 'key' => 'od_k'], ['label' => 'L', 'key' => 'od_l'], ['label' => 'M', 'key' => 'od_m'], ['label' => 'N', 'key' => 'od_n'], ['label' => 'O', 'key' => 'od_o'], ['label' => 'P', 'key' => 'od_p']];
@endphp
<div id="student_data_area">
    @foreach ($data as $item)
        <table class="table_outer table_tekisei">
            <tr>
                <th rowspan="3">運転適性<br>検査<br>（{{ $item->test_type->description }}）</th>
                <td>
                    <table class="table_inner">
                        <tr>
                            <th class="sougou">総合</th>
                            <th class="pattern">パターン</th>
                            @foreach ($arrayTable as $itemHeader)
                                <th class="group">{{ $itemHeader['label'] }}</th>
                            @endforeach
                        </tr>
                        <tr>
                            <td class="sougou">{{ $item?->score }}</td>
                            <td class="pattern">{{ $item?->od_pattern }}</td>
                            @foreach ($arrayTable as $val)
                                <td class="group">{{ isset($item[$val['key']]) ? trim($item[$val['key']]) : '' }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <th class="kensain">検査員</th>
                            <td colspan="2" class="yyyymmdd">{{ $item?->test_date->format('Y/m/d') }}</td>
                            <td colspan="15">{{ $item?->schoolStaffs?->name }}</td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @endforeach
</div>
