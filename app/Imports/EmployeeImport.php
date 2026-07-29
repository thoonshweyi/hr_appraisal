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
use App\Models\SubSection;
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
use Maatwebsite\Excel\Events\AfterImport;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class EmployeeImport implements ToModel,WithHeadingRow, OnEachRow, WithEvents{
    protected $rowNumber = 1;  // Initialize row number
    protected $importedEmployeeCodes = [];

    public function model(array $row)
    {
        Log::info($this->rowNumber);

        if (!empty($row['beginning_date'])) {
            $row['beginning_date'] = is_numeric($row['beginning_date'])
            ? Carbon::instance(Date::excelToDateTimeObject($row['beginning_date']))
            : Carbon::parse($row['beginning_date']); // Handles cases where date is already formatted correctly
        }
        // dd(AttachFormType::where('name',$row['attach_form_type'])->first()->id);

        // dd($row);
        // Validate data
        // $row['department'] = Str::lower($row['department']);

        $validator = Validator::make($row, [
            'employee_name'      => 'required|string|max:255',
            'division' => 'required',
            'department' => ['required'],
            'sub_department' => 'required',
            'section' => 'required',
            'sub_section' => 'required',
            'position' => 'required',

            'beginning_date'=> "date",
            "employee_code"=> "required",
            // "branch_code"=> "required|exists:branches,branch_code",
            "branch"=> "required|exists:branches,branch_name",
            "age"=> "required",
            "gender"=> "required|exists:genders,name",
            'position_level'=> "required",
            // 'attach_form_type' => 'required',
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
        $this->importedEmployeeCodes[] = $row['employee_code'];

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

        $updateData =   [
            'employee_name'      => $row['employee_name'],
            'nickname'           => $row['nickname'],
            'status_id'          => 1, // Default status_id (change as needed)
            'user_id'            => $user_id,
            "branch_id"          => Branch::where('branch_name', $row['branch'])->first()?->branch_id,
            "age"                => $row['age'],
            "gender_id"          => Gender::where('name', $row['gender'])->first()?->id,
            // "phone"                => $row['phone'] ? $row['phone'] : null,
        ];

        // Start Agile HR Related Data Handling
        $updateData['division_id'] = $this->firstOrCreateMaster(Division::class, $row['division'], $user_id);
        $updateData['department_id'] = $this->firstOrCreateMaster(AgileDepartment::class, $row['department'], $user_id);
        $updateData['sub_department_id'] = $this->firstOrCreateMaster(SubDepartment::class, $row['sub_department'], $user_id);
        $updateData['section_id'] = $this->firstOrCreateMaster(Section::class, $row['section'], $user_id);
        $updateData['position_id'] = $this->firstOrCreateMaster(Position::class, $row['position'], $user_id);
        $updateData['position_level_id'] = $this->firstOrCreateMaster(PositionLevel::class, $row['position_level'], $user_id);


        if (!empty($row['beginning_date'])) {
            $updateData['beginning_date'] = $row['beginning_date'];
        }
        if (!empty($row['nrc'])) {
            $updateData['nrc'] = $row['nrc'];
        }
        if (!empty($row['father_name'])) {
            $updateData['father_name'] = $row['father_name'];
        }
        // End Agile HR Related Data Handling

        if (!empty($row['attach_form_type'])) {
            $attachFormTypeId = AttachFormType::where('name', $row['attach_form_type'])->value('id');
            if ($attachFormTypeId) {
                $updateData['attach_form_type_id'] = $attachFormTypeId;
            }
        }

        if (!empty($row['sub_section'])) {
            $updateData['sub_section_id'] = $this->firstOrCreateMaster(
                SubSection::class,
                $row['sub_section'],
                $user_id
            );
        }

        return Employee::updateOrCreate(
            ['employee_code' => $row['employee_code']], // Check for existing record
            $updateData
        );
    }

    private function firstOrCreateMaster(string $model, string $name, int $userId): int
    {
        return $model::firstOrCreate(
            ['slug' => Str::slug($name)],
            [
                'name' => $name,
                'slug' => Str::slug($name),
                'status_id' => 1,
                'user_id' => $userId,
            ]
        )->id;
    }

    public function onRow($row)
    {
        // Increment the row number with each row
        $this->rowNumber += 1;
    }


    public function registerEvents(): array
    {
        return [
            AfterImport::class => function(AfterImport $event) {
                $existingCodes = Employee::pluck('employee_code')->toArray();
                $codesToDelete = array_diff($existingCodes, $this->importedEmployeeCodes);
                Employee::whereIn('employee_code', $codesToDelete)
                ->update(['status_id' => 2]);

                // Employee::whereNotIn('employee_code',$importedEmployeeCodes)->delete();

                Log::info("Soft Deleted Employees: ",$codesToDelete);
            },
        ];
    }

}
