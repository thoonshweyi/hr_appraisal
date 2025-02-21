<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Section extends Model
{
    use HasFactory;
    protected $table = "sections";
    protected $fillable = [
        "name",
        "slug",
        "division_id",
        "department_id",
        "sub_department_id",
        "status_id",
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
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
}
