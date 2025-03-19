<?php

namespace App\Imports;

use App\Models\Division;
use App\Models\Criteria;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;



class CriteriaImport implements ToModel,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number
    protected $ass_form_cat_id;

    function __construct($ass_form_cat_id) {
        $this->ass_form_cat_id = $ass_form_cat_id;
    }


    public function model(array $row)
    {
        // Validate data
        $validator = Validator::make($row, [
            'name'      => 'required|string',
            "excellent" => 'required',
            "good" => 'required',
            "meet_standard" => 'required',
            "below_standard" => 'required',
            "weak" => 'required',
        ]);

        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );

        }


        $user = Auth::user();
        $user_id = $user["id"];

        $this->rowNumber += 1;

        return new Criteria([
            // 'name'      =>   Rabbit::zg2uni($row['name']),
            'name'      =>   $row['name'],
            // "ass_form_cat_id" => AssFormCat::where('name',$row['division'])->first()->id,
            "ass_form_cat_id" => $this->ass_form_cat_id,
            'status_id' => 1, // Default status_id (change as needed)
            'user_id'   => $user_id,

            "excellent" => $row['excellent'],
            "good" => $row['good'],
            "meet_standard" => $row['meet_standard'],
            "below_standard" => $row['below_standard'],
            "weak" => $row['weak'],
        ]);
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }

}
