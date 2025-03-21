<?php

namespace App\Exports;


use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class AssesseeSummaryExport implements FromCollection, WithHeadings, WithDrawings, ShouldAutoSize, WithEvents, WithColumnFormatting, WithMapping,WithColumnWidths
{

    private $assesseeusers;
    private $appraisal_cycle_id;
    private $totalRows;
    public function __construct($assesseeusers,$appraisal_cycle_id)
    {
        $this->assesseeusers = $assesseeusers;
        $this->appraisal_cycle_id = $appraisal_cycle_id;
        $this->totalRows = count($assesseeusers) + 1;

        // dd($this->assesseeusers);
    }


    public function headings(): array
    {
        return [
            'Employee Name',
            'Employee Code',
            'Branch',
            'Department',
            'Rank',
            'Position',
            'Assessors',
            'RateTotal',
            'Average',
            'Grade',
        ];
    }

    public function collection()
    {
        $data = collect();

        foreach($this->assesseeusers as $assesseeuser){
            $assessors = $assesseeuser->getAssessors($this->appraisal_cycle_id);
            $assessoruserscount = $assesseeuser->getAssessorUsersCount($assessors);
            $criteria_totals = $assesseeuser->getCriteriaTotalArrs($this->appraisal_cycle_id);
            $ratetotal = $assesseeuser->getRateTotal($criteria_totals);
            $average = $assesseeuser->getAverage($ratetotal,$assessoruserscount);
            $grade = $assesseeuser->getGrade($average)->name;

            $data->push([
                'Employee Name' => $assesseeuser->employee->employee_name,
                'Employee Code'=> $assesseeuser->employee->employee_code,
                'Branch'=>  $assesseeuser->employee->branch->branch_name,
                'Department'=>  $assesseeuser->employee->department->name,
                'Rank' =>  $assesseeuser->employee->positionlevel->name,
                'Position' =>  $assesseeuser->employee->position->name,
                'Assessors' => $assessoruserscount,
                'RateTotal' => $ratetotal,
                'Average' => $average,
                'Grade' => $grade,
            ]);
        }

        return $data;
    }

    public function map($row): array
    {
        return [
            $row['Employee Name'],
            $row['Employee Code'],
            $row['Branch'],
            $row['Department'],
            $row['Rank'],
            $row['Position'],
            $row['Assessors'],
            $row['RateTotal'],
            $row['Average'],
            $row['Grade'],
        ];
    }



    public function drawings()
    {
        $drawings = [];


        return $drawings;
    }
    public function columnFormats(): array
    {
        return [];
    }
    public function columnWidths(): array
    {
        $columnWidthPx = 140;
        $columnWidth = $columnWidthPx / 7;
        return [
            // 'F' => $columnWidth,
            // 'G' => $columnWidth,
            // 'H' => $columnWidth,
            // 'I' => $columnWidth,
            // 'J' => $columnWidth,
            // 'K' => $columnWidth,
            // 'L' => $columnWidth,
            // 'M' => $columnWidth
        ];
    }
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();


                foreach (range(1, $this->totalRows) as $row) {
                    $rowHeightPx = $row == 1 ? 45 :35;
                    $rowHeight = $rowHeightPx * 0.75; // Convert pixels to Excel row height scale
                    $sheet->getRowDimension($row)->setRowHeight($rowHeight);
                }

                $sheet->getStyle('A1:J' . $this->totalRows)
                ->getAlignment()
                ->setVertical(Alignment::VERTICAL_CENTER)
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);


                // **Style Heading Row (Row 1)**
                $sheet->getStyle('A1:J1')->applyFromArray([
                    'font' => [
                        'bold' => true,        // Bold text
                        // 'size' => 18,          // Font size
                        'color' => ['rgb' => 'FFFFFF'], // White font color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'], // Blue background color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, // Center align text
                        'vertical' => Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Thin border
                        'color' => ['rgb' => 'BFBFBF'], // Light gray border
                    ],
                ],
                ]);

            },
        ];
    }

}
