<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Criteria;
use Illuminate\Http\Request;

class AssesseeSummaryController extends Controller
{


    public function review($assessee_user_id,$appraisal_cycle_id){

        $assesseeuser = User::where('id',$assessee_user_id)->first();
        // dd($assesseeuser);

        $ass_form_cat_id = $assesseeuser ? $assesseeuser->getAssFormCat()->id : '';
        $criterias = Criteria::where("ass_form_cat_id",$ass_form_cat_id)->get();

        return view('assesseesummary.review',compact("assesseeuser","criterias"));
    }
}
