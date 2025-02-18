<?php

namespace App\Models;

use App\Models\Product;
use App\Models\SupplierCancelRemark;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'document_no',
        'document_type',
        'branch_id',
        'supplier_id',
        'document_date',
        'document_status',
        'operation_id',
        'operation_updated_datetime',
        'branch_manager_id',
        'branch_manager_updated_datetime',
        'operation_attach_file',
        'operation_remark',

        'category_head_id',
        'category_head_updated_datetime',


        'delivery_date',
        'merchandising_manager_id',
        'merchandising_manager_updated_datetime',
        'merchandising_remark',
        'merchandising_attach_file',

        'arrive_to_dc_id',
        'arrive_to_dc_updated_datetime',

        'operation_rg_out_id',       
        'operation_rg_out_updated_datetime',
        'operation_rg_out_attach_file',
        'rg_out_remark',

        'accounting_cn_id',
        'accounting_cn_updated_datetime',
        'accounting_cn_attach_file',
        'accounting_cn_credit_day',
        'due_date',
        'accounting_remark',

        'dc_manager_in_id',
        'dc_manager_in_updated_datetime',

        'operation_rg_in_id',
        'operation_rg_in_updated_datetime',
        'operation_rg_in_attach_file',

        'accounting_db_id',
        'accounting_db_updated_datetime',
        'accounting_db_credit_day',
        'db_due_date',
        'accounting_db_attach_file',

        'vat_no_vat',
        'ref_doc_no',

        'exchange_to_return',
        'exchange_to_return_bm',
        'document_remark',
        'category_id',
        'supplier_cancel_datetime',

        'pending_remark',
        'reject_remark',
        'dc_status',
        'supplier_cancel_remark_id',

        'change_dc_status_user_id',
        'change_dc_status_updated_datetime',
    ];
    public function branches()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'branch_id');
    }
    public function rg_out()
    {
        return $this->belongsTo(User::class, 'operation_rg_out_id');
    }
    public function branch_manager()
    {
        return $this->belongsTo(User::class, 'branch_manager_id');
    }
    public function suppliers()
    {
        return $this->belongsTo(Supplier::class, 'supplier_id', 'vendor_id');
    }
    public function DocumentStatus()
    {
        return $this->belongsTo(DocumentStatus::class, 'document_status', 'document_status');
    }
    public function Category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }

    public function Products()
    {
        return $this->hasMany(Product::class, 'document_id', 'id');
    }

    public function RejectRemarks()
    {
        return $this->belongsTo(RejectRemark::class, 'reject_remark', 'id');
    }

    public function SupplierCancelRemarks()
    {
        return $this->belongsTo(SupplierCancelRemark::class, 'supplier_cancel_remark_id', 'id');
    }

    public function cn_db()
    {
        return $this->hasMany(CNDBDocument::class, 'document_id', 'id');
    }

    public function supplier()
    {
        return $this->hasOne(Vendor::class, 'vendor_id', 'supplier_id');
    }
}
