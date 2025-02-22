<?php

namespace App\Imports;

use App\Models\Section;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Support\Str;
use App\Models\SubDepartment;
use App\Models\AgileDepartment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class PositionImport implements ToModel,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number

    public function model(array $row)
    {
        // dd($row);
        // Validate data
        // $row['department'] = Str::lower($row['department']);

        $validator = Validator::make($row, [
            'name'      => 'required|string|max:255|unique:positions,name',
            'division' => 'required|exists:divisions,name',
            // 'department' => 'required|exists:agile_departments,name',
            'department' => ['required',"exists:agile_departments,name"],
            'sub_department' => 'required|exists:sub_departments,name',
            'section' => 'required|exists:sections,name',
        ]);
        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {
            // dd($row);
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );
        }

        // Proceed with saving the data if validation passes
        $user = Auth::user();
        $user_id = $user["id"];

        $this->rowNumber += 1;


        return new Position([
            'name'      => $row['name'],
            'slug'      => Str::slug($row['name']),
            "division_id"=> Division::where('name',$row['division'])->first()->id,
            "department_id"=> AgileDepartment::where('name',$row['department'])->first()->id,
            "sub_department_id"=> SubDepartment::where('name',$row['sub_department'])->first()->id,
            "section_id"=> Section::where('name',$row['section'])->first()->id,
            'status_id' => 1, // Default status_id (change as needed)
            'user_id'   => $user_id,
        ]);
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }

}
