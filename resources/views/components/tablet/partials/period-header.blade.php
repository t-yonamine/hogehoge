@props(['period' => null, 'codePeriod' => null, 'codeWord' => null, 'action' => ''])
<input type="hidden" name="period_id" value="{{ $period->id }}">
<div id="today_detail">
    <div id="today_detail_date">{{ App\Helpers\Helper::getStringFormatDate($period?->period_date, 'Y') }}年
        <em>{{ App\Helpers\Helper::getStringFormatDate($period?->period_date, 'm/d') }}</em>
    </div>
    <div id="today_detail_time"><em>{{ $period?->schoolPeriodM->period_name }}</em>限目</div>
    <div id="today_detail_title">
        <div id="today_detail_title_outline">{{ $codePeriod->cd_text }}</div>
        <div id="today_detail_title_body">
            <div id="today_detail_title_body_title">{{ $codeWord->cd_text }}</div>
            <div id="today_detail_title_body_button"><button type="submit" name="action"
                    value="{{ $action }}">編集</button></div>
        </div>
    </div>
</div>
