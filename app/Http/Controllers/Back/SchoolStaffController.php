<?php

namespace App\Http\Controllers\Back;


use App\Enums\Status;
use App\Helpers\Helper;
use App\Http\Controllers\Controller;
use App\Models\SchoolStaff;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Lang;

class SchoolStaffController extends Controller
{

    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     * @Route('/', method: 'GET', name: 'school-staff.index')
     */
    public function index(Request $request)
    {

        $request->validate(
            [
                'school_staff_no' => 'nullable|regex:/^[a-zA-Z0-9]+$/'
            ],
            [
                'school_staff_no' => __('messages.MSE00004', ['label' => '職員番号'])
            ]
        );

        $school_id =  $request->session()->get('school_id');
        if (!$school_id) {
            return abort(403);
        }
        $user = Auth::user();
        Helper::checkRole($user->schoolStaff->role);

        $data = SchoolStaff::buildQuery($request->input())->where('school_id', $school_id)->where('status', Status::ENABLED())
            ->orderBy('school_staff_no', 'ASC')->paginate();

        return  view('back.school-staff.index', ['data' => $data]);
    }

    /**
     * @Route('/school-staff/{id}', method: 'DELETE', name: 'school-staff.delete')
     */
    public function delete($id)
    {
        $model = SchoolStaff::where('id', $id)->first();
        $authUser = Auth::user();
        $user = User::where('id', $id)->first();
        Helper::checkRole($authUser->schoolStaff->role);
        if (!$model) {
            return redirect()->route('school-staff.index')->with('error', Lang::get('messages.MSE00002'));
        } else {
            SchoolStaff::handleDelete($model, $user, $authUser);
        }
        return redirect()->route('school-staff.index')->with('success', Lang::get('messages.MSI00002'));
    }
}
