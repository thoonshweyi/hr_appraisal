<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Department extends Model
{
    use HasFactory;
    protected $fillable = [
        "code",
        "name",
        "slug",
        "dept_group_id",
        "status_id",
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function deptgroup(){
        return $this->belongsTo(DeptGroup::class,'dept_group_id','id');
    }
}
