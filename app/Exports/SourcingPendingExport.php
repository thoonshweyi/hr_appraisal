<?php

namespace App\Exports;

use App\Models\BranchUser;
use App\Models\SourcingDocument;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class SourcingPendingExport implements FromView
{
    protected $document_type;
    protected $detail_type;


    public function __construct($document_type='', $detail_type)
    {
        $this->document_type = $document_type;
        $this->detail_type = $detail_type;
    }
    public function view(): View
    {
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        // dd('hi');
        // $result = SourcingDocument::where('document_type', '=', $this->document_type)
        // ->whereIn('branch_id', $user_branches)
        // ->whereIn('document_status', ['1', '2','4', '9','17','19','21'])
        // ->get();
        if($this->detail_type == 10 || $this->detail_type == 11 || $this->detail_type == 12)
        {
            $result = SourcingDocument::where('document_type', '=', $this->document_type)
                        ->whereIn('branch_id', $user_branches)
                        ->get();
            // dd($result);
            if($this->document_type == 1)
            {
                $status = 'Small Document';
            }
            elseif($this->document_type == 2)
            {
                $status = 'Big Document';
            }
            elseif($this->document_type == 3)
            {
                $status = 'Need Accessory Document';
            }
        }
        elseif($this->detail_type == 3 || $this->detail_type == 6 || $this->detail_type == 9)
        {
            $result = SourcingDocument::where('document_type', '=', $this->document_type)
                        ->whereIn('branch_id', $user_branches)
                        ->whereIn('document_status', ['3','5','20', '22'])
                        ->get();
            // dd($result);
            if($this->document_type == 1)
            {
                $status = 'Small Reject';
            }
            elseif($this->document_type == 2)
            {
                $status = 'Big Reject';
            }
            elseif($this->document_type == 3)
            {
                $status = 'Need Accessory Reject';
            }
        }
        elseif($this->detail_type == 2 ||$this->detail_type == 5 || $this->detail_type == 8)
        {
            $result = SourcingDocument::where('document_type', '=', $this->document_type)
                    ->whereIn('branch_id', $user_branches)
                    ->whereIn('document_status', ['1', '2','4', '9','17','19','21'])
                    ->has('products')
                    ->get();
            // dd($result);
            if($this->document_type == 1)
            {
                $status = 'Small Pending';
            }
            elseif($this->document_type == 2)
            {
                $status = 'Big Pending';
            }
            elseif($this->document_type == 3)
            {
                $status = 'Need Accessory Pending';
            }
        }

        return view('sourcing_documents.export_pending_document', compact('result','status'));
    }
}
