<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Document;
use App\Models\Supplier;
use App\Models\BranchUser;
use App\Models\DocumentStatus;
use App\Models\SourcingDocument;
use App\Models\LogisticsDocument;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class HomeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    protected function connection()
    {
        return new Document();
    }
    public function index()
    {

            $branches = BranchUser::where('user_id', auth()->user()->id)->with('branches')->get();

            return view('home', compact( 'branches'));

    }
    public function getTotalReturnDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['1', '2', '3','4','5', '6','7', '8','9','14','15'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getRejectReturnDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['3','5','7'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Reject Return Document!');
        }
    }
    public function getTotalExchangeDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->whereIn('document_status', ['1','2','3','4','5','6','7','8','9','10','11','14','15'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Exchange Document!');
        }
    }
    public function getRejectExchangeDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->whereIn('document_status', ['3','5','7'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Exchange Document!');
        }
    }

    public function getCompleteReturnDocument()
    {

        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->where('document_status', 9)->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Complete Return Document!');
        }
    }

    public function getCompleteExchangeDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->where('document_status', 11)->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Complete Exchange Document!');
        }
    }

    public function getOverdueExchangeDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            $inetrval = date('Y-m-d', strtotime(now() . ' - 14 days'));
            return $document->where('document_type', '=', '2')->where('deleted_at', null)
            ->where('document_status', '!=',13)
            ->whereIn('document_status', ['1', '2', '4', '6', '8','9','10','14','15'])
            ->where('operation_rg_in_updated_datetime', null)->where('operation_rg_out_updated_datetime', '<', $inetrval)->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Complete Exchange Document!');
        }
    }
    public function getNearlyOverdueExchangeDocument()
    {
        try {
                $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
                $document = $this->connection()->whereIn('branch_id', $user_branches);
                $intervalStart = now()->subDays(14)->format('Y-m-d');
                $intervalEnd = now()->subDays(10)->format('Y-m-d');

                return $document->where('document_type', '=', '2')
                    ->where('deleted_at', null)
                    ->where('document_status', '!=', 13)
                    ->whereIn('document_status', ['1', '2', '4', '6', '8', '9', '10', '14', '15'])
                    ->where('operation_rg_in_updated_datetime', null)
                    ->whereBetween('operation_rg_out_updated_datetime', [$intervalStart, $intervalEnd])
                    ->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Complete Exchange Document!');
        }
    }
    public function getSupplierCancelDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->where('document_status', 12)->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Supplier Cancel Document!');
        }
    }

    public function getcnPendingDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_status', 8)->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Supplier Cancel Document!');
        }
    }

    public function getdbPendingDocument()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = $this->connection()->whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->where('document_status', 10)->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Supplier Cancel Document!');
        }
    }

    public function getTotalLogisticsSmall()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['1', '2', '3','4','5','16','17','18','19','20','21'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Document!');
        }
    }
    public function getTotalLogisticsSmallFinishedDoc()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['18'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Document!');
        }
    }
    public function getTotalLogisticsBig()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->whereIn('document_status', ['1', '2', '3','4','5','16','17','18','19','20','21'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Document!');
        }
    }
    public function getTotalLogisticsBigFinishedDoc()
    {
        try{
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->whereIn('document_status', ['18'])->count();
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return redirect()->intended(route("home"))
            ->with('error', 'Fail to get Total Document!');
            }
    }
    public function getTotalLogisticsAccessory()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '3')->whereIn('document_status', ['1', '2', '3','4','5','16','17','18','19','20','21'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Document!');
        }
    }
    public function getTotalLogisticsAccessoryFinishedDoc()
    {
        try{
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '3')->whereIn('document_status', ['18'])->count();
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return redirect()->intended(route("home"))
            ->with('error', 'Fail to get Total Document!');
            }
    }

    public function getTotalSourcingSmall()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['1', '2', '3','4','5', '9','17','19','21','20','22','18'])->count();
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }

    public function getTotalSourcingSmallFinishedDoc()
    {
        try{
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '1')->whereIn('document_status', ['18'])->count();
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return redirect()->intended(route("home"))
            ->with('error', 'Fail to get Total Document!');
            }
    }
    public function getTotalSourcingBig()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '2')->whereIn('document_status', ['1', '2', '3','4','5', '9','17','19','21','20','22','18'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalSourcingBigFinishedDoc()
    {
        try{
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '2')->whereIn('document_status', ['18'])->count();
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return redirect()->intended(route("home"))
            ->with('error', 'Fail to get Total Document!');
            }
    }

    public function getTotalSourcingAccessary()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '3')->whereIn('document_status', ['1', '2','3','4','5','9','17','22','19','20','21','18'])->count();
            // dd($document);
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalAccessaryFinishedDoc()
    {
        try{
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            return $document->where('document_type', '3')->whereIn('document_status', ['18'])->count();
        }catch(\Exception $e){
            Log::debug($e->getMessage());
            return redirect()->intended(route("home"))
            ->with('error', 'Fail to get Total Document!');
            }
    }
    public function make_as_read($notification_id,$document_id)
    {
        $notification = auth()->user()->notifications->find($notification_id);
        // dd(auth()->user()->notifications);
        if($notification) {
            $notification->markAsRead();
        }
        return redirect()
                ->intended(route("documents.edit",$document_id));


    }
    /////see document ////
    public function see_document($document_id,$type,$notification_id)
    {
        $notification = auth()->user()->notifications->find($notification_id);
        if($notification) {
            $notification->markAsRead();
        }
        if($type == 1){
            return redirect()
                    ->intended(route("sourcing_documents.edit",$document_id));
        }else{
            return redirect()
                    ->intended(route("logistics_documents.edit",$document_id));
        }

    }
    ///Pending////
    public function getTotallogSmallPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '1')->whereIn('document_status', ['1','2', '4','16','17','19'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotallogBigPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '2')->whereIn('document_status', ['1','2', '4','16','17','19'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotallogAccessoryPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '3')->whereIn('document_status', ['1','2', '4','16','17','19'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingSmallPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '1')->whereIn('document_status', ['1', '2','4', '9','17','19','21'])->has('products')->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingBigPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '2')->whereIn('document_status', ['1', '2','4', '9','17','19','21'])->has('products')->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingAccessaryPending()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '3')->whereIn('document_status', ['1', '2','4', '9','17','19','21'])->has('products')->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    ////Reject/////
    public function getTotallogSmallReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
         return $document->where('document_type', '1')->whereIn('document_status', ['3','5','20'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotallogBigReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
        return $document->where('document_type', '2')->whereIn('document_status', ['3','5','20'])->count();


        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotallogAccessoryReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = LogisticsDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
        return $document->where('document_type', '3')->whereIn('document_status', ['3','5','20'])->count();


        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingBigReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '2')->whereIn('document_status', ['3','5','20','22'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingSmallReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '1')->whereIn('document_status', ['3','5','20','22'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    public function getTotalsourcingAccessaryReject()
    {
        try {
            $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $document = SourcingDocument::whereIn('branch_id', $user_branches);
            // dd($document);
            // return
          return $document->where('document_type', '3')->whereIn('document_status', ['3','5','20','22'])->count();

        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to get Total Return Document!');
        }
    }
    // public function getTotalsourcingAccessaryPending()
    // {
    //     try {
    //         $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
    //         $document = SourcingDocument::whereIn('branch_id', $user_branches);
    //         // dd($document);
    //         // return
    //       return $document->where('document_type', '3')->whereIn('document_status', ['1', '2', '3','4','5', '6','7', '8','14','15','16','17','19','21','20'])->count();

    //     } catch (\Exception $e) {
    //         Log::debug($e->getMessage());
    //         return redirect()
    //             ->intended(route("home"))
    //             ->with('error', 'Fail to get Total Return Document!');
    //     }
    // }
    // public function getTotalsourcingAccessaryPending()
    // {
    //     try {
    //         $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
    //         $document = SourcingDocument::whereIn('branch_id', $user_branches);
    //         // dd($document);
    //         // return
    //       return $document->where('document_type', '3')->whereIn('document_status', ['1', '2', '3','4','5', '6','7', '8','14','15','16','17','19','21','20'])->count();

    //     } catch (\Exception $e) {
    //         Log::debug($e->getMessage());
    //         return redirect()
    //             ->intended(route("home"))
    //             ->with('error', 'Fail to get Total Return Document!');
    //     }
    // }
    // public function getTotalsourcingAccessaryPending()
    // {
    //     try {
    //         $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
    //         $document = SourcingDocument::whereIn('branch_id', $user_branches);
    //         // dd($document);
    //         // return
    //       return $document->where('document_type', '3')->whereIn('document_status', ['1', '2', '3','4','5', '6','7', '8','14','15','16','17','19','21','20'])->count();

    //     } catch (\Exception $e) {
    //         Log::debug($e->getMessage());
    //         return redirect()
    //             ->intended(route("home"))
    //             ->with('error', 'Fail to get Total Return Document!');
    //     }
    // }

    public function notifications()
    {
        return number_convert(auth()->user()->unreadNotifications->count());
    }

    public function getDownload(){
        //PDF file is stored under project/public/download/info.pdf
        $file="../public/download/Imported Claim System (Operation User Guide).pdf";
        return response()->download($file);
}
}
