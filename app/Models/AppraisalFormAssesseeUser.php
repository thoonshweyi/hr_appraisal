<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalFormAssesseeUser extends Model
{
    use HasFactory;

    protected $table = "appraisal_form_assessee_users";
    protected $primaryKey = "id";
    protected $fillable = [
        "appraisal_form_id",
        "assessee_user_id",
    ];

    public function appraisalform(){
        return $this->belongsTo(AppraisalForm::class,"appraisal_form_id");
    }

    public function assesseeuser(){
        return $this->belongsTo(User::class,"assessee_user_id");
    }

}
