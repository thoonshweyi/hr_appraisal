<?php

namespace App\Imports;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Section;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\BranchUser;
use Illuminate\Support\Str;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Models\AttachFormType;
use App\Models\AgileDepartment;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\OnEachRow;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class EmployeeImport implements ToModel,WithHeadingRow, OnEachRow{
    protected $rowNumber = 1;  // Initialize row number

    public function model(array $row)
    {
        Log::info($this->rowNumber);
        $row['beginning_date'] = is_numeric($row['beginning_date'])
        ? Carbon::instance(Date::excelToDateTimeObject($row['beginning_date']))
        : Carbon::parse($row['beginning_date']); // Handles cases where date is already formatted correctly

        // dd(AttachFormType::where('name',$row['attach_form_type'])->first()->id);

        // dd($row);
        // Validate data
        // $row['department'] = Str::lower($row['department']);

        // $row['beginning_date'];
        $validator = Validator::make($row, [
            'employee_name'      => 'required|string|max:255',
            'division' => 'required|exists:divisions,name',
            'department' => ['required',"exists:agile_departments,name"],
            'sub_department' => 'required|exists:sub_departments,name',
            'section' => 'required|exists:sections,name',
            'position' => 'required|exists:positions,name',

            'beginning_date'=> "required|date",
            "employee_code"=> "required",
            // "branch_code"=> "required|exists:branches,branch_code",
            "branch"=> "required|exists:branches,branch_name",
            "age"=> "required",
            "gender"=> "required|exists:genders,name",
            'position_level'=> "required|exists:position_levels,name",
            // "nrc"=> "required",
            // "father_name"=> "required",
            'attach_form_type' => 'required',
            // 'phone' => 'required'
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

        $empuser = User::firstOrCreate(
            ['employee_id' => $row['employee_code']], // Ensure user is linked by employee_code
            [
                "name"      => $row['employee_name'],
                "password"  => Hash::make($row['employee_code'])
            ]
        );
        // $userBranch['user_id'] = $empuser->id;
        // $userBranch['branch_id'] = Branch::where('branch_name',$row['branch'])->first()->branch_id;
        // BranchUser::firstOrCreate(["user_id"=>$empuser->id],["branch_id"=>Branch::where('branch_code',$row['branch_code'])->first()->branch_id]);
        BranchUser::firstOrCreate(["user_id"=>$empuser->id],["branch_id"=>Branch::where('branch_name',$row['branch'])->first()->branch_id]);

        return Employee::updateOrCreate(
            ['employee_code' => $row['employee_code']], // Check for existing record
            [
                'employee_name'      => $row['employee_name'],
                'nickname'           => $row['nickname'],
                "division_id"        => Division::where('name', $row['division'])->first()?->id,
                "department_id"      => AgileDepartment::where('name', $row['department'])->first()?->id,
                "sub_department_id"  => SubDepartment::where('name', $row['sub_department'])->first()?->id,
                "section_id"         => Section::where('name', 'like' , "%".$row['section'].'%')->first()?->id,
                "position_id"        => Position::where('name', $row['position'])->first()?->id,
                'status_id'          => 1, // Default status_id (change as needed)
                'user_id'            => $user_id,
                'beginning_date'     => $row['beginning_date'],
                "branch_id"          => Branch::where('branch_name', $row['branch'])->first()?->branch_id,
                "age"                => $row['age'],
                "gender_id"          => Gender::where('name', $row['gender'])->first()?->id,
                "position_level_id"  => PositionLevel::where('name', $row['position_level'])->first()?->id,
                "nrc"                => $row['nrc'],
                "father_name"        => $row['father_name'],
                "attach_form_type_id"=> AttachFormType::where('name', $row['attach_form_type'])->first()?->id,
                "phone"                => $row['phone'] ? $row['phone'] : null,
            ]
        );
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }

}
