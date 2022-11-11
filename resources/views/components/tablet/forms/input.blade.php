@props(['placeholder' => '', 'maxlength', 'type' => 'text', 'value' => '', 'name' => ''])

<div class="form-input">
    <input type="{{ $type }}" name="{{ $name }}" maxlength="{{ $maxlength }}"
        class="@error($name) is-invalid @enderror" value="{{ old($name, $value) }}" placeholder="{{ $placeholder }}">
    @error($name)
        <span class="invalid-feedback error-message" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
</div>
