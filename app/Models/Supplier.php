<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $connection = 'pgsql2';
    protected $table = 'configure.setap_vendor';
    protected $fillable = [
        'supplier_code', 'supplier_name'
    ];
}

