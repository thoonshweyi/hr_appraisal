<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalForm extends Model
{
    use HasFactory;

    protected $table = "appraisal_forms";
    protected $primaryKey = "id";
    protected $fillable = [
        "assessor_user_id",
        "ass_form_cat_id",
        "appraisal_cycle_id",
        "user_id"
    ];

    public function assessor(){
        return $this->belongsTo(User::class);
    }

    public function assessoruser(){
        return $this->belongsTo(User::class,"assessor_user_id");
    }


    public function assformcat(){
        return $this->belongsTo(AssFormCat::class,"ass_form_cat_id");
    }

    public function appraisalcycle(){
        return $this->belongsTo(AppraisalCycle::class,"appraisal_cycle_id");
    }

    public function assesseeusers(){
        return $this->belongsToMany(User::class,"appraisal_form_assessee_users","appraisal_form_id","assessee_user_id");
    }
}
