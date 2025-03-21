<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssesseeSummary extends Model
{
    use HasFactory;


    public function getAssessorGivenMark($assessor_user_id,$assessee_user_id,$criteria_id,$appraisal_cycle_id){

        $formresult = FormResult::whereHas('appraisalform',function($query) use($assessor_user_id,$appraisal_cycle_id){
            $query->where('assessor_user_id',$assessor_user_id)
                ->where('appraisal_cycle_id',$appraisal_cycle_id);
        })
        ->where('assessee_user_id',$assessee_user_id)
        ->where('criteria_id',$criteria_id)
        ->first();

        $result = $formresult ? $formresult->result : '';

        // dd($result);
        return $result;
    }

    public function getAssesseeAssFormCats($assessee_user_id,$appraisal_cycle_id){
        $ass_form_cat_ids = AppraisalForm::where('appraisal_cycle_id',$appraisal_cycle_id)
        ->whereHas('assesseeusers',function($query) use($assessee_user_id){
            $query->where('assessee_user_id',$assessee_user_id);
        })->pluck('ass_form_cat_id');
        $assformcats = AssFormCat::whereIn("id",$ass_form_cat_ids)->get();
        // dd($assformcats);
    }
}
