<?php

namespace App\Exports;

use App\Models\Document;
use App\Models\BranchUser;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class PendingExport implements  FromView
{
    protected $detail_type ;
    public function __construct($detail_type='') {
        $this->detail_type = $detail_type;
    }
    protected function connection()
    {
        return new Document();
    }
    public function view(): View
    {
        $result = $this->connection();
        $inetrval = date('Y-m-d', strtotime(now() . ' - 14 days'));
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        if($this->detail_type == 5)
        {
            $result = $result->where('document_type', '2')->where('deleted_at', null)
            ->whereIn('document_status', ['1', '2', '4', '6', '8','9','10'])
            ->where('operation_rg_in_updated_datetime', null)->where('operation_rg_out_updated_datetime', '<', $inetrval);
            $status = "All Branch Over Due Exchange";
        }
        else if($this->detail_type == 1)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '1')
            ->where('document_status', 9);
            $status = "Return Doc List";
        }
        else if($this->detail_type == 2)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '1')
            ->whereIn('document_status', ['1', '2', '4', '6', '8', '14', '15']);
            // dd($result->get());
            $status = "Pending Return Document";

        }
        else if($this->detail_type == 3)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '2')
            ->where('document_status', 11);

        }
        else if($this->detail_type == 4)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '2')
            ->whereIn('document_status',['1', '2', '4', '6', '8', '9', '10', '14', '15']);
            // dd($result->get());
            $status = "Pending Exchange Document";

        }
        else if($this->detail_type == 7)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '1')
            ->whereIn('document_status', ['3','5','7']);
            $status = "Reject Return Document";
        }
        else if($this->detail_type == 8)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '2')
            ->whereIn('document_status', ['3','5','7']);
            $status = 'Reject Exchange Document';
        }
        else if($this->detail_type == 9)
        {
            $result = $result->whereIn('branch_id', $user_branches)->where('document_type', '2')
            ->where('document_status', 12);
            $status ="Supplier Cancel Document" ;
        }
        else if($this->detail_type == 10)
        {
            $result = $result->whereIn('branch_id', $user_branches)
            ->where('document_status', 8);
            $status = "Pending CN Document";
        }
        else if($this->detail_type == 11)
        {
            $result = $result->whereIn('branch_id', $user_branches)
            ->where('document_type', '2')->where('document_status', 10);
            $status ="Pending DB Document";
        }


        $result = $result->with('Category')->orderBy('updated_at','DESC')->get();
        return view('documents.export_pending_document', compact('result', 'status'));
    }
}
