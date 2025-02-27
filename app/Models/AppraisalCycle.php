<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalCycle extends Model
{
    use HasFactory;
    protected $table = "appraisal_cycles";
    protected $fillable = [
        "name",
        "description",
        "start_date",
        "end_date",
        "action_start_date",
        "action_end_date",
        "action_start_time",
        "action_end_time",
        "status_id",
        'user_id'
    ];
    public function user(){
        return $this->belongsTo(User::class);
    }
    public function status(){
        return $this->belongsTo(Status::class);
    }
}
