<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rankable extends Model
{
    use HasFactory;
    protected $table = "rankables";
    protected $primaryKey = "id";
    protected $fillable = [
        "position_level_id",
        "rankable_id",
        "rankable_type"
    ];

}
