<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    // protected $connection = 'pgsql2';
    // protected $table = 'master_data.master_product_category';
    protected $fillable = [
        'category_code', 
        'category_name', 
    ];

}
