<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Grade;
use App\Models\Criteria;
use App\Models\FormResult;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AssesseeSummary;
use App\Exports\AppraisalExport;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AssesseeSummaryExport;
use App\Models\AppraisalFormAssesseeUser;

class AssesseeSummaryController extends Controller
{


    public function review($assessee_user_id,$appraisal_cycle_id){

        $assesseeuser = User::where('id',$assessee_user_id)->first();
        // dd($assesseeuser);

        $appraisal_form_ids = AppraisalForm::where('appraisal_cycle_id',$appraisal_cycle_id)
                            ->whereHas('assesseeusers',function($query) use($assesseeuser){
                                $query->where('assessee_user_id',$assesseeuser->id);
                            })->pluck('id');
        $criteria_ids = FormResult::whereIn("appraisal_form_id",$appraisal_form_ids)->pluck("criteria_id");
        $criterias = Criteria::whereIn("id",$criteria_ids)->orderBy("id")->get();

        $criteria_totals = [];
        foreach($criterias ?? [] as $criteria){
            $criteria_totals[$criteria->id] = $this->getCriteriaTotal($assessee_user_id,$criteria->id,$appraisal_cycle_id);
        }

        $ratetotal = array_sum($criteria_totals);


        // Get Assessors
        $assessor_user_ids = AppraisalForm::where('appraisal_cycle_id',$appraisal_cycle_id)
                        ->whereHas('assesseeusers',function($query) use($assesseeuser){
                            $query->where('assessee_user_id',$assesseeuser->id);
                        })->pluck('assessor_user_id');
        $assessorusers = User::whereIn("id",$assessor_user_ids)->get();
        $assessoruserscount = count($assessorusers);


        $average = floor($ratetotal / $assessoruserscount);


        $grade = Grade::where('from_rate', '<=', $average)
              ->where('to_rate', '>=', $average)
              ->first();

        // dd($grade);

        $assesseesummary = new AssesseeSummary();


        return view('assesseesummary.review',compact("assesseeuser","criterias",'criteria_totals','ratetotal','assessorusers','assessoruserscount','average','grade','assesseesummary'));
    }


    public function getCriteriaTotal($assessee_user_id,$criteria_id,$appraisal_cycle_id){

        $criteriatotal = FormResult::where('assessee_user_id',$assessee_user_id)
                            ->where('criteria_id',$criteria_id)
                            ->whereHas('appraisalform',function($query) use($appraisal_cycle_id){
                                $query->where('appraisal_cycle_id',$appraisal_cycle_id);
                            })->sum('result');
        // dd($criteriatotal);

        return $criteriatotal;

    }

    public function export(Request $request,$appraisal_cycle_id){

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


        // $response = Excel::download(new AssesseeSummaryExport($assesseeusers,$appraisal_cycle_id), "AssesseeSummaryReport".Carbon::now()->format('Y-m-d').".xlsx");
        $response = Excel::download(new AppraisalExport($assesseeusers,$appraisal_cycle_id), "AppraisalReport".Carbon::now()->format('Y-m-d').".xlsx");

        return $response;
    }
}
