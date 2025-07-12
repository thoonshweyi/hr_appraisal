<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Employee extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "employees";
    protected $fillable = [
       'beginning_date',
       "employee_code",
       "branch_id",
       "employee_name",
       'age',
       'gender_id',
       "nickname",
       "division_id",
       "department_id",
       "sub_department_id",
       "section_id",
       "position_id",
       "status_id",
       "user_id",
       'longevity_year',
       'longevity_month',
       'longevity_day',
       'longevity_total',
       'education_level',
       'institution',
       'faculty',
       'major_graduated',
       'position_level_id',
       "nrc",
       "father_name",
       "attach_form_type_id",
       "job_status",
       "phone",
       "address",
       "dob",
       "image"
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function gender(){
        return $this->belongsTo(Gender::class);
    }

    public function division(){
        return $this->belongsTo(Division::class,'division_id','id');
    }

    public function department(){
        return $this->belongsTo(AgileDepartment::class,'department_id','id');
    }

    public function subdepartment(){
        return $this->belongsTo(SubDepartment::class,'sub_department_id','id');
    }

    public function section(){
        return $this->belongsTo(Section::class,'section_id','id');
    }

    public function position(){
        return $this->belongsTo(Position::class,'position_id','id');
    }

    public function positionlevel(){
        return $this->belongsTo(PositionLevel::class,'position_level_id','id');
    }

    public function branch(){
        return $this->belongsTo(Branch::class,'branch_id','branch_id');
    }

    public function attachformtype(){
        return $this->belongsTo(AttachFormType::class,'attach_form_type_id','id');
    }

    public function attachformtypes(){
        return $this->hasMany(EmployeeAttachFormType::class,"employee_code","employee_code");
    }

    public function emppuser(){
        return $this->belongsTo(User::class,'employee_code',"employee_id");
    }

}
