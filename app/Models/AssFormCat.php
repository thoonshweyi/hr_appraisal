<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AssFormCat extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "ass_form_cats";
    protected $primaryKey = "id";
    protected $fillable = [
        "name",
        "status_id",
        "user_id",
        "attach_form_type_id",
        "lang",
        "location_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }



    public function positionlevels(){
        return $this->morphToMany(PositionLevel::class,"rankable");
    }


    public function criterias(){
        return $this->hasMany(Criteria::class);
    }


}
