<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AppraisalFormAssesseeUser extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "appraisal_form_assessee_users";
    protected $primaryKey = "id";
    protected $fillable = [
        "appraisal_form_id",
        "assessee_user_id",
        "status_id",
        "user_id",
        "delete_by"
    ];

    public function appraisalform(){
        return $this->belongsTo(AppraisalForm::class,"appraisal_form_id");
    }

    public function assesseeuser(){
        return $this->belongsTo(User::class,"assessee_user_id");
    }

}
