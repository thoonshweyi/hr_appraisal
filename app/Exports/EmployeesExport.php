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


class EmployeesExport implements FromView, WithTitle, ShouldAutoSize
{

    private $employees;
    public function __construct($employees)
    {
        $this->employees = $employees;
    }


    public function title(): string
    {
        return "Employees List";
    }

    public function view(): View
    {
        return view('employees.export')->with("employees",$this->employees);
    }
}
