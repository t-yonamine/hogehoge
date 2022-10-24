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
        $exitStaff = Staff::where('id', $user->id)->first();
        if ($user->school_id || $exitStaff->role != Role::SYS_ADMINISTRATOR) {
            Log::error("403 Error. Forbidden.");
            abort(403);
        }
        return $next($request);
    }
}
