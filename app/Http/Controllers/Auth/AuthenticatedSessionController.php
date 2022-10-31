<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     *
     * @return \Illuminate\View\View
     */
    public function create($school_cd = null)
    {
        return view('auth.login', ['schoolCd' => $school_cd]);
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

        $school_cd = $request->session()->get('school_cd');

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($school_cd) {
            return redirect()->route('login', $school_cd);
        } else {
            return  redirect()->route('login');
        }
    }
}
