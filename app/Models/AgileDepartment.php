<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgileDepartment extends Model
{
    use HasFactory;
    protected $table = "agile_departments";
    protected $fillable = [
        "name",
        "slug",
        "division_id",
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
}
