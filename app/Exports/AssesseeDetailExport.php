<?php

namespace App\Exports;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromView;
class AssesseeDetailExport implements FromView, WithTitle
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

        
        return view('assesseesdetail.detail')->with("assesseeusers",$this->assesseeusers);
    }

}
