<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use SoftDeletes;
      /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_id',
        'product_code_no',
        'product_name',
        'product_unit',
        'stock_quantity',
        'return_quantity',
        'operation_actual_quantity',
        'merchandising_actual_quantity',
        'operation_rg_out_actual_quantity',
        'operation_rg_in_actual_quantity',
        'avg_cost',
        'avg_cost_total',
        'product_attach_file',
        'operation_remark',
        'rg_out_doc_no',
        'discount_amount',
        'invoice',
        'vat_no_vat',
        'tax_check','delete_by'
    ];

    public function document()
    {
        return $this->hasOne(Document::class, 'id', 'document_id');
    }
}
