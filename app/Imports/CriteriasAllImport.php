<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\Criteria;
use App\Models\Rankable;
use App\Models\AssFormCat;
use Illuminate\Support\Str;
use App\Models\PositionLevel;
use App\Models\AttachFormType;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;
use Maatwebsite\Excel\Concerns\ToCollection;

class CriteriasAllImport implements ToCollection,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number
 

    function __construct() {
    }

    public function collection(Collection $rows){
        // dd($rows);
        $curcat = null;
        $list_no = 0;

        foreach ($rows as $row) {
            // Start Format Excel Row
            $data = $row->toArray();

            $data['assessment_form_category'] =  $row['criteria_set'] ?? '';
            $row->put("assessment_form_category", $row['criteria_set'] ?? '');
            // dd($data,$row);

            // End Format Excel Row

            // Start Validate Data
            $validator = Validator::make($data, [
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
            // End Validate Data

            $user = Auth::user();
            $user_id = $user["id"];

            $this->rowNumber += 1;


            if(Str::slug($row['assessment_form_category']) !=  $curcat){
                // Start Move to trash old criterias
                $assFormCatSlugs = $rows
                    ->pluck('assessment_form_category')
                    ->filter()
                    ->map(fn ($name) => Str::slug(trim($name)))
                    ->unique()
                    ->values()
                    ->toArray();
                // dd($assFormCatSlugs);

                $assFormCatIds = AssFormCat::whereIn('slug', $assFormCatSlugs)
                    ->pluck('id');


                $criteriaQuery = Criteria::whereIn('ass_form_cat_id', $assFormCatIds);
                $criteriaQuery->update([
                    'status_id'  => 2,
                    'delete_by' => $user_id,
                ]);

                $criteriaQuery->delete();
                // End Move to trash old criterias 

                $list_no = 0;
            }
            $list_no++;



            $attach_form_type_id = AttachFormType::where('name',$row['attach_form_type'])->first()->id;
            $name = $row["assessment_form_category"];

            $rowlocation = $row['location'];
            $location_id = $rowlocation == "HO" ? 7 : ($rowlocation == "HO-Branch" ? 70 : 0);
            // dd($location_id);

            $assformcat = AssFormCat::firstOrCreate(
                ['slug' => Str::slug($name)],
                [
                    'name' => $name,
                    'slug' => Str::slug($name),
                    "attach_form_type_id" => $attach_form_type_id,
                    "lang" => $row['lang'],
                    "location_id" => $location_id,
                    'status_id' => 1,
                    'user_id' => $user_id,
                ]
            );

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

            $criteria =  Criteria::create([
                'name'      =>   $row['name'],
                "ass_form_cat_id" => $assformcat->id,
                'status_id' => 1,

                "excellent" => $row['excellent'],
                "good" => $row['good'],
                "meet_standard" => $row['meet_standard'],
                "below_standard" => $row['below_standard'],
                "weak" => $row['weak'],
                'user_id'   => $user_id,
                "list_no" => $list_no
            ]);

            $curcat = $assformcat->slug;
        }
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }

}

// Criteria Set Exceptions
// 1. Delivery/ Service Team တို့က HO ရော Branch ရော တူတယ်။ (HO - Branch) ရွေးပါမည်။
// 2. Warehouse က  Branch ဖြစ်ပေမယ့် HO Criteria ကိုယူတယ်။
