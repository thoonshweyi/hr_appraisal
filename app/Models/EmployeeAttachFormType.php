<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EmployeeAttachFormType extends Model
{
    use HasFactory;
    protected $table = "employee_criteria_sets";
    protected $primaryKey = "id";
    protected $fillable = [
        "employee_code",
        "attach_form_type_id",
    ];

}
