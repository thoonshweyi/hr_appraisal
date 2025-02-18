<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogisticsDocument extends Model
{
    use HasFactory;
    protected $fillable = [
        'document_type',
        'branch_id',
        'document_no',
        'document_date',
        'operation_id',
        'operation_updated_datetime',
        'operation_attach_file',
        'operation_remark',
        'category_head_id',
        'category_head_updated_datetime',
        'category_head_attach_file',
        'category_head_remark',
        'branch_manager_id',
        'branch_manager_updated_datetime',
        'branch_manager_attach_file',
        'branch_manager_remark',
        'logistics_id',
        'logistics_updated_datetime',
        'logistics_attach_file',
        'logistics_remark',
        'accounting_id',
        'accounting_updated_datetime',
        'accounting_attach_file',
        'finished_id',
        'finished_updated_datetime',
        'accounting_remark',
        'branch_manager_reject_remark_id',
        'branch_manager_reject_updated_datetime',
        'sourcing_manger_id',
        'sourcing_manger_updated_datetime',
        'issue_doc_no',
        'car_no',
        'document_status',
        'category_id',
        'log_manager_reject_remark_id',
        'log_manager_reject_remark_updated_datetime',
        'ch_reject_remark_id',
        'ch_reject_remark_updated_datetime',
        'percentage_total_amount',
        'operation_attach_file_2',
        'excel_attach_file',
        'issue_doc_datetime',
        'account_issued_id',
        'accounting_attach_file2',
        'logistics_reject_id',
        'logistics_reject_updated_datetime',
        'reject_remark',
    ];

    public function Category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'id');
    }
    public function Products()
    {
        return $this->hasMany(ImportProduct::class, 'document_id', 'id');
    }
    public function DocumentStatus()
    {
        return $this->belongsTo(DocumentStatus::class, 'document_status', 'document_status');
    }
    public function branches()
    {
        return $this->belongsTo('App\Models\Branch', 'branch_id', 'branch_id');
    }

    public function copy($date, $doc_no, $doc_type, $doc_status, $cat_head_id, $cat_head_date, $cat_head_remark)
    {
        $type = (int)$doc_type;
        $document = $this->replicate();
        $document->document_date = $date;
        $document->document_no = $doc_no;
        $document->document_type = $type;
        $document->document_status = $doc_status;
        $document->category_head_id = $cat_head_id;
        $document->category_head_updated_datetime = $cat_head_date;
        $document->category_head_remark = $cat_head_remark;

        // Save the copied document
        $document->save();
        return $document;
    }

    public function importproduct(){
        return $this->hasMany('App\Models\ImportProduct','document_id','id');
    }
}
