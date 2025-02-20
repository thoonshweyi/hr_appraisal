<?php

namespace App\Imports;

use App\Models\DeptGroup;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class DepartmentImport implements ToModel,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number

    public function model(array $row)
    {
        // Validate data
        $validator = Validator::make($row, [
            'name'      => 'required|string|max:255|unique:departments,name',
            'code'      => 'required',
            'deptgroup' => 'required|exists:dept_groups,name',
        ]);

        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );
        }

        // Proceed with saving the data if validation passes
        $user = Auth::user();
        $user_id = $user["id"];

        $this->rowNumber += 1;
        return new Department([
            'name'      => $row['name'],
            'code'      => $row['code'],
            'slug'      => Str::slug($row['name']),
            "dept_group_id"=> DeptGroup::where('name',$row['deptgroup'])->first()->id,
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
