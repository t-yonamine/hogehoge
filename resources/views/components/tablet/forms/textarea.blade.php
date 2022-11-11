@props(['placeholder' => '全体の申し送り事項があれば入力する', 'name' => '', 'value' => '', 'maxlength' => 100, 'disabled' => false])
<textarea placeholder="{{ $placeholder }}" name="{{ $name }}" value="{{ $value }}"
    maxlength="{{ $maxlength }}" @disabled($disabled)>{{ $value }}</textarea>
