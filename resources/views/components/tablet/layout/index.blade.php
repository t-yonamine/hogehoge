<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>

    {{-- Base Meta Tags --}}
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- Title --}}
    <title>
        @yield('title_prefix', config('adminlte.title_prefix', ''))
        @yield('title', config('adminlte.title', 'AdminLTE 3'))
        @yield('title_postfix', config('adminlte.title_postfix', ''))
    </title>

    {{-- Base Stylesheets --}}
    <link href="{{ asset('/tablet/css/common.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/tablet/css/base.css') }}" rel="stylesheet" type="text/css">
    <link href="{{ asset('/tablet/css/reset.css') }}" rel="stylesheet" type="text/css">

    {{-- Custom Stylesheets --}}
    @stack('css-customs')

    {{-- Custom Stylesheets --}}
    <script src="{{ asset('/tablet/js/jquery-3.4.1.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/tablet/js/jquery.easing.1.3.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/tablet/lib/jquery-ui.min.js') }}" type="text/javascript"></script>
    <script src="{{ asset('/tablet/lib/datepicker-ja.js') }}" type="text/javascript"></script>
    <link href="{{ asset('/tablet/lib/jquery-ui.min.css') }}" rel="stylesheet" type="text/css">
    <script src="{{ asset('/tablet/js/common.js') }}" type="text/javascript"></script>
</head>

<body>
    <div id="cantainer">
        @if (Auth::check())
            <x-tablet.partials.navleft />
            <x-tablet.partials.header />
            <div id="content">
                {{ $slot }}
            </div>
        @else
            {{ $slot }}
        @endif
    </div>
    <script src="{{ asset('/tablet/js/common.js') }}" type="text/javascript"></script>
    @stack('js')
</body>

</html>
