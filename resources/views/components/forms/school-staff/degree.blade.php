@props(['id', 'value' => [], 'key'])
@php
    $selected = [
        ['name' => App\Enums\Degree::getDescription(App\Enums\Degree::TEACHING), 'value' => App\Enums\Degree::TEACHING, 'key' => 'teacher'],
        ['name' => App\Enums\Degree::getDescription(App\Enums\Degree::MIKIWAME), 'value' => App\Enums\Degree::MIKIWAME, 'key' => 'mikiwame'],
        ['name' => App\Enums\Degree::getDescription(App\Enums\Degree::CERTIFICATION), 'value' => App\Enums\Degree::CERTIFICATION, 'key' => 'certification']
    ];
    $checked = $errors->any() ? old('qualification_'.$key) == '1' : !empty($value);
@endphp
<div class="border d-flex rounded @error($key) is-invalid @enderror">
    <div class="form-check m-2">
        <input class="form-check-input qualification-result" type="radio" value="0" id="checked-no-{{ $id }}"
            name="qualification_{{ $key }}" @checked(!$checked)>
        <label class="form-check-label" for="checked-no-{{ $id }}">
            資格なし
        </label>
    </div>

    <div class="form-check m-2">
        <input class="form-check-input qualification-result" type="radio" value="1" id="checked-yes-{{ $id }}"
            name="qualification_{{ $key }}" @checked($checked)>
        <label class="form-check-label" for="checked-yes-{{ $id }}">
            資格あり
        </label>
    </div>

    {{-- certificate --}}
    @foreach ($selected as $item)
    <div class="form-check m-2">
        <input class="form-check-input qualify-selected" type="checkbox" name="{{ $key.'[]' }}"
            id="{{$item['key']}}-{{ $key }}" value={{$item['value']}} @disabled(!$checked) @checked(array_search($item['value'], $value) !== false)>
        <label class="form-check-label" for="{{$item['key']}}-{{ $key }}">
            {{$item['name']}}
        </label>
    </div>
    @endforeach
    {{-- certificate end--}}
</div>
