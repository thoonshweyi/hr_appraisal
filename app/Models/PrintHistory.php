<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrintHistory extends Model
{
    use HasFactory;
    protected $table = "print_histories";
    protected $fillable = [
        'assessor_user_id',
        'appraisal_cycle_id',
        'printed_at',
        'user_id',
    ];
}
