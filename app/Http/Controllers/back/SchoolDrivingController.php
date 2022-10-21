<?php

namespace App\Http\Controllers\back;

use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolDrivingController extends Controller
{

    const ROLE_ADMIN = 1;
    const SCHOOL_STATUS_DISABLE = 0;

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $exitStaff = Staff::where('staff_no', $user->login_id)->first();
            if (!$exitStaff || $exitStaff->role != self::ROLE_ADMIN) {
                abort(403);
            }

            return $next($request)
                ->header('Cache-Control', 'no-store, must-revalidate');
        });
    }
    /**
     * @Route('/school-driving', method: 'GET', name: 'school-driving.index')
     */
    public function index(Request $request)
    {
        $models = School::buildQuery($request->input())->where('status', '!=', self::SCHOOL_STATUS_DISABLE)->orderBy('id')->paginate();
        return view('back.school-driving.index', ['models' => $models]);
    }

    /**
     * @Route('/school-driving/{id}', method: 'DELETE', name: 'school-driving.delete')
     */
    public function delete($id)
    {
        $model = School::where('id', $id)->first();
        if (!$model) {
            abort(404);
        } else {
            $model->status = self::SCHOOL_STATUS_DISABLE;
            $model->deleted_user_id = Auth::id();
            $model->deleted_user_id = Auth::id();
            $model->save();
        }
        return redirect()->route('school-driving.index')->with('success', 'データを削除しました。');
    }
}
