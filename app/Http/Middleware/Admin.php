<?php

namespace App\Http\Middleware;

use App\Enums\StaffRole;
use Closure;
use Illuminate\Support\Facades\Auth;

class Admin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        $StaffRole = $user->staff?->role;
        if (!$user->school_id && ($StaffRole == StaffRole::SYS_ADMINISTRATOR || $StaffRole == StaffRole::MANAGER 
            || $StaffRole == (StaffRole::SYS_ADMINISTRATOR + StaffRole::MANAGER))) {
            return $next($request);
        }
        abort(403);
    }
}
