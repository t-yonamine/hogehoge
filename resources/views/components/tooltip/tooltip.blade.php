@props(['title'])
<div data-toggle="tooltip" data-placement="top" title="{{ $title }}">
    {{ $slot }}
</div>
@section('js')
    <script src="{{ asset('/js/tem-script.js') }}" type="text/javascript"></script>
@stop
