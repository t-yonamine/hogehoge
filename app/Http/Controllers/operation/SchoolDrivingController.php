<?php

namespace App\Http\Controllers\operation;

use App\Enums\Role;
use App\Enums\Status;
use App\Http\Controllers\Controller;
use App\Models\School;
use App\Models\Staff;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SchoolDrivingController extends Controller
{

    public function __construct()
    {
        $this->middleware(function (Request $request, Closure $next) {
            $user = Auth::user();
            $exitStaff = Staff::where('id', $user->id)->first();
            if (!(!$user->school_id && $exitStaff->role == Role::SYS_ADMINISTRATOR)) {
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
        $models = School::buildQuery($request->input())->where('status', '!=', Status::DISABLE)
            ->orderBy('school_cd')->paginate();
        return view('operation.school-driving.index', ['models' => $models]);
    }

    /**
     * @Route('/school-driving/{id}', method: 'DELETE', name: 'school-driving.delete')
     */
    public function delete($id)
    {
        $model = School::where('id', $id)->first();
        if (!$model) {
            return redirect()->route('school-driving.index')->with('error', 'データは削除されました。または存在していません。');
        } else {
            $model->status = Status::DISABLE;
            $model->save();
        }
        return redirect()->route('school-driving.index')->with('success', 'データを削除しました。');
    }
}
