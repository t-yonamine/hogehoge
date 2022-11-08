<x-tablet.layout>
    <div id="cantainer">
        <div id="logo"><img src="{{ asset('tablet/images/neumann.png') }}" alt=""></div>
        <div id="content">
            <div>
                <div id="title">{{ config('adminlte.title') }}</div>
                <form action="{{ route('frt.login') }}" method="POST">
                    @csrf
                    <div id="idpass">
                        <table>
                            <input type="hidden" name="school_cd" value="{{ $schoolCd }}">
                            <th>ID<em>必須</em></th>
                            <td>
                                <x-tablet.forms.input type="text" maxlength="16" placeholder="113568" name="login_id"
                                    value="{{ old('login_id') }}" />
                            </td>
                            </tr>
                            <tr>
                                <th>PASS<em>必須</em></th>
                                <td>
                                    <x-tablet.forms.input type="password" maxlength="20" placeholder="a1b2c3d4"
                                        name="password" value="{{ old('password') }}" />
                                </td>
                            </tr>
                            <tr>
                                <th></th>
                                <td>
                                    <!-- <div id="login_help">ログインできない方は、<a href="">こちらへ</a></div> -->
                                </td>
                            </tr>
                        </table>
                        <button type=submit>ログイン</button>
                    </div>
                </form>
            </div>
        </div>
        <div id="copy">Copyright © NEUMANN CO.LTD. All Rights Reserved.</div>
    </div>
</x-tablet.layout>
