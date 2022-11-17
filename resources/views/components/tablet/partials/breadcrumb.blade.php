@props(['breadcrumb' => []])
@php
    $firstArray = 0;
@endphp
@if (isset($breadcrumb))
    <div id="breadcrumb">
        @foreach ($breadcrumb as $index => $item)
            @if ($index !== $firstArray)
                　＞　
            @endif
            @if ($index == count($breadcrumb) - 1)
                {{ $item['label'] }}
            @else
                <a href="{{ $item['url'] }}">{{ $item['label'] }}</a>
            @endif
        @endforeach
    </div>
@endif
