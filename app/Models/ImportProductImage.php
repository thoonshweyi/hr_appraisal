<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ImportProductImage extends Model
{
    use HasFactory;
    protected $table = 'import_product_images';
    protected $fillable = [
        'import_product_id',
        'media_link',
        'doc_id',
        'mer_percentage',
        'log_percentage',
        'doc_type',
        'row',
        'separate_qty',
        'discount_amount',
        'after_discount_amount'
    ];
}
