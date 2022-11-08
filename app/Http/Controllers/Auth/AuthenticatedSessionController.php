<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request, $school_cd = null)
    {
        $uri = $request->fullUrl();

        $urlTablet = route('frt.login', $school_cd);

        //check PC OR Tablet
        if ($uri == $urlTablet) {
            $request->session()->put('tablet', true);
            return view('tablet.auth.login', ['schoolCd' => $school_cd]);
        } else {
            $request->session()->put('tablet', false);
            return view('auth.login', ['schoolCd' => $school_cd]);
        }
    }

    /**
     * Handle an incoming authentication request.
     *
     * @param  \App\Http\Requests\Auth\LoginRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(LoginRequest $request)
    {
        $request->authenticate();

        $request->session()->regenerate();

        if (Auth::user()?->school_id) {
            $request->session()->put('school_cd', $request->school_cd);
            $request->session()->put('school_id', Auth::user()->school_id);
            $request->session()->put('school_staff_id', Auth::user()->schoolStaff->id);
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Request $request)
    {
        Auth::guard('web')->logout();

        $schoolCd = session('school_cd');
        $tablet = session('tablet');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($tablet) {
            return redirect()->route('frt.login', $schoolCd);
        } else {
            return  redirect()->route('login', $schoolCd);
        }
    }
}
