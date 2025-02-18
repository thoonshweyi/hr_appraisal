<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SourcingProduct extends Model
{
    use HasFactory, SoftDeletes;
    protected $table = 'sourcing_products';
    protected $dates = ['deleted_at'];
    protected $fillable=[
        'document_id',
        'product_code_no',
        'product_name',
        'product_unit',
        'product_price',
        'stock_quantity',
        'damage_percentage',
        'product_image',
        'remark',
        'percentage_amount',
        'baht_value',
        'kyat_value',
        'product_brand_name',
        'currency_type',
        'damage_remark_types',
        'damage_remark_reasons',
        'finished_status',
        'total_price',
        'new_stock_qty',
        'system_quantity',
    ];
    public function document()
    {
        return $this->hasOne(SourcingDocument::class, 'id', 'document_id');
    }
    public function sourcing_product_image()
    {
        return $this->hasOne(SourcingProductImage::class, 'sourcing_product_id', 'id');
    }

    public function sourcingproductimage(){
        return $this->hasMany('App\Models\SourcingProductImage','sourcing_product_id','id');
    }
}
