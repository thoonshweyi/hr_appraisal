<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
use App\Models\AppraisalFormAssesseeUser;

class AssesseeDetailController extends Controller
{
    public function exportview(Request $request,$appraisal_cycle_id){

        $assessee_user_ids = AppraisalFormAssesseeUser::whereHas("appraisalform",function($query) use($appraisal_cycle_id){
            $query->where('appraisal_cycle_id',$appraisal_cycle_id);
        })
        ->groupBy('assessee_user_id')->pluck("assessee_user_id");
        $assesseeusers = User::whereIn("id",$assessee_user_ids);



        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;

        // $results = PeerToPeer::query();
        $results = $assesseeusers;


        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_name){
                $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
            });
        }

        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_code){
                $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%');
            });
        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_branch_id){
                $query->where('branch_id', $filter_branch_id);
            });
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
                $query->where('position_level_id', $filter_position_level_id);
            });
        }

        $assesseeusers = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
        ->get();

        $appraisalcycle = AppraisalCycle::find($appraisal_cycle_id);
        $assesseedetail = new AssesseeDetail();

        return view('assesseesdetail.detail')->with('assesseeusers',$assesseeusers)->with('appraisal_cycle_id',$appraisal_cycle_id)->with('assesseedetail',$assesseedetail)->with('appraisalcycle',$appraisalcycle);

    }
}
