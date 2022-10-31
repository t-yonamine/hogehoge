<?php

namespace App\Http\Middleware;

use App\Enums\StaffRole;
use Closure;
use Illuminate\Support\Facades\Auth;

class SystemAdmin
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
        $role = $user->staff?->role;
        if (!$user->school_id && ($role == StaffRole::SYS_ADMINISTRATOR() || $role->value == (StaffRole::SYS_ADMINISTRATOR + StaffRole::MANAGER))) {
            return $next($request);
        }
        abort(403);
    }
}
