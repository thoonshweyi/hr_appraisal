<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportProduct extends Model
{
    use HasFactory, SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $table = 'import_products';
    protected $fillable=[
        'document_id',
        'product_code_no',
        'product_name',
        'product_unit',
        'product_price',
        'stock_quantity',
        'damage_percentage',
        'percentage_amount',
        'total_price',
        'product_image',
        'remark',
        'new_stock_qty',
        'system_quantity',


    ];
    public function document()
    {
        return $this->hasOne(LogisticsDocument::class, 'id', 'document_id');
    }
    public function import_product_image()
    {
        return $this->hasOne(ImportProductImage::class, 'import_product_id', 'id');
    }
    public function importproductimage()
    {
        return $this->hasMany(ImportProductImage::class, 'import_product_id', 'id');
    }

    // public function product_percentage()
    // {
    //     return $this->hasOne(ProductImag::class, 'import_product_id', 'id');
    // }
}
