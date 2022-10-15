<?php

namespace App\Http\Action\Back\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StaffIndexAction extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function __invoke(Request $request)
    {
        //

        return response(view('back.staff.index'));
    }
}
