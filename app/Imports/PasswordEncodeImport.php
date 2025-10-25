<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use App\Exceptions\ExcelImportValidationException;

class PasswordEncodeImport implements ToModel,WithHeadingRow
{
        protected $rowNumber = 1;  // Initialize row number

        public function model(array $row){
            $validator = Validator::make($row, [
                'employee_code' => 'required|string',
                "password" => "required|string|min:4",
            ]);
            // If validation fails, throw an exception with the row number
            if ($validator->fails()) {
                throw new ExcelImportValidationException(
                    $validator->errors()->toArray(),
                    $this->rowNumber
                );
            }


            $this->rowNumber += 1;

            $user = User::where("employee_id",$row["employee_code"])
                    ->whereNull('email')
                    ->doesntHave("roles")
                    ->first();
            
            
            if ($user) {
                $user->update([
                    'password' => Hash::make($row['password']),
                ]);
            }

            return null;
        }

}
