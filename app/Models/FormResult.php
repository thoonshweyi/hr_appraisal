<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FormResult extends Model
{
    use HasFactory;
    protected $table = "form_results";
    protected $primaryKey = "id";
    protected $fillable = [
        "appraisal_form_id",
        "assessee_user_id",
        "criteria_id",
        "result"
    ];

    public function appraisalform(){
        return $this->belongsTo(AppraisalForm::class,"appraisal_form_id");
    }

    public function assesseeuser(){
        return $this->belongsTo(User::class,"assessee_user_id");
    }

    public function criteria(){
        return $this->belongsTo(Criteria::class,"criteria_id");
    }
}
