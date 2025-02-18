<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CNDBDocument extends Model
{
    use HasFactory;

    protected $fillable = ['document_id','cn_db_no','type','vat_novat'];
}
