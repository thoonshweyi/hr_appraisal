<?php

namespace App\Exports;


// use Illuminate\Support\Facades\Log;
// use Maatwebsite\Excel\Events\AfterSheet;
// use Maatwebsite\Excel\Concerns\WithEvents;
// use Maatwebsite\Excel\Concerns\WithMapping;
// use Maatwebsite\Excel\Concerns\WithDrawings;
// use Maatwebsite\Excel\Concerns\WithHeadings;

// use PhpOffice\PhpSpreadsheet\Style\Alignment;
// use Maatwebsite\Excel\Concerns\FromCollection;
// use Maatwebsite\Excel\Concerns\ShouldAutoSize;
// use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
// use Maatwebsite\Excel\Concerns\WithColumnWidths;
// use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
// use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AssesseeSummaryExport
{

    private $assesseesummarys;
    public function __construct($assesseesummarys)
    {
        $this->assesseesummarys = $assesseesummarys;
        dd($this->assesseesummarys);
    }

    public function collection()
    {
        //
    }
}
