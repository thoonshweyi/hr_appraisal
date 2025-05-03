<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PeerToPeer extends Model
{
    use HasFactory;
    protected $table = "peer_to_peers";
    protected $primaryKey = "id";
    protected $fillable = [
        "assessor_user_id",
        "assessee_user_id",
        "ass_form_cat_id",
        "appraisal_cycle_id"
    ];

    public function assessor(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }

    public function assessoruser(){
        return $this->belongsTo(User::class,"assessor_user_id");
    }

    public function assesseeuser(){
        return $this->belongsTo(User::class,"assessee_user_id");
    }

    public function assformcat(){
        return $this->belongsTo(AssFormCat::class,"ass_form_cat_id");
    }

    public static function getRecentAssessees($assessor_user_id, $appraisal_cycle_id)
    {
        return self::where('assessor_user_id', $assessor_user_id)
            ->where('appraisal_cycle_id', $appraisal_cycle_id)
            ->with([
                "assessoruser.employee",
                "assesseeuser.employee.branch",
                "assesseeuser.employee.department",
                "assesseeuser.employee.position",
                "assesseeuser.employee.positionlevel",
                "assformcat"
            ])
            ->get();
    }


    public static function getRecentAssessors($assessor_user_id, $appraisal_cycle_id)
    {
        return self::where('assessee_user_id', $assessor_user_id)
            ->where('appraisal_cycle_id', $appraisal_cycle_id)
            ->with(['assessoruser.employee.position'])
            ->get();
    }

}
