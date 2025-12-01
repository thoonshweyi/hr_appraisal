<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Grade;
use App\Models\AssFormCat;
use App\Models\FormResult;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
use Illuminate\Support\Facades\DB;
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

        if ($request->filter_sub_section_id) {
            $results = $results->whereHas('employee',function($query) use($request){
                $query->where('sub_section_id', $request->filter_sub_section_id);
            });
        }

        $assesseeusers = $results->with([
            'employee.branch','employee.department','employee.position','employee.positionlevel'
        ])->get();

        $assessee_ids = $assesseeusers->pluck('id')->toArray();


    

        // $formresults = DB::table('form_results')
        //     ->join('appraisal_forms', function($q) use($appraisal_cycle_id){
        //         $q->on('appraisal_forms.id','=','form_results.appraisal_form_id')
        //         ->where('appraisal_forms.appraisal_cycle_id', $appraisal_cycle_id)
        //         ->whereNull('appraisal_forms.deleted_at');
        //     })
        //     // ->join('appraisal_form_assessee_users', function($q){
        //     //     $q->on('appraisal_form_assessee_users.appraisal_form_id', '=', 'appraisal_forms.id')
        //     //       ->whereNull('appraisal_form_assessee_users.deleted_at');
        //     // })
        //     ->join('ass_form_cats', 'ass_form_cats.id', '=', 'appraisal_forms.ass_form_cat_id')
        //     ->join('criterias', 'criterias.id', '=', 'form_results.criteria_id')
        //     ->join('users as assessor', 'assessor.id', '=', 'appraisal_forms.assessor_user_id')
        //     ->join('users as assessee', 'assessee.id', '=', 'form_results.assessee_user_id')
        //     ->select(
        //         'assessee.id as assessee_id',
        //         'assessee.name as assessee_name',

        //         'assessor.id as assessor_id',
        //         'assessor.name as assessor_name',

        //         'ass_form_cats.id as category_id',
        //         'ass_form_cats.name as category_name',

        //         'criterias.id as criteria_id',
        //         'criterias.name as criteria_question',

        //         'form_results.result'
        //     )
        //     ->whereNull('appraisal_forms.deleted_at')
        //     // ->whereIn('appraisal_form_assessee_users.assessee_user_id', [44])
        //     ->whereIn('form_results.assessee_user_id', $assessee_ids)
        //     ->orderBy('assessee.id')
        //     ->orderBy('category_id')
        //     ->orderBy('assessor_id')
        //     ->orderBy('criteria_id')
        //     ->get();
        // ---------------------------------------------------------------------------------

        \DB::enableQueryLog();
        $formresults = DB::table('appraisal_forms')
        ->join('appraisal_form_assessee_users', function($q) use($assessee_ids){
            $q->on('appraisal_form_assessee_users.appraisal_form_id', '=', 'appraisal_forms.id')
                ->whereNull('appraisal_form_assessee_users.deleted_at')
                ->whereIn('assessee_user_id',$assessee_ids);
        })
        ->leftjoin('form_results', function($q) {
            $q->on('form_results.appraisal_form_id', '=', 'appraisal_forms.id')
            ->whereColumn('form_results.assessee_user_id', '=', 'appraisal_form_assessee_users.assessee_user_id');
        })
        ->join('ass_form_cats', 'ass_form_cats.id', '=', 'appraisal_forms.ass_form_cat_id')
        ->leftjoin('criterias', 'criterias.id', '=', 'form_results.criteria_id')
        ->join('users as assessor', 'assessor.id', '=', 'appraisal_forms.assessor_user_id')
        ->leftJoin('employees as assessoremp', 'assessoremp.employee_code', '=', 'assessor.employee_id')
        ->leftjoin('users as assessee', 'assessee.id', '=', 'appraisal_form_assessee_users.assessee_user_id')
        ->leftJoin('employees as assesseeemp', 'assesseeemp.employee_code', '=', 'assessee.employee_id')
        ->select(
            'assessee.id as assessee_id',
            'assessee.name as assessee_name',
            'assesseeemp.employee_name as assessee_employee_name',

            'assessor.id as assessor_id',
            'assessor.name as assessor_name',
            'assessoremp.employee_name as assessor_employee_name',


            'ass_form_cats.id as category_id',
            'ass_form_cats.name as category_name',

            'criterias.id as criteria_id',
            'criterias.name as criteria_question',

            DB::raw('COALESCE(form_results.result, 0) as result')
        )
        ->where('appraisal_forms.appraisal_cycle_id', $appraisal_cycle_id)
        ->whereNull('appraisal_forms.deleted_at')
        ->orderBy('assessee.id')
        ->orderBy('category_id')
        ->orderBy('assessor_id')
        ->orderBy('criteria_id')
        ->get();

        // dd(\DB::getQueryLog());

        // dd($formresults);

        $report = [];
        $assessees = [];
        $categories = [];
        $assessors = [];
        $criteriaList = [];

        foreach ($formresults as $r) {

            // report tree
            $report[$r->assessee_id][$r->category_id][$r->assessor_id][$r->criteria_id] = $r->result;

            $assessees[$r->assessee_id] = (object)[
                'id' => $r->assessee_id,
                'name' => $r->assessee_name,
                'employee' => (object)[
                    'employee_name'       => $r->assessee_employee_name,
                    'code'       => $r->assessee_employee_code ?? null,
                    'department' => $r->assessee_department ?? null,
                    'position'   => $r->assessee_position ?? null,
                ]
            ];

            $categories[$r->category_id] = (object)[
                'id' => $r->category_id,
                'name' => $r->category_name
            ];

            $assessors[$r->assessee_id][$r->category_id][$r->assessor_id] = (object)[
                'id' => $r->assessor_id,
                'name' => $r->assessor_name,
                'employee' => (object)[
                    'employee_name'         => $r->assessor_employee_name,
                    'code'         => $r->assessor_employee_code ?? null,      
                    'department'   => $r->assessor_department ?? null,         
                    'position'     => $r->assessor_position ?? null,          
                ]
            ];

            if($r->criteria_id){
            $criteriaList[$r->category_id][$r->criteria_id] = (object)[
                'id' => $r->criteria_id,
                'question' => $r->criteria_question
            ];}

            $assesseeTotals[$r->assessee_id] = ($assesseeTotals[$r->assessee_id] ?? 0) + (int)$r->result;
            $assesseeAssessorCount[$r->assessee_id][$r->assessor_id] = true;
        }

        // dd($report);
        // dd($assessors);

        foreach($assesseeTotals as $id => $total){
            $assessees[$id]->total_score = $total;
            $assessees[$id]->assessor_count = count($assesseeAssessorCount[$id]);
            $average = $assessees[$id]->average_score = round($total /  count($assesseeAssessorCount[$id]));

            $grade = Grade::where('from_rate', '<=', $average)
            ->where('to_rate', '>=', $average)
            ->first();
            $assessees[$id]->grade = $grade ? $grade->name : '----';
        }
        // dd($assessees);

        return view('assesseesdetail.detail', [
            'assessees'      => $assessees,
            'appraisalcycle'     => AppraisalCycle::find($appraisal_cycle_id),
            'appraisal_cycle_id' => $appraisal_cycle_id,
            'report'             => $report,                // tree: [assessee][category][assessor][criteria] = result
            'categories'         => $categories,
            'criteriaList'       => $criteriaList,          // criteriaList[category_id][criteria_id] => object
            'assessors'          => $assessors              // assessors[assessee_id][category_id][assessor_id] => object
        ]);
    }

    
}


