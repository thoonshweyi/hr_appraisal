<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\Criteria;
use App\Models\FormResult;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;

class AssesseeSummaryController extends Controller
{


    public function review($assessee_user_id,$appraisal_cycle_id){

        $assesseeuser = User::where('id',$assessee_user_id)->first();
        // dd($assesseeuser);

        $ass_form_cat_id = $assesseeuser ? $assesseeuser->getAssFormCat()->id : '';
        $criterias = Criteria::where("ass_form_cat_id",$ass_form_cat_id)->get();

        foreach($criterias as $criteria){
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


        return view('assesseesummary.review',compact("assesseeuser","criterias",'criteria_totals','ratetotal','assessorusers','assessoruserscount','average','grade'));
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
}
