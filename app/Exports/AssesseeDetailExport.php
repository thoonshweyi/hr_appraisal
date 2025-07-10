<?php

namespace App\Exports;
use App\Models\AppraisalCycle;
use App\Models\AssesseeDetail;
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

    public function view(): View
    {

        $appraisalcycle = AppraisalCycle::find($this->appraisal_cycle_id);
        $assesseedetail = new AssesseeDetail();

        return view('assesseesdetail.detail')
        ->with("assesseeusers",$this->assesseeusers)
        ->with('appraisal_cycle_id',$this->appraisal_cycle_id)
        ->with('assesseedetail',$assesseedetail)
        ->with('appraisalcycle',$appraisalcycle);
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
