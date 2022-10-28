<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use App\Models\Staff;
use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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
        $role = $user->staff->role;
        if (!$user->school_id && ($role == Role::SYS_ADMINISTRATOR || $role == (Role::SYS_ADMINISTRATOR + Role::STAFF_MANAGER))) {
            return $next($request);
        }
        abort(403);
    }
}
