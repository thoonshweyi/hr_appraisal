<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RejectRemark extends Model
{
    use HasFactory;
      /**
     * The attributes that are mass assignable.
     *	
     * @var array
     */
    protected $fillable = [
        'remark_eng', 
        'remark_mm', 
    ];

}
