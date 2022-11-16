@props(['placeholder' => '', 'maxlength' => '', 'type' => 'text', 'value' => '', 'name' => '', 'class' => '', 'disabled' => false])

<input class="form-control @error($name) is-invalid @enderror" type="{{ $type }}" name="{{ $name }}" maxlength="{{ $maxlength }}"
    value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}" @disabled($disabled)>
@error($name)
    <span class="invalid-feedback error-message" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
