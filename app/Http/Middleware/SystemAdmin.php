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
        if (!$user->school_id) {
            $role = $user->staff->role;
            if ($role == Role::SYS_ADMINISTRATOR || $role == Role::STAFF_MANAGER || $role == (Role::SYS_ADMINISTRATOR + Role::STAFF_MANAGER)) {
                return $next($request);
            }
        }
        Log::error("403 Error. Forbidden.");
        abort(403);
    }
}
