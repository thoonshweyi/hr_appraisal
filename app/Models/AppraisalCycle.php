<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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


    public function isActioned()
    {
        $now = Carbon::today(); // Get today's date without time

        return $now->isBetween($this->action_start_date, $this->action_end_date);
    }

    public function isBeforeActionStart()
    {
        $now = Carbon::today(); // Get today's date without time

        return $now->lessThan($this->action_start_date);
    }
}
