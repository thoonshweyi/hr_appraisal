<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Section;
use App\Models\Criteria;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\Rankable;
use App\Models\AssFormCat;
use App\Models\BranchUser;
use Illuminate\Support\Str;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Models\AttachFormType;
use App\Models\AgileDepartment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class CriteriasAllImport implements ToModel,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number
    protected $ass_form_cat_id;
    protected $max_totals = [];

    protected  $total_excellent = 0;
    protected $total_good = 0;
    protected $total_meet_standard = 0;
    protected $total_below_standard = 0;
    protected $total_weak = 0;
    function __construct($max_totals) {
        // $this->ass_form_cat_id = $ass_form_cat_id;
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
            "assessment_form_category" => "required",
            "attach_form_type" => "required|exists:attach_form_types,name",
            "location" => "required",
            "lang" => "required",
            "ranks" => "required",
        ]);

        // If validation fails, throw an exception with the row number
        if ($validator->fails()) {
            throw new ExcelImportValidationException(
                $validator->errors()->toArray(),
                $this->rowNumber
            );

        }

        // Start Max Validation
        // $this->total_excellent += (int) $row['excellent'];
        // $this->total_good += (int) $row['good'];
        // $this->total_meet_standard += (int) $row['meet_standard'];
        // $this->total_below_standard += (int) $row['below_standard'];
        // $this->total_weak += (int) $row['weak'];

        // $max_errors = [];
        // if($this->total_excellent > $this->max_totals['max_total_excellent']){
        //     $max_errors[][] = "Total Excellent cannot exceed ".$this->max_totals['max_total_excellent'];
        // }

        // if($this->total_good > $this->max_totals['max_total_good']){
        //     $max_errors[][] = "Total Good cannot exceed ".$this->max_totals['max_total_good'];
        // }

        // if($this->total_meet_standard > $this->max_totals['max_total_meet_standard']){
        //     $max_errors[][] = "Total Meet Standard cannot exceed ".$this->max_totals['max_total_meet_standard'];
        // }

        // if($this->total_below_standard > $this->max_totals['max_total_below_standard']){
        //     $max_errors[][] = "Total Below Standard cannot exceed ".$this->max_totals['max_total_below_standard'];
        // }

        // if($this->total_weak > $this->max_totals['max_total_weak']){
        //     $max_errors[][] = "Total Weak cannot exceed ".$this->max_totals['max_total_weak'];
        // }
        // if(!empty($max_errors)){
        //     throw new ExcelImportValidationException(
        //         $max_errors,
        //         $this->rowNumber
        //     );
        // }

        // End Max Validation


        $user = Auth::user();
        $user_id = $user["id"];

        $this->rowNumber += 1;

        // dd($row['attach_form_type']);
        // dd(AttachFormType::where('name',$row['attach_form_type'])->first());
        $attach_form_type_id = AttachFormType::where('name',$row['attach_form_type'])->first()->id;
        $assformcat = AssFormCat::firstOrCreate([
            "name" => $row["assessment_form_category"],
            // "status_id" => ,
            // "user_id" =>,
        ],[
            "attach_form_type_id" => $attach_form_type_id,
            "lang" => $row['lang'],
            "location_id" => $row['location'] == "HO" ? 7 : 0,
            "status_id" => 1,
            "user_id" => $user_id,
        ]);

        $ranksstr = $row['ranks'];
        if (str_contains($ranksstr, 'Above') || str_contains($ranksstr, 'above')) {
            // dd('hay');
            $aboverank = substr($ranksstr, 0, 1);
            $ranks_arr = PositionLevel::where("id",">=",$aboverank)->pluck("id")->toArray();
        }elseif(str_contains($ranksstr, 'All') || str_contains($ranksstr, 'all')){
            $ranks_arr = PositionLevel::all()->pluck("id")->toArray();
        }else{
            $ranks_arr = explode(',', $ranksstr);
        }
        foreach($ranks_arr as $idx=>$rank){
            $rankable = Rankable::firstOrCreate([
                "position_level_id"=> $rank,
                "rankable_id" => $assformcat->id,
                "rankable_type" => get_class($assformcat)
            ]);
        }

        return Criteria::firstOrCreate([
            'name'      =>   $row['name'],
            // "ass_form_cat_id" => AssFormCat::where('name',$row['division'])->first()->id,
            "ass_form_cat_id" => $assformcat->id,
            'status_id' => 1, // Default status_id (change as needed)
            // 'user_id'   => $user_id,

            "excellent" => $row['excellent'],
            "good" => $row['good'],
            "meet_standard" => $row['meet_standard'],
            "below_standard" => $row['below_standard'],
            "weak" => $row['weak'],
        ],[
            'user_id'   => $user_id,
        ]);
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }

}
