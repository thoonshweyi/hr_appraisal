<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssFormCat extends Model
{
    use HasFactory;
    protected $table = "ass_form_cats";
    protected $primaryKey = "id";
    protected $fillable = [
        "name",
        "status_id",
        "user_id"
    ];

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function status(){
        return $this->belongsTo(Status::class);
    }
}
