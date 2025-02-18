<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductImagePercentage extends Model
{
    use HasFactory;
    protected $fillable = ['doc_id','product_id','product_img','mer_percentage','log_percentage','doc_type','row'];
}
