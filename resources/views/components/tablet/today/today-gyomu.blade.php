@props(['period' => null, 'codePeriod' => null, 'codeWord' => null, 'route' => '', 'disabled' => false])
<form method="POST" action="{{ $route }}" autocomplete="off">
    @csrf
    @method('PUT')
    <x-tablet.partials.period-header :period="$period" :codePeriod="$codePeriod" :codeWord="$codeWord" :action="App\Enums\PeriodAction::REDIRECT_LINK" />

    <div id="student_comment">
        <div id="student_comment_title">備考</div>
        <div id="student_comment_input">
            <x-tablet.forms.textarea name="remarks" value="{{ old('remarks', $period->remarks) }}"
                disabled="{{ $disabled }}" />
        </div>
    </div>

    <x-tablet.partials.footer :action="App\Enums\PeriodAction::UPDATE_WORK" :disabled="$disabled" />
</form>
