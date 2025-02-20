<?php

namespace App\Imports;

use App\Models\DeptGroup;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class DeptGroupImport implements ToModel, WithHeadingRow, OnEachRow
{
    // public function model(array $row)
    // {
    //     // return new DeptGroup([
    //     //     'name'      => $row['name'],   // Column 'name' from Excel
    //     //     'slug'      => $row['slug'],   // Column 'slug' from Excel
    //     //     'status_id' => $row['status_id'], // Column 'status_id' from Excel
    //     //     'user_id'   => $row['user_id'], // Column 'user_id' from Excel
    //     // ]);
    //     $user = Auth::user();
    //     $user_id = $user["id"];
    //     // dd($row);

    //     return new DeptGroup([
    //         'name'      => $row['name'],   // Column 'name' from Excel
    //         'slug'      =>  Str::slug($row['name']),   // Column 'slug' from Excel
    //         'status_id' => 1, // Column 'status_id' from Excel
    //         'user_id'   => $user_id, // Column 'user_id' from Excel
    //     ]);
    // }


    protected $rowNumber = 1;  // Initialize row number

    public function model(array $row)
    {
        // Validate data
        $validator = Validator::make($row, [
            'name'      => 'required|string|max:255|unique:dept_groups,name',
            // 'slug'      => 'nullable|string|max:255',
            // 'status_id' => 'required|integer|exists:statuses,id',  // assuming 'statuses' table and 'id' field exist
            // 'user_id'   => 'nullable|integer|exists:users,id',      // assuming 'users' table and 'id' field exist
        ]);

        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {

            // throw new \Exception("Validation failed on row {$this->rowNumber}: " . json_encode($validator->errors()->toArray()));
            // dd($this->rowNumber);
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );
        }

        // Proceed with saving the data if validation passes
        $user = Auth::user();
        $user_id = $user["id"];

        $this->rowNumber += 1;
        return new DeptGroup([
            'name'      => $row['name'],
            'slug'      => Str::slug($row['name']),
            'status_id' => 1, // Default status_id (change as needed)
            'user_id'   => $user_id,
        ]);
    }

    // Implement the required onRow method from the OnEachRow interface
    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }


}
