<?php

namespace App\Models;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Criteria extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = "criterias";
    protected $primaryKey = "id";

    protected $orgfillable = [
        "name",
        "status_id",
        "user_id",
        "ass_form_cat_id"
    ];

    protected $fillable = [];
    protected $dynfillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes); // Call parent constructor
        $this->getfillable(); // Initialize fillable fields
    }


    public function getfillable()
    {
        $ratingscales = RatingScale::orderBy('id', 'asc')->get();
        foreach ($ratingscales as $ratingscale) {
            $this->dynfillable[] = Str::snake($ratingscale['name']);
        }

        $this->fillable = array_merge($this->orgfillable, $this->dynfillable);
        return  $this->fillable;
    }



    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
    public function assformcat(){
        return $this->belongsTo(AssFormCat::class,'ass_form_cat_id','id');
    }
    public function getRatingScaleAttribute()
    {
        // return [
        //     $this->excellent,
        //     $this->good,
        //     $this->meet_standard,
        //     $this->below_standard,
        //     $this->weak,
        // ];

        return $this->only(['excellent', 'good', 'meet_standard', 'below_standard', 'weak']);
    }
}
