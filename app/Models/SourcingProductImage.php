<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SourcingProductImage extends Model
{
    use HasFactory;
    protected $table = 'sourcing_product_images';
    protected $fillable = [
        'sourcing_product_id',
        'media_link',
        //'media_link',
        'doc_id',
        'mer_percentage',
        'sourcing_percentage',
        'log_percentage',
        'doc_type',
        'row',
        'separate_qty',
        'discount_amount',
        'after_discount_amount'

    ];
    public function sourcing_product()
    {
        return $this->hasOne(SourcingProduct::class, 'id', 'sourcing_product_id');
    }
}
