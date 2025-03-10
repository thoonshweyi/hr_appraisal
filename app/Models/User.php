<?php

namespace App\Models;

use App\Models\AssFormCat;
use App\Models\PeerToPeer;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'branch_id',
        'phone_no',
        'department_id',
        'employee_id',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    // public function branches(){
    //     return $this->belongsToMany('App\Models\BranchUser');
    // }

    public function branches(){
        return $this->belongsToMany(Branch::class,'App\Models\BranchUser',"user_id","branch_id");
    }

    public function user_branches(){
        return $this->hasMany(BranchUser::class,'user_id');
    }
    // public function notifications()
    // {
    //     return $this->belongsToMany('App\Models\Notification');
    // }

    public function employee(){
        return $this->hasOne(Employee::class,"employee_code","employee_id");
    }


    public function getAssFormCat(){
        $employee = $this->employee;
        $attach_form_type_id = $employee->attach_form_type_id;
        $position_level_id = $employee->position_level_id;

        $assformcat = AssFormCat::where('attach_form_type_id',$attach_form_type_id)
        ->whereHas('positionlevels',function($query) use($position_level_id){
            $query->where('position_levels.id',$position_level_id);
        })
        ->first();


        // if (!empty($filter_branch_id)) {
        //     $results = $results->whereHas('branches', function($query) use ($filter_branch_id) {
        //         $query->where('branch_users.branch_id', $filter_branch_id);
        //     });
        // }

        return $assformcat;
    }

    public function getAppraisalFormCount($appraisal_cycle_id){
        $appraisalforms = AppraisalForm::where('appraisal_cycle_id', $appraisal_cycle_id)
        ->where('assessor_user_id', $this->id)
        ->get();

        $appraisalformcount = count($appraisalforms);

        return $appraisalformcount;
    }


    public function getAllFormCount($appraisal_cycle_id){
        $assformcat_ids = PeerToPeer::where('assessor_user_id',$this->id)
        ->where('appraisal_cycle_id', $appraisal_cycle_id)
        ->distinct()
        ->pluck('ass_form_cat_id');

        // // dd($assformcat_ids);
        // $assformcats = AssFormCat::whereIn('id',$assformcat_ids)->whereNotIn('id',$filled_assformcat_ids)->get();

        return count($assformcat_ids);
    }

    public function getSentPercentage($appraisal_cycle_id){
        $sentpercentage = ($this->getAppraisalFormCount($appraisal_cycle_id) / $this->getAllFormCount($appraisal_cycle_id)) * 100;

        return $sentpercentage;
    }
}
