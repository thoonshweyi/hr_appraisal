<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PasswordEncode extends Model
{
    use HasFactory;
    protected $table = "password_encodes";
    protected $primaryKey = "id";
    protected $fillable = [
        "image",
        "appraisal_cycle_id",
        "remark",
        "status_id",
        "user_id"
    ];


    public function appraisalcycle(){
        return $this->belongsTo(AppraisalCycle::class,"appraisal_cycle_id");
    }

}
