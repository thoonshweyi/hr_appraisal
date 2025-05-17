<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\WithMultipleSheets;

class AppraisalExport implements WithMultipleSheets
{
    private $assesseeusers;
    private $appraisal_cycle_id;
    public function __construct($assesseeusers,$appraisal_cycle_id)
    {
        $this->assesseeusers = $assesseeusers;
        $this->appraisal_cycle_id = $appraisal_cycle_id;
    }



    public function sheets(): array
    {
        return [
            new AssesseeSummaryExport($this->assesseeusers,$this->appraisal_cycle_id),
            new AssesseeDetailExport($this->assesseeusers,$this->appraisal_cycle_id)
        ];
    }
}
