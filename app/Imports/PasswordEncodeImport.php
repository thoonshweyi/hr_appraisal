<?php

namespace App\Imports;

use App\Exceptions\ExcelImportValidationException;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithHeadingRow;


class PasswordEncodeImport implements ToModel,WithHeadingRow, WithChunkReading
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

        $user = User::where("employee_id",trim($row["employee_code"]))
                ->whereNull('email')
                ->doesntHave("roles")
                ->first();
        
        
        if ($user) {
            $user->update([
                'password' => Hash::make(trim($row['password'])),
            ]);

            Log::info('Password updated successfully', [
                'employee_code' => trim($row['employee_code']),
                'user_id' => $user->id,
            ]);
        }

        return null;
    }

    public function chunkSize(): int
    {
        return 240;
    }

}
