<?php

namespace App\Exports;
use App\Models\Grade;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Events\AfterSheet;
use App\Services\AssessmentReportService;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;


class AssesseeDetailExport implements FromView, WithTitle, ShouldAutoSize, WithEvents
{

    private $assesseeusers;
    private $appraisal_cycle_id;
    private $totalRows;
    private $shareReport;
    public function __construct($assesseeusers,$appraisal_cycle_id,$shareReport)
    {
        $this->assesseeusers = $assesseeusers;
        $this->appraisal_cycle_id = $appraisal_cycle_id;
        $this->totalRows = count($assesseeusers) + 1;
        $this->shareReport = app(AssessmentReportService::class)->generate($assesseeusers,$appraisal_cycle_id);
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

        return view('assesseesdetail.detail', [
            'assessees'      => $this->shareReport->assessees,
            'appraisalcycle'     => AppraisalCycle::find($this->appraisal_cycle_id),
            'appraisal_cycle_id' => $this->appraisal_cycle_id,
            'report'             => $this->shareReport->report,        // tree: [assessee][category][assessor][criteria] = result
            'categories'         => $this->shareReport->categories,
            'criteriaList'       => $this->shareReport->criteriaList,  // criteriaList[category_id][criteria_id] => object
            'assessors'          => $this->shareReport->assessors      // assessors[assessee_id][category_id][assessor_id] => object
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
