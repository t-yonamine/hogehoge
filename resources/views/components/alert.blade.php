@props(['message', 'type'])

@if ($message && $type)
    @switch ($type)
        @case('error')
            <div class="alert alert-danger">
            @break
        @case('success')
            <div class="alert alert-success">
            @break
        @case('warning')
            <div class="alert alert-warning">
            @break
        @default
            <div>
    @endswitch
{{ $message }}
</div>
@endif
