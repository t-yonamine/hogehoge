@php( $logout_url = View::getSection('logout_url') ?? config('adminlte.logout_url', 'logout') )
@if (config('adminlte.use_route_url', false))
    @php( $logout_url = $logout_url ? route($logout_url) : '' )
@else
    @php( $logout_url = $logout_url ? url($logout_url) : '' )
@endif

<div id="header">
    <div id="header_title">@stack('content_header')</div>
    <div id="header_data">
        @if (Auth::user())
            <div id="header_instructor">
                <em><img src="{{ asset('/tablet/images/tag.png') }}" alt="">指導員</em> {{ Auth::user()->getName() }}
            </div>
            <div id="header_exit">
                <a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <img src="{{ asset('/tablet/images/header_exit.png') }}" alt="">
                </a>
                <form id="logout-form" action="{{ $logout_url }}" method="POST" style="display: none;">
                    @if(config('adminlte.logout_method'))
                        {{ method_field(config('adminlte.logout_method')) }}
                    @endif
                    {{ csrf_field() }}
                </form>
            </div>
        @endif
    </div>
</div>
