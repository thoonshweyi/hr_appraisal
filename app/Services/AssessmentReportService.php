<?php

namespace App\Services;

use App\Models\Grade;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;


class AssessmentReportService{
    public function generate($assesseeusers,$appraisal_cycle_id){
        $assessee_ids = $assesseeusers->pluck('id')->toArray();
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
            // 'assessoremp.code as assessor_employee_code',
            // 'assessoremp.branch as assessor_employee_branch',
            // 'assessoremp.department as assessor_employee_department',
            // 'assessoremp.rank as assessor_employee_name',
            // 'assessoremp.position as assessor_employee_name',


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
        $criteriaTotals = [];

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

            $criteriaTotals[$r->assessee_id][$r->criteria_id] = ($criteriaTotals[$r->assessee_id][$r->criteria_id] ?? 0) + (int)$r->result;
        }

        // dd($report);
        // dd($criteriaTotals);

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

        return (object)[
            'report'=>$report,
            'assessees'=>$assessees,
            'categories'=>$categories,
            'assessors'=>$assessors,
            'criteriaList'=>$criteriaList,
            'criteriaTotals'=> $criteriaTotals
        ];
    }

}
?>
