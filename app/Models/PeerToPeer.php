<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
