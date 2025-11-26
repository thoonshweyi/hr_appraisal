<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\AssFormCat;
use App\Models\FormResult;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
use Illuminate\Support\Facades\Log;
use App\Models\AppraisalFormAssesseeUser;

class AssesseeDetailController extends Controller
{
    // public function exportview(Request $request,$appraisal_cycle_id){

    //     $assessee_user_ids = AppraisalFormAssesseeUser::whereHas("appraisalform",function($query) use($appraisal_cycle_id){
    //         $query->where('appraisal_cycle_id',$appraisal_cycle_id);
    //     })
    //     ->groupBy('assessee_user_id')->pluck("assessee_user_id");
    //     $assesseeusers = User::whereIn("id",$assessee_user_ids);



    //     $filter_employee_name = $request->filter_employee_name;
    //     $filter_employee_code = $request->filter_employee_code;
    //     $filter_branch_id = $request->filter_branch_id;
    //     $filter_position_level_id = $request->filter_position_level_id;

    //     // $results = PeerToPeer::query();
    //     $results = $assesseeusers;


    //     if (!empty($filter_employee_name)) {
    //         $results = $results->whereHas('employee',function($query) use($filter_employee_name){
    //             $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
    //         });
    //     }

    //     if (!empty($filter_employee_code)) {
    //         $results = $results->whereHas('employee',function($query) use($filter_employee_code){
    //             $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%');
    //         });
    //     }

    //     if (!empty($filter_branch_id)) {
    //         $results = $results->whereHas('employee',function($query) use($filter_branch_id){
    //             $query->where('branch_id', $filter_branch_id);
    //         });
    //     }


    //     if (!empty($filter_position_level_id)) {
    //         $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
    //             $query->where('position_level_id', $filter_position_level_id);
    //         });
    //     }

    //     $assesseeusers = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
    //     ->get();


    //     foreach($assesseeusers as $assesseeuser){
    //         $assformcat_ids = AppraisalForm::where('appraisal_cycle_id',$appraisal_cycle_id)
    //                                         ->whereHas('assesseeusers',function($query) use($assesseeuser){
    //                                             $query->where('assessee_user_id',$assesseeuser->id);
    //                                         })->pluck('ass_form_cat_id');
    //         $assformcats = AssFormCat::whereIn("id",$assformcat_ids)->get();
    //         $assesseeuser["assformcats"] = $assformcats;
    //     }

    //     $appraisalcycle = AppraisalCycle::find($appraisal_cycle_id);
    //     $assesseedetail = new AssesseeDetail();


    //     return view('assesseesdetail.detail')
    //     ->with('assesseeusers',$assesseeusers)
    //     ->with('appraisal_cycle_id',$appraisal_cycle_id)
    //     ->with('assesseedetail',$assesseedetail)
    //     ->with('appraisalcycle',$appraisalcycle);

    // }

    public function exportview(Request $request, $appraisal_cycle_id)
{
    /* --------------------------------------------------------
       1. Load assessee users with filters (same as you do)
    -------------------------------------------------------- */
    $assessee_user_ids = AppraisalFormAssesseeUser::whereHas("appraisalform", function($q) use($appraisal_cycle_id){
        $q->where('appraisal_cycle_id', $appraisal_cycle_id);
    })
    ->groupBy('assessee_user_id')
    ->pluck('assessee_user_id');

    $results = User::whereIn("id", $assessee_user_ids);

    if ($request->filter_employee_name) {
        $results->whereHas('employee', fn($q) =>
            $q->where('employee_name','like','%'.$request->filter_employee_name.'%'));
    }
    if ($request->filter_employee_code) {
        $results->whereHas('employee', fn($q) =>
            $q->where('employee_code','like','%'.$request->filter_employee_code.'%'));
    }
    if ($request->filter_branch_id) {
        $results->whereHas('employee', fn($q) =>
            $q->where('branch_id',$request->filter_branch_id));
    }
    if ($request->filter_position_level_id) {
        $results->whereHas('employee', fn($q) =>
            $q->where('position_level_id',$request->filter_position_level_id));
    }

    $assesseeusers = $results->with([
        'employee.branch','employee.department','employee.position','employee.positionlevel'
    ])->get();

    $assessee_ids = $assesseeusers->pluck('id')->toArray();


    // $appraisal_forms = AppraisalForm::where('appraisal_cycle_id', $appraisal_cycle_id)
    //     ->with('assesseeusers') 
    //     ->get();

    // // Build mapping: assesse -> category -> assessors[]
    // $assessors_map = [];
    // $assformcats_map = [];

    // foreach ($appraisal_forms as $form) {
    //     foreach ($form->assesseeusers as $assessee) {
    //         $assessors_map[$assessee->id][$form->ass_form_cat_id][] = $form->assessor_user_id;
    //         $assformcats_map[$assessee->id][$form->ass_form_cat_id] = true;
    //     }
    // }


    // $all_cat_ids = collect($assformcats_map)->map(fn($arr)=>array_keys($arr))
    //     ->flatten()->unique();

    // $assformcats = AssFormCat::whereIn('id',$all_cat_ids)
    //     ->with('criterias')
    //     ->get()
    //     ->keyBy('id');

    // $formresults = FormResult::query()
    // ->select(
    //     'form_results.*',
    //     'appraisal_forms.ass_form_cat_id',
    //     'appraisal_forms.assessor_user_id'
    // )
    // ->join('appraisal_forms', 'appraisal_forms.id', '=', 'form_results.appraisal_form_id')
    // ->where('appraisal_forms.appraisal_cycle_id', $appraisal_cycle_id)
    // ->whereNull('appraisal_forms.deleted_at')
    // ->whereIn('assessee_user_id', $assessee_ids)
    // ->orderBy('appraisal_forms.id')
    // ->get();


    $formresults = FormResult::query()
    ->select(
        'form_results.*',
        'appraisal_forms.ass_form_cat_id',
        'appraisal_forms.assessor_user_id'
    )
    ->join('appraisal_forms', 'appraisal_forms.id', '=', 'form_results.appraisal_form_id')
    ->where('appraisal_forms.appraisal_cycle_id', $appraisal_cycle_id)
    ->whereNull('appraisal_forms.deleted_at')
    ->whereIn('assessee_user_id', [56])
    ->orderBy('appraisal_forms.id')
    ->get();

    // Build final structure
   $report = [];

    foreach ($formresults as $r) {
        $report[$r->assessee_user_id][$r->ass_form_cat_id][$r->assessor_user_id][$r->criteria_id]
            = $r->result;
    }
    dd($report);

    /* --------------------------------------------------------
        5. Send all massive preloaded arrays to Blade
    -------------------------------------------------------- */
    return view('assesseesdetail.detail', [
        'assesseeusers'      => $assesseeusers,
        'appraisalcycle'     => AppraisalCycle::find($appraisal_cycle_id),
        'appraisal_cycle_id' => $appraisal_cycle_id,
        'assessors_map'      => $assessors_map,
        'assformcats'        => $assformcats,
        'report'             => $report,
    ]);
}

    
}


