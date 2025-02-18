<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;

class FAQ extends Model
{
    use HasFactory,SoftDeletes;
    protected $fillable = [
        'name_eng',
        'name_mm',
        'description_eng',
        'description_mm',
        'question_eng',
        'question_mm',
        'answer_eng',
        'answer_mm',
    ];
}
