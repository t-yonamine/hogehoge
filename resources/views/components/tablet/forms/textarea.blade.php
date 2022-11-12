@props(['placeholder' => '全体の申し送り事項があれば入力する', 'name' => '', 'value' => '', 'maxlength' => 100, 'disabled' => false])
<textarea placeholder="{{ $placeholder }}" name="{{ $name }}" value="{{ $value }}"
    class="@error($name) is-invalid @enderror" @disabled($disabled) maxlength="{{ $maxlength }}">{{ old($name, $value) }}</textarea>
@error($name)
    <span class="invalid-feedback error-message" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
