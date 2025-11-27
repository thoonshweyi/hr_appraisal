<?php

namespace App\Exports;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AssesseeDetailExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{

    private $assesseeusers;
    private $appraisal_cycle_id;
    private $totalRows;
    public function __construct($assesseeusers,$appraisal_cycle_id)
    {
        $this->assesseeusers = $assesseeusers;
        $this->appraisal_cycle_id = $appraisal_cycle_id;
        $this->totalRows = count($assesseeusers) + 1;
    }

    public function title(): string
    {
        return "Assesseee Detail";
    }

    // public function view(): View
    // {

    //     $appraisalcycle = AppraisalCycle::find($this->appraisal_cycle_id);
    //     $assesseedetail = new AssesseeDetail();

    //     return view('assesseesdetail.detail')
    //     ->with("assesseeusers",$this->assesseeusers)
    //     ->with('appraisal_cycle_id',$this->appraisal_cycle_id)
    //     ->with('assesseedetail',$assesseedetail)
    //     ->with('appraisalcycle',$appraisalcycle);
    // }

    public function view(): View
    {

        $assessee_ids = $this->assesseeusers->pluck('id')->toArray();


        $formresults = DB::table('form_results')
            ->join('appraisal_forms', function($q) {
                $q->on('appraisal_forms.id','=','form_results.appraisal_form_id')
                ->where('appraisal_forms.appraisal_cycle_id', $this->appraisal_cycle_id)
                ->whereNull('appraisal_forms.deleted_at');
            })
            // ->join('appraisal_form_assessee_users', function($q){
            //     $q->on('appraisal_form_assessee_users.appraisal_form_id', '=', 'appraisal_forms.id')
            //       ->whereNull('appraisal_form_assessee_users.deleted_at');
            // })
            ->join('ass_form_cats', 'ass_form_cats.id', '=', 'appraisal_forms.ass_form_cat_id')
            ->join('criterias', 'criterias.id', '=', 'form_results.criteria_id')
            ->join('users as assessor', 'assessor.id', '=', 'appraisal_forms.assessor_user_id')
            ->join('users as assessee', 'assessee.id', '=', 'form_results.assessee_user_id')
            ->select(
                'assessee.id as assessee_id',
                'assessee.name as assessee_name',

                'assessor.id as assessor_id',
                'assessor.name as assessor_name',

                'ass_form_cats.id as category_id',
                'ass_form_cats.name as category_name',

                'criterias.id as criteria_id',
                'criterias.name as criteria_question',

                'form_results.result'
            )
            ->whereNull('appraisal_forms.deleted_at')
            // ->whereIn('appraisal_form_assessee_users.assessee_user_id', [44])
            ->whereIn('form_results.assessee_user_id', $assessee_ids)
            ->orderBy('assessee.id')
            ->orderBy('category_id')
            ->orderBy('assessor_id')
            ->orderBy('criteria_id')
            ->get();

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
                'name' => $r->assessee_name
            ];

            $categories[$r->category_id] = (object)[
                'id' => $r->category_id,
                'name' => $r->category_name
            ];

            $assessors[$r->assessee_id][$r->category_id][$r->assessor_id] = (object)[
                'id' => $r->assessor_id,
                'name' => $r->assessor_name
            ];

            $criteriaList[$r->category_id][$r->criteria_id] = (object)[
                'id' => $r->criteria_id,
                'question' => $r->criteria_question
            ];

            $assesseeTotals[$r->assessee_id] = ($assesseeTotals[$r->assessee_id] ?? 0) + (int)$r->result;
            $assesseeAssessorCount[$r->assessee_id][$r->assessor_id] = true;
        }

        // dd($report);
        // dd($assessors);

        foreach($assesseeTotals as $id => $total){
            $assessees[$id]->total_score = $total;
            $assessees[$id]->assessor_count = count($assesseeAssessorCount[$id]);
            $assessees[$id]->average_score = round($total /  count($assesseeAssessorCount[$id]));
        }
        // dd($assessees);

        return view('assesseesdetail.detail', [
            'assessees'      => $assessees,
            'appraisalcycle'     => AppraisalCycle::find($this->appraisal_cycle_id),
            'appraisal_cycle_id' => $this->appraisal_cycle_id,
            'report'             => $report,        // tree: [assessee][category][assessor][criteria] = result
            'categories'         => $categories,    // meta (optional, you can keep)
            'criteriaList'       => $criteriaList,  // criteriaList[category_id][criteria_id] => object
            'assessors'          => $assessors      // assessors[assessee_id][category_id][assessor_id] => object
        ]);
    }




    public function registerEvents(): array
    {
        return [
            // AfterSheet::class => function (AfterSheet $event) {
            //     $sheet = $event->sheet->getDelegate();

            //     $sheet->getStyle('A1:P1000')
            //     ->getAlignment()
            //     ->setVertical(Alignment::VERTICAL_CENTER)
            //     ->setHorizontal(Alignment::HORIZONTAL_CENTER);
            // },
        ];
    }

}
