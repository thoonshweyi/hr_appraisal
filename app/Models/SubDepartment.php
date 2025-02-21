<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubDepartment extends Model
{
    use HasFactory;
    protected $table = "sub_departments";
    protected $fillable = [
        "name",
        "slug",
        "division_id",
        "department_id",
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
}
