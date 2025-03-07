<?php

namespace App\Models;

use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
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
        ->first();


        return $assformcat;



    }
}
