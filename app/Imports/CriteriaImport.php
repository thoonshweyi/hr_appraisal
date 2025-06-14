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
    protected $max_totals = [];

    protected  $total_excellent = 0;
    protected $total_good = 0;
    protected $total_meet_standard = 0;
    protected $total_below_standard = 0;
    protected $total_weak = 0;
    function __construct($ass_form_cat_id,$max_totals) {
        $this->ass_form_cat_id = $ass_form_cat_id;
        $this->max_totals = $max_totals;

        // need to add existing total
        // $criterias = Criteria::where('ass_form_cat_id',$ass_form_cat_id);
        // $this->total_excellent = $criterias->sum('excellent');
        // $this->total_good = $criterias->sum('good');
        // $this->total_meet_standard = $criterias->sum('meet_standard');
        // $this->total_below_standard = $criterias->sum('below_standard');
        // $this->total_weak = $criterias->sum('weak');
    }


    public function model(array $row)
    {
        // Validate data
        $validator = Validator::make($row, [
            'name'      => 'required|string',
            "excellent" => 'required|numeric',
            "good" => 'required|numeric',
            "meet_standard" => 'required|numeric',
            "below_standard" => 'required|numeric',
            "weak" => 'required|numeric',
        ]);

        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );

        }

        // Start Max Validation
        $this->total_excellent += (int) $row['excellent'];
        $this->total_good += (int) $row['good'];
        $this->total_meet_standard += (int) $row['meet_standard'];
        $this->total_below_standard += (int) $row['below_standard'];
        $this->total_weak += (int) $row['weak'];

        $max_errors = [];
        if($this->total_excellent > $this->max_totals['max_total_excellent']){
            $max_errors[][] = "Total Excellent cannot exceed ".$this->max_totals['max_total_excellent'];
        }

        if($this->total_good > $this->max_totals['max_total_good']){
            $max_errors[][] = "Total Good cannot exceed ".$this->max_totals['max_total_good'];
        }

        if($this->total_meet_standard > $this->max_totals['max_total_meet_standard']){
            $max_errors[][] = "Total Meet Standard cannot exceed ".$this->max_totals['max_total_meet_standard'];
        }

        if($this->total_below_standard > $this->max_totals['max_total_below_standard']){
            $max_errors[][] = "Total Below Standard cannot exceed ".$this->max_totals['max_total_below_standard'];
        }

        if($this->total_weak > $this->max_totals['max_total_weak']){
            $max_errors[][] = "Total Weak cannot exceed ".$this->max_totals['max_total_weak'];
        }
        if(!empty($max_errors)){
            throw new ExcelImportValidationException(
                $max_errors,
                $this->rowNumber
            );
        }

        // End Max Validation


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
