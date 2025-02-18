<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;
    protected $connection = 'pgsql3';
    protected $table = 'exchange_rates';
    protected $fillable = [
        'sell',
    ];

}
