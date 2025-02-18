<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReferenceDocument extends Model
{
    use HasFactory;
    protected $table='reference_document';
    protected $fillable =[
        'new_reference_no',
        'old_reference_no',
    ];
}
