<?php

namespace App\Http\Controllers;

use Exception;
use Throwable;
use PDF as MPDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\BranchUser;
use App\Jobs\ImageReadyJob;
use App\Models\DamageRemark;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Models\DocumentRemark;
use App\Models\DocumentStatus;
use App\Models\SourcingProduct;
use App\Models\SourcingDocument;
use App\Models\ReferenceDocument;
use App\Models\DamageRemarkReason;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\SourcingProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Route;
use App\Exports\SourcingPendingExport;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Exports\LogisticsDamageRpBigExport;
use App\Exports\SourcingsDamageRpBigExport;

use App\Notifications\DocumentNotification;
use Illuminate\Support\Facades\Notification;

class SourcingDocumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function connection(){

        return new SourcingDocument;
    }

    public function index(Request $request)
    {
    // try {
        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $document_status = DocumentStatus::get();
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.sourcing_small_document');
            $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.sourcing_big_document');
            $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.sourcing_accessary');
            $document_status = DocumentStatus::get();
        }

        $finished_document = DocumentStatus::where('document_status', '=', 9)->get();
        $document_type = isset($request->type) ? $request->type : '';
        $categories = Category::get();

        $old_from_date = Session::get('old_from_date') ?? '';
        $old_to_date = Session::get('old_to_date') ?? '';
        $old_branch_id = Session::get('branch_id') ?? '';
        $old_document_status = Session::get('document_status') ?? '';
        $old_category_id = Session::get('category') ?? '';

        return view('sourcing_documents.index', compact(
            'branches',
            'document_status',
            'document_type_title',
            'document_type',
            'categories',
            'old_from_date',
            'old_to_date',
            'old_branch_id',
            'old_document_status',
            'old_category_id'
        ));
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("home"))
        //         ->with('error', 'Fail to load Data!');
        // }
    }

    public function small()
    {
        clearSession();

        $user = Auth::guard()->user();
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);

        $sourcing_documents = SourcingDocument::where('document_type','1')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,11,18,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,11,18,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','1')
        ->orderBy('updated_at','DESC')->paginate(10);

        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12])->orderBy('sorting_id')->get();
        return view('sourcing_documents.small', compact('sourcing_documents','branches','categories','document_statuses'));
        // dd($sourcing_documents);
    }

    public function big()
    {
        clearSession();

        $user = Auth::guard()->user();
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);

        $sourcing_documents = SourcingDocument::where('document_type','2')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,11,18,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,11,18,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','2')
        ->orderBy('updated_at','DESC')->paginate(10);

        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12,13])->orderBy('sorting_id')->get();

        return view('sourcing_documents.big', compact('sourcing_documents','branches','categories','document_statuses'));
        // dd($sourcing_documents);
    }

    public function need_accessory()
    {
        clearSession();

        $user = Auth::guard()->user();
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);

        $sourcing_documents = SourcingDocument::where('document_type','3')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,11,18,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,11,18,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','3')
        ->orderBy('updated_at','DESC')->paginate(10);

        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12])->orderBy('sorting_id')->get();

        return view('sourcing_documents.need_accessory', compact('sourcing_documents','branches','categories','document_statuses'));
        // dd($sourcing_documents);
    }

    public function new_index(Request $request)
    {
        // dd('hi');
        $sourcing_documents = SourcingDocument::paginate(10);
    // try {
        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        // $document_status = DocumentStatus::get();
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.sourcing_small_document');
            $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.sourcing_big_document');
            $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.sourcing_accessary');
            $document_status = DocumentStatus::get();
        }

        // $finished_document = DocumentStatus::where('document_status', '=', 9)->get();
        $document_type = isset($request->type) ? $request->type : '';
        // dd($document_type);
        $categories = Category::get();

        $old_from_date = Session::get('old_from_date') ?? '';
        $old_to_date = Session::get('old_to_date') ?? '';
        $old_branch_id = Session::get('branch_id') ?? '';
        $old_document_status = Session::get('document_status') ?? '';
        $old_category_id = Session::get('category') ?? '';

        return view('sourcing_documents.new_index', compact(
            'branches',
            // 'document_status',
            'document_type_title',
            'document_type',
            'categories',
            'old_from_date',
            'old_to_date',
            'old_branch_id',
            'old_document_status',
            'old_category_id',
            'sourcing_documents'
        ));
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("home"))
        //         ->with('error', 'Fail to load Data!');
        // }
    }

    public function new_search_result(Request $request)
    {

        // dd($request->all());
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.sourcing_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.sourcing_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.sourcing_accessary');
            // $document_status = DocumentStatus::get();
        }

        $document_type          = isset($request->type) ? $request->type : '';

        $document_no            = $request->document_no;
        $document_from_date     = $request->document_from_date;
        $document_to_date       = $request->document_to_date;
        $branch_id              = $request->branch_id;
        $document_status        = $request->document_status;
        $category_id            = $request->category_id;
        $next_step              = $request->next_step;
        $brand                  = $request->brand;
        $result                 = SourcingDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12])->orderBy('sorting_id')->get();


        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);
        Session::put('brand', $brand);

        // dd($document_status);
        if (!empty($document_no)) {
            // dd(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'));
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        // if (!empty($document_from_date)) {
        //     $result = $result->where('created_at', 'like', '%'.$document_from_date.'%');
        // }
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }
        if (!empty($document_from_date) || !empty($document_to_date)) {
            // $result = $result->where('created_at', $document_from_date);
            if($document_from_date === $document_to_date)
            {
                $result = $result->whereDate('created_at', $document_from_date);
            }
            else
            {
                if($document_from_date)
                {
                    $from_date = Carbon::parse($document_from_date);
                }
                if($document_to_date)
                {
                    $to_date = Carbon::parse($document_to_date)->endOfDay();;
                }
                $result = $result->whereBetween('created_at', [$document_from_date , $document_to_date]);
            }
        }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id',$branch_id);
        }else{
            $user = Auth::guard()->user();
            $branch_ids =$user->user_branches->pluck('branch_id');
            $result = $result->whereIn("branch_id",$branch_ids);
        }


        if (!empty($document_status)) {
            if($document_status[0]!=null || $document_status[0]!=0)
            {

                $result = $result->whereIn('document_status', $document_status);
            }
        }
        if (!empty($next_step)) {
            $result = $result->where('document_status', $next_step);
        }

        if (!empty($category_id)) {
            $result = $result->where('category_id', $category_id);
        }
        if (!empty($brand)) {
        //    dd($brand);
           $product = SourcingProduct::whereIn('product_brand_name',$brand)->pluck('document_id')->toArray();
            $result = $result->whereIn('id', $product);
            // dd($result->get());
        }


        if(empty($document_no) && (empty($document_from_date) && empty($document_to_date)) && empty($branch_id) && empty($document_status) && empty($next_step) && empty($category_id)){
            $filterdate = Carbon::now()->subMonths(1);

            $result = $result->where('document_date', ">=", $filterdate)
            ->where('document_type',$document_type)
            ->whereNotIn('document_status', [3,5,7,11,18,22,23])
            ->where('document_date',">=",$filterdate)
            ->orWhereNotIn('document_status', [3,5,7,11,18,22,23])->whereIn("branch_id",$branch_ids)->where('document_type',$document_type);
            // dd($document_type);
        }

        $sourcing_documents = $result->where('document_type',$document_type)->latest()->paginate(10);
        // dd($result->where('document_type',$document_type)->get());
        $sourcing_documents->appends($request->all());
        if($request->ajax()){
            return response()->json($sourcing_documents, 200);
        }

        return view('sourcing_documents.new_search_result', compact(
            'branches',
            // 'document_status',
            'document_type_title',
            'document_type',
            'categories',
            'document_from_date',
            'document_to_date',
            'branch_id',
            'document_status',
            'category_id',
            'sourcing_documents',
            'document_statuses'
        ));
    }

    public function create()
    {
        try {
            $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
            $document_remark_types = DocumentRemark::get();
            $categories = Category::get();
            $branches = BranchUser::where('user_id', auth()->user()->id)->with('branches')->get();
            return view('sourcing_documents.create', compact('suppliers', 'document_remark_types', 'categories', 'branches'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("sourcing_documents.index"))
                ->with('error', 'Fail to load Create Form!');
        }
    }

    public function store(Request $request)
    {
        // try {
            $filename = null;
            request()->validate([
                'document_type' => 'required',
                'operation_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
            ]);

            $request->validate([
                'operation_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
            ]);
            $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['operation_attach_file']['name'];
            try {
                if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp1')->put($filename, fopen($request->file('operation_attach_file'), 'r+'));
                }else{
                    Storage::disk('ftp3')->put($filename, fopen($request->file('operation_attach_file'), 'r+'));
                }


                } catch (Exception $e) {
                return 'Something went wrong with the connection ' . $e->getMessage();
            }

            $filename_2 = null;

            $filename_2 = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['operation_attach_file_2']['name'];

            try {
                if($request->operation_attach_file_2){
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename_2, fopen($request->file('operation_attach_file_2'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename_2, fopen($request->file('operation_attach_file_2'), 'r+'));
                    }

                }else{
                    $filename_2 = null;
                }

                } catch (Exception $e) {
                return 'Something went wrong with the connection ' . $e->getMessage();
            }
            $filename_3 = null;

            request()->validate([
                'document_type' => 'required',


            ]);

            $filename_3 = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['excel_attach_file']['name'];
            if($request->excel_attach_file){
                $request->validate([
                    'excel_attach_file' => 'mimes:xlsx,xls,csv|max:30720',
                ]);
            }
            try {
                if($request->excel_attach_file){
                    $request->validate([
                        'excel_attach_file' => 'mimes:xlsx,xls,csv|max:30720',
                    ]);
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename_3, fopen($request->file('excel_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename_3, fopen($request->file('excel_attach_file'), 'r+'));
                    }
                }else{
                    $filename_3 = null;
                }

                } catch (Exception $e) {
                return 'Something went wrong with the connection ' . $e->getMessage();
            }
            $request['document_no'] = $this->generate_doc_no($request->document_type, $request->document_date, $request->branch_id);
            $request['operation_id'] = Auth::id();
            $now_hour = date('H');
            $now_minute = date('i');
            $now_secound = date('s');
            $request['operation_updated_datetime'] = date('Y-m-d H:i:s', strtotime('+ ' . $now_hour . ' hour + ' . $now_minute . ' minutes + ' . $now_secound . ' seconds', strtotime($request['document_date'])));
            $request['branch_id'] = $request->branch_id;
            $request['category_id'] = (int) $request->category_id;
            $request['document_type'] = (int) $request->document_type;
            $request['car_no'] =  $request->car_no;
            $request['operation_remark'] = $request->operation_remark;
            $request['document_status'] = 1;

            $sourcings_document = SourcingDocument::create($request->except(['operation_attach_file','operation_attach_file_2','excel_attach_file']) +
            ['operation_attach_file' => $filename] + ['operation_attach_file_2' => $filename_2]  + ['excel_attach_file' => $filename_3]);
            return redirect()->route('sourcing_documents.edit', $sourcings_document->id);
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("logistics_documents.index"))
        //         ->with('error', 'Fail to Store Department!');
        // }
    }


    public function show()
    {
        //
    }


    public function edit($id, Request $request)
    {
        $sourcing = SourcingDocument::where('id',$id)->first();

        // dd( $id,$request->all(),Session::get('currentURL'));
        $branch = Branch::where('branch_id', $sourcing->branch_id)->first();
        $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
        $user_role = Auth::user()->roles->pluck('name')->first();
        $categories = Category::get();
        $damage_remark_types = DamageRemark::get();
        $exchange_rate= ExchangeRate::select('sell')->where('type', 'Baht')->whereDate('created_at',Carbon::today())->first();
        if($exchange_rate){
            $exchange_rate = $exchange_rate->sell;
        }else{
            $exchange_rate =  0;
        }
        $sourcing_products = SourcingProduct::where('document_id',$sourcing->id)
        ->withTrashed()->get();
        // dd($sourcing_products);
        $check_type = null;
        foreach($sourcing_products as $check_product){
            if(!$check_product->finished_status){
                 $check_type = 1;
            }
        }
        $check_type = $check_type == 1 ? 1 : 2;
        $reference_nos = ReferenceDocument::where('old_reference_no',$sourcing->document_no)->get();
        $old_reference_no = ReferenceDocument::where('new_reference_no',$sourcing->document_no)->first();
        $currentURL = Session::get('currentURL') != null ? Session::get('currentURL') : "";
        $reasons = DamageRemarkReason::get();
        // $damage_remark_type =  SourcingProduct::where('document_id',$sourcing->id)->pluck('damage_remark_types')->toArray()[0];
        // dd($damage_remark_type);
        // dd($request->all());
        return view('sourcing_documents.edit',compact('branch','suppliers','user_role','categories','sourcing','damage_remark_types',
        'exchange_rate','check_type','reference_nos','old_reference_no', 'currentURL'));
    }


    public function select_product($id)
    {
        // dd(request()->all());
        $sourcing = SourcingDocument::where('id',$id)->first();
        $branch = Branch::where('branch_id', $sourcing->branch_id)->first();
        $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
        $user_role = Auth::user()->roles->pluck('name')->first();
        $categories = Category::get();
        $damage_remark_types = DamageRemark::get();
        $exchange_rate= ExchangeRate::select('sell')->where('type', 'Baht')->whereDate('created_at',Carbon::today())->first();
        if($exchange_rate){
            $exchange_rate = $exchange_rate->sell;
        }else{
            $exchange_rate =  0;
        }
        $sourcing_products = SourcingProduct::where('document_id',$sourcing->id)->withTrashed()->get();
        // dd($sourcing_products);
        $check_type = null;
        foreach($sourcing_products as $check_product){
            if(!$check_product->finished_status){
                 $check_type = 1;
            }
        }
        $check_type = $check_type == 1 ? 1 : 2;
        // $ref_no = ReferenceDocument::where('old_reference_no', request()->all())->get();
        $ref_no = request()->ref;
        // dd($ref_no);
        return view('sourcing_documents.select_product',compact('branch','suppliers','user_role','categories','sourcing','damage_remark_types','exchange_rate','check_type', 'ref_no'));
    }

    public function test($id)
    {
        $sourcing = SourcingDocument::where('id',$id)->first();
        $branch = Branch::where('branch_id', $sourcing->branch_id)->first();
        $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
        $user_role = Auth::user()->roles->pluck('name')->first();
        $categories = Category::get();
        $damage_remark_types = DamageRemark::get();
        $exchange_rate= ExchangeRate::select('sell')->where('type', 'Baht')->whereDate('created_at',Carbon::today())->first();
        if($exchange_rate){
            $exchange_rate = $exchange_rate->sell;
        }else{
            $exchange_rate =  0;
        }
        $sourcing_products = SourcingProduct::where('document_id',$sourcing->id)->get();
        $check_type = null;
        foreach($sourcing_products as $check_product){
            if(!$check_product->finished_status){
                 $check_type = 1;
            }
        }
        $check_type = $check_type == 1 ? 1 : 2;

        return view('sourcing_documents.test',compact('branch','suppliers','user_role','categories','sourcing','damage_remark_types','exchange_rate','check_type'));
    }


    public function update(Request $request,$id)
    {

        $sourcings = SourcingDocument::where('id', $id)->first();
        // dd($sourcings->document_status ,Gate::allows('update-document-acc-complete'));
        if ($sourcings->document_status == 1 && Gate::allows('edit-document-operation-attach-file')) {
            $update_sourcings['operation_id'] = Auth::id();
            $sourcings['car_no'] = $request->car_no;

            $filename = "";
            if ($request->operation_attach_file || $request->operation_attach_file_2 || $request->excel_attach_file) {
                // $request->validate([
                //     'operation_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                // ]);
                $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['operation_attach_file']['name'];
                try {
                    if($request->operation_attach_file){
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename, fopen($request->file('operation_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename, fopen($request->file('operation_attach_file'), 'r+'));
                        }

                    }else{
                        $filename = $sourcings->operation_attach_file;
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }
                $filename_2 = "";
                $filename_2 = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['operation_attach_file_2']['name'];
                try {
                    if($request->operation_attach_file_2){
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename_2, fopen($request->file('operation_attach_file_2'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename_2, fopen($request->file('operation_attach_file_2'), 'r+'));
                        }

                    }else {
                        $filename_2= $sourcings->operation_attach_file_2;

                    }
                }
                catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }
                $filename_3 = "";
                $filename_3 = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['excel_attach_file']['name'];

                if($request->excel_attach_file){
                    $request->validate([
                        'excel_attach_file' => 'mimes:xlsx,xls,csv|max:30720',
                    ]);
                }
                try {
                    if($request->excel_attach_file){
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            $aa= Storage::disk('ftp1')->put($filename_3, fopen($request->file('excel_attach_file'), 'r+'));
                        }else{
                            $aa= Storage::disk('ftp3')->put($filename_3, fopen($request->file('excel_attach_file'), 'r+'));
                        }

                    }
                    else {
                        $filename_3 = $sourcings->excel_attach_file;
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename = $filename ?? $sourcings->operation_attach_file;
                $filename_2= $filename_2 ?? $sourcings->operation_attach_file_2;
                $filename_3= $filename_3 ?? $sourcings->excel_attach_file;

                $status = $sourcings->update($request->except(['operation_attach_file','operation_attach_file_2','excel_attach_file']) +
                ['operation_attach_file' => $filename] + ['operation_attach_file_2' => $filename_2]  + ['excel_attach_file' => $filename_3]);
            } else {
                $filename = $sourcings->operation_attach_file;
                $filename_2 = $sourcings->operation_attach_file_2;
                $filename_3 = $sourcings->excel_attach_file;
                $filename = $sourcings->operation_attach_file;
                $sourcings->update($request->except(['operation_attach_file']) + ['operation_attach_file' => $filename]
                + ['operation_attach_file_2' => $filename_2]  + ['excel_attach_file' => $filename_3]);
            }
        }

        if ($sourcings->document_status == 2 && Gate::allows('update-document-ch-complete')) {

            $update_sourcings['category_head_id'] = Auth::id();
            $update_sourcings['category_head_remark'] = $request->merchandising_remark;

        }

        // if (($sourcings->document_status == 4 || $sourcings->document_status == 17 || $sourcings->document_status == 9 ) && Gate::allows('update-document-acc-complete')) {
        //     if ($sourcings->document_type == 2) {
        //         // dd("hello");
        //         $request->validate([
        //             'issue_no' => 'required',
        //         ]);
        //     }
        //     $update_sourcings['accounting_id'] = Auth::id();
        //     $sourcings['issue_doc_no'] = $request->issue_no;
        //     $update_sourcings['accounting_remark'] = $request->accounting_remark;

        //     $filename = "";
        //     if ($request->accounting_attach_file) {
        //         $request->validate([
        //             'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
        //         ]);
        //         $filename = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
        //         try {
        //             Storage::disk('ftp1')->put($filename, fopen($request->file('accounting_attach_file'), 'r+'));

        //         } catch (Exception $e) {
        //             return 'Something went wrong with the connection ' . $e->getMessage();
        //         }
        //         $filename = $filename ?? $sourcings->accounting_attach_file;

        //         $status = $sourcings->update($request->except(['accounting_attach_file']) + ['accounting_attach_file' => $filename]);
        //     } else {
        //         $filename = $sourcings->accounting_attach_file;
        //         $sourcings->update($request->except(['accounting_attach_file']) + ['accounting_attach_file' => $filename]);
        //     }
        // }

        // add attached file 1

        if (($sourcings->document_status == 4 || $sourcings->document_status == 17 || $sourcings->document_status == 19 ||$sourcings->document_status == 21 ) && (Gate::allows('update-document-acc-complete')) &&
        (Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin')){

            if ($sourcings->document_type == 2 || $sourcings->document_type == 1) {
                if( Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin'){
                    if($request->file1 == null ){
                        $request->validate([
                            'issue_no' => 'required',
                            'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                            // 'accounting_attach_file2' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                        ]);
                    }else{

                        $request->validate([
                            'issue_no' => 'required',
                        ]);

                    }
                }
            }
            $update_sourcings['accounting_id'] = Auth::id();
            $sourcings['issue_doc_no'] = $request->issue_no;
            $update_sourcings['accounting_remark'] = $request->accounting_remark;

            $filename1 = $sourcings->accounting_attach_file; // First attached file
            $filename2 = $sourcings->accounting_attach_file2; // Second attached file

                if ($request->accounting_attach_file) {
                    $filename1 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                        }

                    } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }
                    $filename1 = $filename1 ?? $sourcings->accounting_attach_file;
                    // dd($filename);
                    $sourcings->update($request->except(['accounting_attach_file']) + ['accounting_attach_file' => $filename1]);
                }

        }


        if (($sourcings->document_status == 4 || $sourcings->document_status == 19) && Gate::allows('update-document-log-complete')  ) {
            // dd("hi");
            $update_sourcings['sourcing_id'] = Auth::id();
            $update_sourcings['sourcing_remark'] = $request->sourcing_remark;
            $update_sourcings['damage_remark'] = $request->damage_remark_types;
            $update_sourcings['car_no'] = $request->car_no;
            if ($request->sourcing_attach_file) {
                $request->validate([
                    'sourcing_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                    'car_no' => 'required',
                ]);
                $filename = 'lo_' . auth()->id() . '_' . time() . '_' . $_FILES['sourcing_attach_file']['name'];
                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename, fopen($request->file('sourcing_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename, fopen($request->file('sourcing_attach_file'), 'r+'));
                    }
                    } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename = $filename ?? $sourcings->sourcing_attach_file;
                $sourcings->update($request->except(['sourcing_attach_file']) + ['sourcing_attach_file' => $filename]);
            } else {
                $filename = $sourcings->accounting_db_attach_file;
                $sourcings->update($request->except(['sourcing_attach_file', 'sourcing_attach_file']) + ['sourcing_attach_file' => $filename, 'sourcing_attach_file' => $sourcings->sourcing_attach_file]);
            }

        }
        if (($sourcings->document_status == 17) && Gate::allows('update-document-log-complete') && (Auth::user()->roles->pluck('name')->first() == 'Sourcing' || Auth::user()->roles->pluck('name')->first() == 'Mer Approver & Sourcing') ) {
            $update_sourcings['cn_id'] = Auth::id();
            $update_sourcings['sourcing_remark'] = $request->sourcing_remark;
            if($request->cnfile == ''){
                if ($request->cn_attach_file) {
                    $request->validate([
                        'cn_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                    ]);
                    $filename = 'cn_' . auth()->id() . '_' . time() . '_' . $_FILES['cn_attach_file']['name'];
                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename, fopen($request->file('cn_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename, fopen($request->file('cn_attach_file'), 'r+'));
                        }

                        } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }
                    $filename = $filename ?? $sourcings->cn_attach_file;
                    $sourcings->update($request->except(['cn_attach_file']) + ['cn_attach_file' => $filename]);
                    // dd($sourcings);
                } else {
                    $filename = $sourcings->accounting_db_attach_file;
                    $sourcings->update($request->except(['cn_attach_file', 'cn_attach_file']) + ['cn_attach_file' => $filename, 'cn_attach_file' => $sourcings->cn_attach_file]);
                }
            }else{

                $sourcings->update($request->except(['cn_attach_file']));

            }
        }

        // if(($sourcings->document_status == 21 || $sourcings->document_status == 19) && Gate::allows('update-document-acc-complete')  && (Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin')){

        //     if ($sourcings->document_type == 2 || $sourcings->document_type == 1) {
        //         if( Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin'){
        //             if($request->file1 == null && $request->file2 == null){
        //                 $request->validate([
        //                     'issue_no' => 'required',
        //                     'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
        //                     'accounting_attach_file2' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
        //                 ]);
        //             }elseif($request->file1 == null){
        //                 // dd("hello");
        //                 $request->validate([
        //                     'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
        //                 ]);
        //             }
        //             elseif($request->file2 == null){
        //                 // dd("hello");
        //                 $request->validate([
        //                     'accounting_attach_file2' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
        //                 ]);
        //             }
        //             else {
        //                 $request->validate([
        //                     'issue_no' => 'required',
        //                 ]);
        //             }
        //         }
        //     }
        //     $update_sourcings['accounting_id'] = Auth::id();
        //     $sourcings['issue_doc_no'] = $request->issue_no;
        //     $update_sourcings['accounting_remark'] = $request->accounting_remark;

        //     $filename1 = $sourcings->accounting_attach_file; // First attached file
        //     $filename2 = $sourcings->accounting_attach_file2; // Second attached file

        //     if($request->accounting_attach_file != null &&  $request->accounting_attach_file2){
        //         $filename1 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
        //         try {
        //             Storage::disk('ftp1')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
        //         } catch (Exception $e) {
        //             return 'Something went wrong with the connection ' . $e->getMessage();
        //         }
        //         $filename1 = $filename1 ?? $sourcings->accounting_attach_file;
        //         $filename2 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file2']['name'];
        //         try {
        //             Storage::disk('ftp1')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
        //         } catch (Exception $e) {
        //             return 'Something went wrong with the connection ' . $e->getMessage();
        //         }
        //         $filename2 = $filename2 ?? $sourcings->accounting_attach_file2;
        //         $sourcings->update($request->except(['accounting_attach_file' , 'accounting_attach_file2']) + ['accounting_attach_file' => $filename1 ,'accounting_attach_file2'=>$filename2]);
        //     }
        //     else {
        //         if ($request->accounting_attach_file) {
        //             $filename1 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
        //             try {
        //                 Storage::disk('ftp1')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
        //             } catch (Exception $e) {
        //                 return 'Something went wrong with the connection ' . $e->getMessage();
        //             }
        //             $filename1 = $filename1 ?? $sourcings->accounting_attach_file;
        //             // dd($filename);
        //             $sourcings->update($request->except(['accounting_attach_file']) + ['accounting_attach_file' => $filename1]);
        //         }

        //         if ($request->accounting_attach_file2) {
        //             $filename2 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file2']['name'];
        //             try {
        //                 Storage::disk('ftp1')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
        //             } catch (Exception $e) {
        //                 return 'Something went wrong with the connection ' . $e->getMessage();
        //             }
        //             $filename2 = $filename2 ?? $sourcings->accounting_attach_file2;
        //             // dd($filename);
        //             $sourcings->update($request->except(['accounting_attach_file2']) + ['accounting_attach_file2' => $filename2]);
        //         }
        //     }
        //     if($sourcings->accounting_attach_file2 || $sourcings->accounting_attach_file){
        //         $sourcings->update($request->except(['accounting_attach_file','accounting_attach_file2']));
        //     }
        // }

        // dd($sourcings->document_status ,Gate::allows('update-document-acc-complete'));
        if ($sourcings->document_status == 9 &&(Gate::allows('add-issue-no') || Gate::allows('update-document-acc-complete'))) {

            if ($sourcings->document_type == 2 || $sourcings->document_type == 1) {
                if( Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin'){
                    // dd($request->file1 == null && $request->file2 == null);
                    // if($request->file1 == null && $request->file2 == null){
                    //     $request->validate([
                    //         'issue_no' => 'required',
                    //         'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                    //         'accounting_attach_file2' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                    //     ]);
                    // }
                    // else{
                        // if($request->file1 == null){
                        //     // dd("hello");
                        //     $request->validate([
                        //         'accounting_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                        //     ]);
                        // }

                        if($request->file2 == null){
                            // dd("hello");
                            $request->validate([
                                'accounting_attach_file2' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                            ]);
                        }
                        // if($request->issue_no == null) {
                        //     $request->validate([
                        //         'issue_no' => 'required',
                        //     ]);
                        // }
                    // }
                }
            }
            $update_sourcings['accounting_id'] = Auth::id();
            // $sourcings['issue_doc_no'] = $request->issue_no;
            $update_sourcings['accounting_remark'] = $request->accounting_remark;

            // $filename1 = $sourcings->accounting_attach_file; // First attached file
            // $filename2 = $sourcings->accounting_attach_file2; // Second attached file

            // dd($request->accounting_attach_file2 != null ,  $request->acounting_attach_file != null);
            if($request->accounting_attach_file != null &&  $request->accounting_attach_file2 != null){
                $filename1 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }
                $filename1 = $filename1 ?? $sourcings->accounting_attach_file;

                $filename2 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file2']['name'];
                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }
                $filename2 = $filename2 ?? $sourcings->accounting_attach_file2;
                // dd($filename1 ,$filename2);
                $sourcings->update($request->except(['accounting_attach_file' , 'accounting_attach_file2']) + ['accounting_attach_file' => $filename1 ,'accounting_attach_file2'=>$filename2]);
            }
            else {
                if ($request->accounting_attach_file) {
                    $filename1 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file']['name'];
                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename1, fopen($request->file('accounting_attach_file'), 'r+'));
                        }
                    } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }
                    $filename1 = $filename1 ?? $sourcings->accounting_attach_file;
                    // dd($filename);
                    $sourcings->update($request->except(['accounting_attach_file']) + ['accounting_attach_file' => $filename1]);
                }

                if ($request->accounting_attach_file2) {
                    $filename2 = 'ac_' . auth()->id() . '_' . time() . '_' . $_FILES['accounting_attach_file2']['name'];
                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename2, fopen($request->file('accounting_attach_file2'), 'r+'));
                        }

                    } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }
                    $filename2 = $filename2 ?? $sourcings->accounting_attach_file2;
                    // dd($filename);
                    $sourcings->update($request->except(['accounting_attach_file2']) + ['accounting_attach_file2' => $filename2]);
                }
            }
            if($sourcings->accounting_attach_file2 || $sourcings->accounting_attach_file){
                $sourcings->update($request->except(['accounting_attach_file','accounting_attach_file2']));
            }
            // if($sourcings->accounting_attach_file2 ){
            //     $sourcings->update($request->except(['accounting_attach_file2']));
            // }
        }

        // END

        $sourcings->update($update_sourcings);
        return redirect()->route('sourcing_documents.edit', $sourcings->id)->with("updatesuccess","Your document is updated successfully");
    }


    public function sourcing_search_result(Request $request)
    {
        // try{
        $document_no = (!empty($_GET["document_no"])) ? ($_GET["document_no"]) : ('');
        $fromDate = (!empty($_GET["document_from_date"])) ? ($_GET["document_from_date"]) : ('0');
        $fromDate = $fromDate ?? Session::get('old_from_date');
        $toDate = (!empty($_GET["document_to_date"])) ? ($_GET["document_to_date"]) : ('0');
        $toDate = $toDate ?? Session::get('old_to_date');
        $document_type = ($_GET["document_type"]) ? ($_GET["document_type"]) : ('');
        $document_branch = (!empty($_GET["document_branch"])) ? ($_GET["document_branch"]) : ('0');
        $document_branch = $document_branch ?? Session::get('branch_id');
        $document_status = (!empty($_GET["document_status"])) ? ($_GET["document_status"]) : ('0');
        $document_status = $document_status ?? Session::get('document_status');

        $category = (!empty($_GET["category"])) ? ($_GET["category"]) : ('0');
        $category = $category ?? Session::get('category');
        $result = $this->connection();

        if ($fromDate != "0") {
            Session::put('old_from_date', $fromDate);
            $dateStr = str_replace("/", "-", $fromDate);
            $fromDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('sourcing_documents.document_date', '>=', $fromDate);
        } else {
            Session::put('old_from_date', $fromDate);
        }
        if ($toDate != "0") {
            Session::put('old_to_date', $toDate);
            $dateStr = str_replace("/", "-", $toDate);
            $toDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('sourcing_documents.document_date', '<=', $toDate);
        } else {
            Session::put('old_to_date', $toDate);
        }
        // if ($document_type == 2 && $document_status == '') {
        //     $result = $result->where('documents.document_type', $document_type)->whereIn('document_status', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '13', '14', '15']);
        // }
        if ($document_type == 1) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        if ($document_type == 2) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        if ($document_type == 3) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        // if ($document_no != "") {
        //     $result = $result->where('document_no', 'ilike', '%' . $document_no . '%');
        // }
        if (($document_no != "")) {

            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        if ($document_status != "0") {
            Session::put('document_status', $document_status);

            $result = $result->where('sourcing_documents.document_status', $document_status);
        } else {
            Session::put('document_status', $document_status);
        }

        if ($document_branch != "0") {
            Session::put('branch_id', $document_branch);
            $result = $result->where('sourcing_documents.branch_id', $document_branch);
        } else {
            Session::put('branch_id', $document_branch);
        }
        if ($category != "0") {
            Session::put('category', $category);
            $result = $result->where('sourcing_documents.category_id', $category);
        } else {
            Session::put('category', $category);
        }
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        $result = $result->whereIn('branch_id', $user_branches);
        $can_delete_document = Gate::allows('delete-document');

        $result = $result->with('Category', 'Products')->orderBy('updated_at', 'DESC')->get();

        return DataTables::of($result)
            ->editColumn('document_status', function ($data) {
                if($data->document_status == 4){
                return 'Cat.Head Acknowledge';
            }
                return $data->DocumentStatus->document_status_name;
            })
            ->addColumn('check_product', function ($data) {
                if($data->Products()->count()){
                    return 1;
                }else{
                    return 2;
                };
            })
            ->addColumn('check_reject_status', function ($data) {
                if($data->document_status == 3   || $data->document_status == 5 || $data->document_status == 20 || $data->document_status == 22){
                    return 1;
                }else{
                    return 2;
                };
            })
            ->editColumn('category', function ($data) {
                if (isset($data->Category)) {
                    $category = $data->Category->category_name;
                    $sub_category = explode(" ", $category);
                    $category = $sub_category[0];
                    $checkCategory = strpos($category, '/');
                    if ($checkCategory == true) {
                        $sub_category = explode("/", $category);
                        $category = $sub_category[0];
                    }
                    return $category;
                }
                return '';
            })
            ->editColumn('operation_updated_datetime', function ($data) {
                return $data->operation_updated_datetime ? date('d/m/Y', strtotime($data->operation_updated_datetime)) : '';
            })
            ->editColumn('branch_manager_updated_datetime', function ($data) {
                return $data->branch_manager_updated_datetime ? date('d/m/Y', strtotime($data->branch_manager_updated_datetime)) : '';
            })
            ->editColumn('category_head_updated_datetime', function ($data) {
                return $data->category_head_updated_datetime ? date('d/m/Y', strtotime($data->category_head_updated_datetime)) : '';
            })
            ->editColumn('issue_doc_datetime', function ($data) {
                return $data->issue_doc_datetime ? date('d/m/Y', strtotime($data->issue_doc_datetime)) : '';
            })
            ->editColumn('sourcing_updated_datetime', function ($data) {
                return $data->sourcing_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_updated_datetime)) : '';
            })
            ->editColumn('sourcing_manger_updated_datetime', function ($data) {
                return $data->sourcing_manger_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_manger_updated_datetime)) : '';
            })
            ->editColumn('accounting_updated_datetime', function ($data) {
                return $data->accounting_updated_datetime ? date('d/m/Y', strtotime($data->accounting_updated_datetime)) : '';
            })
            ->editColumn('cn_updated_datetime', function ($data) {
                return $data->cn_updated_datetime ? date('d/m/Y', strtotime($data->cn_updated_datetime)) : '';
            })
            ->editColumn('finished_updated_datetime', function ($data) {
                return $data->finished_updated_datetime ? date('d/m/Y', strtotime($data->finished_updated_datetime)) : '';
            })

            ->addColumn('action', function ($data) use ($can_delete_document) {
                return $can_delete_document;
            })
            ->addIndexColumn()
            ->make(true);
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to Search Document!');
        // }
    }

    public function sourcing_listing_search_result(Request $request)
    {
        // try{
        $document_no = (!empty($_GET["document_no"])) ? ($_GET["document_no"]) : ('');
        $fromDate = (!empty($_GET["document_from_date"])) ? ($_GET["document_from_date"]) : ('0');
        $fromDate = $fromDate ?? Session::get('old_from_date');
        $toDate = (!empty($_GET["document_to_date"])) ? ($_GET["document_to_date"]) : ('0');
        $toDate = $toDate ?? Session::get('old_to_date');
        $document_type = ($_GET["document_type"]) ? ($_GET["document_type"]) : ('');
        $detail_type = ($_GET["detail_type"]) ? ($_GET["detail_type"]) : ('');
        $document_branch = (!empty($_GET["document_branch"])) ? ($_GET["document_branch"]) : ('0');
        $document_branch = $document_branch ?? Session::get('branch_id');
        $document_status = (!empty($_GET["document_status"])) ? ($_GET["document_status"]) : ('0');
        $document_status = $document_status ?? Session::get('document_status');

        $category = (!empty($_GET["category"])) ? ($_GET["category"]) : ('0');
        $category = $category ?? Session::get('category');
        $result = $this->connection();

        if ($fromDate != "0") {
            Session::put('old_from_date', $fromDate);
            $dateStr = str_replace("/", "-", $fromDate);
            $fromDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('sourcing_documents.document_date', '>=', $fromDate);
        } else {
            Session::put('old_from_date', $fromDate);
        }
        if ($toDate != "0") {
            Session::put('old_to_date', $toDate);
            $dateStr = str_replace("/", "-", $toDate);
            $toDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('sourcing_documents.document_date', '<=', $toDate);
        } else {
            Session::put('old_to_date', $toDate);
        }
        // if ($document_type == 2 && $document_status == '') {
        //     $result = $result->where('documents.document_type', $document_type)->whereIn('document_status', ['1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '13', '14', '15']);
        // }
        if ($document_type == 1) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        if ($document_type == 2) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        if ($document_type == 3) {
            $result = $result->where('sourcing_documents.document_type', $document_type);
        }
        // if ($document_no != "") {
        //     $result = $result->where('document_no', 'ilike', '%' . $document_no . '%');
        // }

        if (($document_no != "")) {

            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }

        if ($document_status != "0") {
            Session::put('document_status', $document_status);

            $result = $result->where('sourcing_documents.document_status', $document_status);
        } else {
            Session::put('document_status', $document_status);
        }

        if ($document_branch != "0") {
            Session::put('branch_id', $document_branch);
            $result = $result->where('sourcing_documents.branch_id', $document_branch);
        } else {
            Session::put('branch_id', $document_branch);
        }
        if ($category != "0") {
            Session::put('category', $category);
            $result = $result->where('sourcing_documents.category_id', $category);
        } else {
            Session::put('category', $category);
        }
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        $result = $result->whereIn('branch_id', $user_branches);
        // dd($user_branches);
        $can_delete_document = Gate::allows('delete-document');

        if ($detail_type == 1) {
            $result = $result->where('document_status', '=', 18)->where('document_type', 1);
        }
        if ($detail_type == 2) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '9', '17', '19', '21'])->where('document_type', 1)->has('products');
        }
        if ($detail_type == 3) {
            $result = $result->whereIn('document_status', ['3','5','20','22'])->where('document_type', 1);
        }
        if ($detail_type == 4) {
            $result = $result->where('document_status', '18')->where('document_type', 2);
        }
        if ($detail_type == 5) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '9', '17', '19', '21'])->where('document_type', 2)->has('products');
        }
        if ($detail_type == 6) {
            $result = $result->whereIn('document_status', ['3', '5','20','22'])->where('document_type', 2);
        }
        if ($detail_type == 7) {
            $result = $result->where('document_status','18')->where('document_type', 3);
        }
        if ($detail_type == 8) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '9', '17', '19', '21'])->where('document_type', 3)->has('products');
        }
        if ($detail_type == 9) {
            $result = $result->whereIn('document_status', ['3','5','20','22'])->where('document_type', 3);
        }
        if ($detail_type == 10) {
            $result = $result->where('document_status', 8);
        }
        if ($detail_type == 11) {
            $result = $result->where('document_status', 10)->where('document_type', 2);
        }
        $result = $result->with('Category', 'Products')->orderBy('updated_at', 'DESC')->get();

        return DataTables::of($result)
            ->editColumn('document_status', function ($data) {
                if($data->document_status == 4){
                return 'Cat.Head Acknowledge';
            }
                return $data->DocumentStatus->document_status_name;
            })
            ->addColumn('check_product', function ($data) {
                if($data->Products()->count()){
                    return 1;
                }else{
                    return 2;
                };
            })
            ->addColumn('check_reject_status', function ($data) {
                if($data->document_status == 3 || $data->document_status == 5 || $data->document_status == 20 || $data->document_status == 22){
                    return 1;
                }else{
                    return 2;
                };
            })
            ->editColumn('category', function ($data) {
                if (isset($data->Category)) {
                    $category = $data->Category->category_name;
                    $sub_category = explode(" ", $category);
                    $category = $sub_category[0];
                    $checkCategory = strpos($category, '/');
                    if ($checkCategory == true) {
                        $sub_category = explode("/", $category);
                        $category = $sub_category[0];
                    }
                    return $category;
                }
                return '';
            })
            ->editColumn('operation_updated_datetime', function ($data) {
                return $data->operation_updated_datetime ? date('d/m/Y', strtotime($data->operation_updated_datetime)) : '';
            })
            ->editColumn('branch_manager_updated_datetime', function ($data) {
                return $data->branch_manager_updated_datetime ? date('d/m/Y', strtotime($data->branch_manager_updated_datetime)) : '';
            })
            ->editColumn('category_head_updated_datetime', function ($data) {
                return $data->category_head_updated_datetime ? date('d/m/Y', strtotime($data->category_head_updated_datetime)) : '';
            })
            ->editColumn('issue_doc_datetime', function ($data) {
                return $data->issue_doc_datetime ? date('d/m/Y', strtotime($data->issue_doc_datetime)) : '';
            })
            ->editColumn('sourcing_updated_datetime', function ($data) {
                return $data->sourcing_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_updated_datetime)) : '';
            })
            ->editColumn('sourcing_manger_updated_datetime', function ($data) {
                return $data->sourcing_manger_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_manger_updated_datetime)) : '';
            })
            ->editColumn('accounting_updated_datetime', function ($data) {
                return $data->accounting_updated_datetime ? date('d/m/Y', strtotime($data->accounting_updated_datetime)) : '';
            })
            ->editColumn('cn_updated_datetime', function ($data) {
                return $data->cn_updated_datetime ? date('d/m/Y', strtotime($data->cn_updated_datetime)) : '';
            })
            ->editColumn('finished_updated_datetime', function ($data) {
                return $data->finished_updated_datetime ? date('d/m/Y', strtotime($data->finished_updated_datetime)) : '';
            })

            ->addColumn('action', function ($data) use ($can_delete_document) {
                return $can_delete_document;
            })
            ->addIndexColumn()
            ->make(true);
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to Search Document!');
        // }
    }

    public static function generate_doc_no($type, $date, $branch_id)
    {
        // try {
            // $type == '1' || '3' ? $prefix = 'SSD' : $prefix = 'SBD';
            if($type == '1' ){
                $prefix = 'SSD';
            }else if($type == '2'){
                $prefix = 'SBD';
            }else{
                $prefix = 'SNA';
            }
            $branch_prefix = Branch::select('branch_short_name')->where('branch_id', $branch_id)->first()->branch_short_name;
            $dateStr = str_replace("/", "-", $date);
            $date = date('Y/m/d H:i:s', strtotime($dateStr));

            $prefix = $prefix . $branch_prefix;
            $last_id = SourcingDocument::select('id', 'document_no')->where('document_type', $type)
            ->whereDate('sourcing_documents.document_date', '=', $date)
            ->latest()->get()->take(1);
            if (isset($last_id[0]) == false) {
                return $doc_no = $prefix . date('ymd-', strtotime($date)) . '0001';
            } else {

                $doc_no = $last_id[0]->document_no;
                $doc_no_arr = explode("-", $doc_no);
                $old_ymd = substr($doc_no_arr[0], -6);
                if ($old_ymd == date('ymd', strtotime($date))) {

                    $last_no = str_pad($doc_no_arr[1] + 1, 4, 0, STR_PAD_LEFT);
                } else {
                    $last_no = '0001';
                }

               return $doc_no = $prefix . date('ymd-', strtotime($date)) . $last_no;

            }
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("logistics_documents.index"))
        //         ->with('error', 'Fail to generate Document No!');
        // }
    }

    public function view_logistics_attach_file($document_id, $attach_type)
    {
        // try {
        $sourcings = SourcingDocument::where('id', $document_id)->first();
        $user_name = Auth::user()->name;
        if ($attach_type == 1) {
            $attach_file_name = 'Operation Attach File';
            $attach_file_type = $sourcings->operation_attach_file;
        } else if ($attach_type == 2) {
            $attach_file_name = 'Merchandising Attach File';
            $attach_file_type = $sourcings->merchandising_attach_file;
        } else if ($attach_type == 4) {
            $attach_file_name = 'Sourcing Attach File';
            $attach_file_type = $sourcings->sourcing_attach_file;
        } else if ($attach_type == 9) {
            $attach_file_name = 'Accounting Attach File';
            $attach_file_type = $sourcings->accounting_attach_file;
        } else if ($attach_type == 5) {
            $attach_file_name = 'CN Attach File';
            $attach_file_type = $sourcings->cn_attach_file;
        } else if ($attach_type == 6) {
            $attach_file_name = 'Accounting DB Attach File';
            $attach_file_type = $sourcings->accounting_db_attach_file;
        }else if ($attach_type == 7) {
            $attach_file_name = 'Operation Attach File';
            $attach_file_type = $sourcings->operation_attach_file_2;
        }else if ($attach_type == 10) {
            $attach_file_name = 'Accounting Attach File';
            $attach_file_type = $sourcings->accounting_attach_file2;
        }

        // Delete File
        $data = File::deleteDirectory(public_path('storage'));
        // Get File form ftp

        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
        {
        $ftp_file = Storage::disk('ftp1')->get($attach_file_type);
        }else{
            try {
                $ftp_file = Storage::disk('ftp3')->get($attach_file_type);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                try {
                    $ftp_file = Storage::disk('ftp1')->get($attach_file_type);
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    $ftp_file = null;
                }
            }
        }

        //Copy to public storage
        Storage::disk('public')->put($attach_file_type, $ftp_file);
        if (substr($attach_file_type, -3) !== 'pdf' || substr($attach_file_type, -3) !== 'PDF') {
            return response()->file(public_path('storage/' . $attach_file_type));
        }
        // if (substr($attach_file_type, -3) == 'pdf' || substr($attach_file_type, -3) == 'PDF') {
        //     return response()->file(public_path('images/attachFile/' . $attach_file_type));
        // }

        $pdf = MPDF::loadView('sourcing_documents.view_document_attach_file', compact('user_name', 'attach_file_name', 'attach_file_type'));
        return $pdf->stream($sourcings->document_no . '_' . $attach_file_name . ".pdf");
        // } catch (\Exception$e) {
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to View Attach File!');
        // }
    }

    public function sourcing_bm_approve(Request $request)
    {
        // try {
            // dd(Session::get('currentURL'));
            // dd($request->all());
        if ($request->sourcing_id == null) {
            return redirect()->route('sourcing_documents.index')
                ->with('error', 'Error');
        }

        $sourcing_id = $request->sourcing_id;
        $document = $this->connection()->where('id', $sourcing_id)->first();
        $request['branch_manager_id'] = Auth::id();
        $request['branch_manager_updated_datetime'] = date('Y-m-d H:i:s');
        $request['document_status'] = 2;
        $document->update($request->all());

        if(Session::get('currentURL') != null)
        {
            return redirect(Session::get('currentURL'))->with('success', 'Branch Manager Checked Successfully');;
        }
        else
        {
            if($document->document_type==1)
            {
                // dd('hi');
                return redirect()->route('sourcing_small')
                ->with('success', 'Branch Manager Checked Successfully');
            }
            elseif($document->document_type==2)
            {
                return redirect()->route('sourcing_big')
                ->with('success', 'Branch Manager Checked Successfully');
            }
            else{
                return redirect()->route('sourcing_need_accessory')
                ->with('success', 'Branch Manager Checked Successfully');
            }
        }

                    // redirect_page($document_type,'Branch Manager Checked Successfully');
    }


    public function sourcing_bm_reject(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return redirect()->route('sourcing_documents.index')
                    ->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['branch_manager_id'] = Auth::id();
            $request['branch_manager_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 3;
            $request['reject_remark'] = $request->reason;
            $request['category_id'] = $document->category_id;
            // dd($request->all());
            $document->update($request->all());
            // dd($document);
            $message = "Your Document " . $document->document_no . " is rejected ";
            // reject noti
            $user = User::where('id',$document->operation_id)->get();
            $type = 1;
            Notification::send($user, new DocumentNotification($message, $sourcing_id, $type));
            // end
            // dd("hi");
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Branch Manager Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
            }


            // redirect_page($document_type,'Branch Manager Rejected Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Branch Manager Rejected Successfully');
    }
    public function sourcing_mm_reject(Request $request)
    {
        // dd($request->all());
        // try {
            if ($request->sourcing_id == null) {
                return back()->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = SourcingDocument::whereId($sourcing_id)->first();

            $request['log_manager_reject_remark_id'] = Auth::id();

            $request['log_manager_reject_remark_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 20;
            $request['reject_remark'] = $request->reason;
            $request['category_id'] = $document->category_id;
            // dd($request->all());
            $document->update($request->all());
            // reject noti

            $message = "Your Document " . $document->document_no . " is rejected ";
            $user = User::where('id',$document->operation_id)->get();
            $type = 1;
            Notification::send($user, new DocumentNotification($message, $sourcing_id, $type));
            // end
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Sourcing Manager Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Sourcing Manager Rejected Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Sourcing Manager Rejected Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Sourcing Manager Rejected Successfully');
                }
            }

            // redirect_page($document_type,'Sourcing Manager Rejected Successfully');
    }
    public function mm_reject(Request $request)
    {
        // dd($request->all());
        // try {
            if ($request->sourcing_id == null) {
                return back()->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = SourcingDocument::whereId($sourcing_id)->first();
            // dd($document);
            $request['sourcing_reject_remark_id'] = Auth::id();

            $request['sourcing_reject_remark_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 22;
            $request['reject_remark'] = $request->reason;
            $request['category_id'] = $document->category_id;
            // dd($request->all());

            $document->update($request->all());
            // reject noti
            $message = "Your Document " . $document->document_no . " is rejected ";
            $user = User::where('id',$document->operation_id)->get();
            $type = 1;
            Notification::send($user, new DocumentNotification($message, $sourcing_id, $type));
            // end
            // dd($document);
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Sourcing Staff Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Sourcing Staff Rejected Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Sourcing Staff Rejected Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Sourcing Staff Rejected Successfully');
                }
            }

            // redirect_page($document_type,'Sourcing Manager Rejected Successfully');
    }
    public function sourcing_ch_approve(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return redirect()->route('sourcing_documents.index')
                    ->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['category_head_id'] = Auth::id();
            $request['category_head_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 4;
            $product = SourcingProduct::where('document_id',$document->id)->first();
            $mer = SourcingProductImage::where('sourcing_product_id', $product->id)->pluck('mer_percentage')->toArray();
            $request['category_id'] = $document->category_id;
            $document->update($request->all());
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Category Head acknowledge Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Category Head acknowledge Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Category Head acknowledge Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Category Head acknowledge Successfully');
                }
            }

            // redirect_page($document_type,'Category Head acknowledge Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Cat.Head acknowledge Successfully');

    }
    public function sourcing_ch_reject(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }
            $document = $this->connection()->where('id', $request->sourcing_id)->first();

            $request['ch_reject_remark_id'] = Auth::id();
            $request['ch_reject_remark_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 5;
            $request['reject_remark'] = $request->reason;
            $request['category_id'] = $document->category_id;

            $document->update($request->all());
            // reject noti
            $message = "Your Document " . $document->document_no . " is rejected ";
            $user = User::where('id',$document->operation_id)->get();
            $type = 1;
            Notification::send($user, new DocumentNotification($message, $request->sourcing_id, $type));
            // end
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Category Head Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Category Head Rejected Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Category Head Rejected Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Category Head Rejected Successfully');
                }
            }

            // redirect_page($document_type,'Category Head Rejected Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Category Head Rejected Successfully');

    }
    public function sourcing_account_issue_approve(Request $request)
    {
        // try {
            $sourcings = SourcingDocument::where('id', $request->sourcing_id)->first();
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }
            if ($sourcings->document_type == 2) {
                $request->validate([
                    'issue_no' => 'required',
                ]);
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['accounting_id'] = Auth::id();
            $request['accounting_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 19;
            $request['category_id'] = $document->category_id;

            $document->update($request->all());
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Accounting Issued Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Accounting Issued Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Accounting Issued Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Accounting Issued Successfully');
                }
            }
            // redirect_page($document_type,'Accounting Issued Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Accounting Issued Successfully');
    }
    // Issued
    public function sourcing_issued_approve(Request $request){
        // dd($request->sourcing_id);
        if ($request->sourcing_id == null) {
            return back()
                ->with('error', 'Error');
        }
        $sourcing_id = $request->sourcing_id;
        $document = $this->connection()->where('id', $sourcing_id)->first();
        $request['account_issued_id'] = Auth::id();
        $request['issue_doc_datetime'] = date('Y-m-d H:i:s');
       if($document->document_status == 21 || $document->document_status == 17 || $document->document_status == 9){
            $request['document_status'] = $document->document_status;
       }else{
            $request['document_status'] = 19;
       }
       $request['category_id'] = $document->category_id;
        $document->update($request->all());
        // $document->update(['account_issued_id' =>  Auth::id() , 'document_status' => 19 , 'issue_doc_datetime' =>date('Y-m-d H:i:s')]);
        // dd($document);
        if(Session::get('currentURL') != null)
        {
            return redirect(Session::get('currentURL'))->with('success', 'Accounting Issued Successfully');;
        }
        else
        {
            if($document->document_type==1)
            {
                // dd('hi');
                return redirect()->route('sourcing_small')
                ->with('success', 'Accounting Issued Successfully');
            }
            elseif($document->document_type==2)
            {
                return redirect()->route('sourcing_big')
                ->with('success', 'Accounting Issued Successfully');
            }
            else{
                return redirect()->route('sourcing_need_accessory')
                ->with('success', 'Accounting Issued Successfully');
            }
        }
    }
    // end
    public function sourcing_log_approve(Request $request)
    {
        // dd($request->all());
        // try {
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['sourcing_id'] = Auth::id();
            $request['sourcing_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 21;
            $request['category_id'] = $document->category_id;
            $request['document_no'] = $document->document_no;
            // dd($request->all());
            $document->update($request->all());
            // dd($document);

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Sourcing Confirmed Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Sourcing Confirmed Successfully');
                }
                elseif(($document->document_type==2))
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Sourcing Confirmed Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Sourcing Confirmed Successfully');
                }
            }

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Soucing Approved Successfully');
    }

    public function sourcing_log_mm_approve(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }

            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['sourcing_manger_id'] = Auth::id();
            $request['sourcing_manger_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 17;
            // $request['category_id'] = $document->category_id;
            // dd($request->all());
            $document->update($request->all());
            // dd($document);

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Sourcing Manager Approved Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Soucing Manager Approved Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Soucing Manager Approved Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Soucing Manager Approved Successfully');
                }
            }

            // redirect_page($document->document_type,'Sourcing Manager Approved Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Sourcing Manager Approved Successfully');

    }
    public function sourcing_finished_document(Request $request)
    {
        // try {
            // dd($request);
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }
            if($request->file == null){
                request()->validate([
                    'accounting_attach_file2' => 'required',
                ]);
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['finished_id'] = Auth::id();
            $request['finished_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 18;
            $request['category_id'] = $document->category_id;
            if($document->issue_doc_datetime == ''){
                $request['issue_doc_datetime'] =  date('Y-m-d H:i:s');
                $request['account_issued_id'] = Auth::id();
            }else {
                $request['issue_doc_datetime'] = $document->issue_doc_datetime;
            }

            $document->update($request->all());
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Document Finished Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Document Finished Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Document Finished Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Document Finished Successfully');
                }
            }

            // redirect_page($document_type,'Document Finished Successfully');

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Document Finished Successfully');

    }
    public function sourcing_cn_complete_document(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return back()
                    ->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $request['cn_id'] = Auth::id();
            $request['cn_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 9;
            // $request['category_id'] = $document->category_id;
            // dd($request->all());
            if($request->cnfile == ''){
                $document->update($request->all());

            }
            else{
                $document->update($request->except(['cn_attach_file']));
            }


            // redirect_page($document->document_type,'CN complete Successfully');

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'CN complete Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'CN complete Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'CN complete Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'CN complete Successfully');
                }
            }
    }
    public function change_to_previous_status(Request $request)
    {
        // try {
            if ($request->sourcing_id == null) {
                return redirect()->route('sourcing_documents.index')
                    ->with('error', 'Error');
            }
            $sourcing_id = $request->sourcing_id;
            $document = $this->connection()->where('id', $sourcing_id)->first();
            $document_status = $document->document_status;
            $message = "Your Document " . $document->document_no . " is changed";
            if (Gate::allows('update-document-bm-complete') && $document_status == 2) {
                $request['branch_manager_id'] = null;
                $request['branch_manager_updated_datetime'] = null;
                $request['document_status'] = 1;
                $user = User::where('id', $document->branch_manager_id)->first();
            }
            if (Gate::allows('update-document-bm-complete') && $document_status == 3) {
                $request['branch_manager_id'] = null;
                $request['branch_manager_updated_datetime'] = null;
                $request['document_status'] = 1;
                $user = User::where('id', $document->branch_manager_id)->first();
            }
            if (Gate::allows('update-document-ch-complete') && ($document_status == 4 || $document_status == 5)) {
                $request['category_head_id'] = null;
                $request['category_head_updated_datetime'] = null;
                $request['document_status'] = 2;
                $user = User::where('id', $document->category_head_id)->first();
            }
            if (Gate::allows('update-document-logmm-complete') && $document_status == 21) {
                $request['sourcing_id'] = null;
                $request['sourcing_updated_datetime'] = null;
                $request['document_status'] = 4;
                $user = User::where('id', $document->sourcing_id)->first();
            }
            if (Gate::allows('update-document-acc-complete') && $document_status == 19) {
                $request['accounting_id'] = null;
                $request['accounting_updated_datetime'] = null;
                $request['document_status'] = 2;
                $user = User::where('id', $document->accounting_id)->first();
            }
            // if (Gate::allows('update-document-logmm-complete') && $document_status == 17) {
            //     $request['sourcing_manger_id'] = null;
            //     $request['sourcing_manger_updated_datetime'] = null;
            //     $request['document_status'] = 21;
            //     $user = User::where('id', $document->sourcing_manger_id)->first();
            // }
            if (Gate::allows('arrive-to-dc') && $document_status == 14) {
                $request['arrive_to_dc_id'] = null;
                $request['arrive_to_dc_updated_datetime'] = null;
                $request['document_status'] = 6;
                $user = User::where('id', $document->arrive_to_dc_id)->first();
            }
            if (Gate::allows('update-document-mm-complete') && $document_status == 7) {
                $request['merchandising_manager_id'] = null;
                $request['merchandising_manager_updated_datetime'] = null;
                $request['document_status'] = 4;
                $user = User::where('id', $document->merchandising_manager_id)->first();
            }
            if (Gate::allows('update-document-bm-complete') && $document_status == 8) {
                $request['operation_rg_out_id'] = null;
                $request['operation_rg_out_updated_datetime'] = null;
                $request['document_status'] = 6;
                $user = User::where('id', $document->operation_rg_out_id)->first();
            }
            if (Gate::allows('update-document-cn-complete') && $document_status == 9) {
                $request['accounting_cn_id'] = null;
                $request['accounting_cn_updated_datetime'] = null;
                $request['document_status'] = 8;
                $user = User::where('id', $document->accounting_cn_id)->first();
            }
            if (Gate::allows('update-document-bm-complete') && $document_status == 10) {
                $request['operation_rg_in_id'] = null;
                $request['operation_rg_in_updated_datetime'] = null;
                $request['document_status'] = 9;
                $user = User::where('id', $document->operation_rg_in_id)->first();
            }
            if (Gate::allows('update-document-db-complete') && $document_status == 11) {
                $request['accounting_db_id'] = null;
                $request['accounting_db_updated_datetime'] = null;
                $request['document_status'] = 10;
                $user = User::where('id', $document->accounting_db_id)->first();
            }
            $request['category_id'] = $document->category_id;
            $document->update($request->all());

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Change to Previous Level Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('sourcing_small')
                    ->with('success', 'Change to Previous Level Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('sourcing_big')
                    ->with('success', 'Change to Previous Level Successfully');
                }
                else{
                    return redirect()->route('sourcing_need_accessory')
                    ->with('success', 'Change to Previous Level Successfully');
                }
            }

            // return redirect()->route('sourcing_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Change to Previous Level Successfully');
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to DB Complete!');
        // }
    }

    public function change_to_sourcing_approved_status(Request $request){
        if ($request->sourcing_id == null) {
            return redirect()->route('sourcing_documents.index')
                ->with('error', 'Error');
        }
        $sourcing_id = $request->sourcing_id;
        $document = $this->connection()->where('id', $sourcing_id)->first();
        $document_status = $document->document_status;
        $message = "Your Document " . $document->document_no . " is changed";

        if (Gate::allows('update-document-logmm-complete') && $document_status == 17) {
            $request['sourcing_manger_id'] = null;
            $request['sourcing_manger_updated_datetime'] = null;
            $request['document_status'] = 21;
            $user = User::where('id', $document->sourcing_manger_id)->first();
        }
        if (Gate::allows('update-document-cn-complete') && $document_status == 9) {
            $request['sourcing_manger_id'] = null;
            $request['sourcing_manger_updated_datetime'] = null;

            $request['cn_id'] = null;
            $request['cn_updated_datetime'] = null;
            $request['document_status'] = 21;
            $user = User::where('id', $document->accounting_cn_id)->first();
        }
        $document->update($request->all());

        // $sourcing_products = $document->sourcingproduct;
        // // dd($sourcing_products);
        // foreach($sourcing_products as $sourcing_product){
        //     $sourcing_product_images = $sourcing_product->sourcingproductimage;

        //     foreach($sourcing_product_images as $sourcing_product_image){
        //         // dd($sourcing_product_image);
        //         $sourcing_product_image->update([
        //             "sourcing_percentage" => null
        //         ]);
        //     }
        // }

        if(Session::get('currentURL') != null)
        {
            return redirect(Session::get('currentURL'))->with('success', 'Change to Previous Level Successfully');;
        }
        else
        {
            if($document->document_type==1)
            {
                // dd('hi');
                return redirect()->route('sourcing_small')
                ->with('success', 'Change to Previous Level Successfully');
            }
            elseif($document->document_type==2)
            {
                return redirect()->route('sourcing_big')
                ->with('success', 'Change to Previous Level Successfully');
            }
            else{
                return redirect()->route('sourcing_need_accessory')
                ->with('success', 'Change to Previous Level Successfully');
            }
        }
    }

    public function sourcing_destory($document_id)
    {
        // dd('hi');
        // try {
            $sourcing = SourcingDocument::where('id', $document_id)->first();
            $sourcing->delete();
            return response()->json([
                'success' => 'Document deleted successfully!',
            ]);
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to delete Document!');
        // }
    }
    public function check_image(Request $request,$id)
    {
        $check_products = SourcingProduct::where('document_id', $id)->get();
        foreach($check_products as $check_product){
            $checkImage = SourcingProductImage::where('sourcing_product_id', $check_product->id)->first();
            if(!$checkImage){
                return response()->json([ 'error' => 'checkImage'], 200);
            }
        }
        return response()->json([ 'success' => 'successfully'], 200);
    }

    public function check_sourcing_percentage(Request $request,$id)
    {
        // dd($id, request()->route);
        $check_products = SourcingProduct::where('document_id', $id)->get();
        $sourcing_document = SourcingDocument::where('id',$id)->first();
        if($sourcing_document->document_type == 1){
            foreach($check_products as $check_product){
                if(!$check_product->damage_percentage){
                    return response()->json([ 'error' => 'checkPercentage'], 200);
                }
            }
        }

        return response()->json([ 'success' => 'successfully'], 200);
    }


    public function check_damage_type(Request $request,$id)
    {
        $check_products = SourcingProduct::where('document_id', $id)->get();
        $sourcing_document = SourcingDocument::where('id',$id)->first();
            foreach($check_products as $check_product){
                if(!$check_product->damage_remark_types){
                  return   response()->json([ 'error' => 'checkType'], 200);
                }
                if($check_product->damage_remark_types == 3)
                {
                    if(!$check_product->damage_remark_reasons){
                        return   response()->json([ 'reasons'], 200);
                    }
                }

                if($check_product->currency_type == null){
                    return response()->json('currencyType', 200);
                }
                if($check_product->baht_value == '' || $check_product->baht_value == 0){
                    return response()->json('baht', 200);
                }
                if($check_product->kyat_value == "NAN" || $check_product->kyat_value == 0){
                    return response()->json('kyat', 200);
                }
            }
            // dd($check_product);



        return response()->json([ 'success' => 'successfully'], 200);
    }

    public function sourcing_listing(Request $request)
    {
        // dd($request->detail_type);
        // try {
            $branches = BranchUser::where('user_id', auth()->user()->id)->get();
            $document_status = DocumentStatus::get();
            if ($request->detail_type == 1) {
                $document_type_title = __('home.finish_log_small_doc');
                $document_type = '1';
                $document_status = DocumentStatus::where('document_status', 18)->get();
            } else if ($request->detail_type == 2) {
                $document_type_title = __('home.sourcing_small_pending');
                $document_status = DocumentStatus::get();
                $document_type = '1';
            } else if ($request->detail_type == 3) {
                $document_type_title = __('home.reject_log_small_doc');
                $document_status = DocumentStatus::get();
                $document_type = '1';
            } else if ($request->detail_type == 4) {
                $document_type_title = __('home.finish_sou_big_doc');
                $document_status = DocumentStatus::where('document_status', 18)->get();
                $document_type = '2';
            } else if ($request->detail_type == 5) {
                $document_type_title = __('home.sourcing_big_pending');
                $document_status = DocumentStatus::get();
                $document_type = '2';
            } else if ($request->detail_type == 6) {
                $document_type_title = __('home.sourcing_big_reject');
                $document_status = DocumentStatus::get();
                $document_type = '2';
            } else if ($request->detail_type == 7) {
                $document_type_title = __('home.finish_accessary_doc');
                $document_status = DocumentStatus::get();
                $document_type = '3';
            } else if ($request->detail_type == 8) {
                $document_type_title = __('home.sourcing_accessay_pending');
                $document_status = DocumentStatus::get();
                $document_type = '3';
            } else if ($request->detail_type == 9) {
                $document_type_title = __('home.sourcing_accessary_reject');
                $document_status = DocumentStatus::get();
                $document_type = '3';

            }
            $detail_type = isset($request->detail_type) ? $request->detail_type : '';

            $categories = Category::get();

            $old_from_date = Session::get('old_from_date') ?? '';
            $old_to_date = Session::get('old_to_date') ?? '';
            $old_branch_id = Session::get('branch_id') ?? '';
            $old_document_status = Session::get('document_status') ?? '';
            $old_category_id = Session::get('category') ?? '';

            return view('sourcing_documents.sourcing_listing', compact(
                'branches',
                'document_status',
                'categories',
                'document_type_title',
                'detail_type',
                'document_type',
                'old_from_date',
                'old_to_date',
                'old_branch_id',
                'old_document_status',
                'old_category_id'
            ));
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to load Data!');
        // }
    }

    public function move_to_other_doc(Request $request)
    {
        $selectedIDs = $request->selectedIDs;

        $document_id = $request->documentID;
        $ref_no = $request->ref_no;
        // dd($product_code);
        //check reference more 2 time

        //find original_doc document
        $old_sourcing_document = SourcingDocument::where('id',$document_id)->first();
        // dd($old_sourcing_document);
        $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
        // dd($check_reference_no);
        if($check_reference_no < 2){

           //find products with selectdIDs
            $old_doc_no = $old_sourcing_document->document_no;
            $document_type = $request->type;
            $document_date = now();
            $new_operation_file_name =null;
            if($old_sourcing_document->operation_attach_file){
                $old_data =  explode(".",$old_sourcing_document->operation_attach_file);
                $new_operation_file_name =  $old_data[0] . '_' . time() . '_update.'. $old_data[1];
                if (strtotime($old_sourcing_document->created_at->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp1')->copy($old_sourcing_document->operation_attach_file, $new_operation_file_name);
                }else{

                    Storage::disk('ftp3')->copy($old_sourcing_document->operation_attach_file, $new_operation_file_name);
                }
            }
            $new_sourcing_file_name =null;
            if($old_sourcing_document->sourcing_attach_file){
                $old_data =  explode(".",$old_sourcing_document->sourcing_attach_file);
                $new_sourcing_file_name =  $old_data[0] . '_' . time() . '_update.'. $old_data[1];
                if (strtotime($old_sourcing_document->created_at->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp1')->copy($old_sourcing_document->sourcing_attach_file, $new_sourcing_file_name);
                }else{
                    Storage::disk('ftp3')->copy($old_sourcing_document->sourcing_attach_file, $new_sourcing_file_name);
                }

            }
            $sourcing_document['document_no'] = $this->generate_doc_no($document_type, $document_date, $old_sourcing_document->branch_id);
            $sourcing_document['operation_id'] = $old_sourcing_document->operation_id;
            $sourcing_document['branch_id'] = $old_sourcing_document->branch_id;
            $sourcing_document['category_id'] = (int) $old_sourcing_document->category_id;
            $sourcing_document['document_type'] = (int) $document_type;
            $sourcing_document['car_no'] =  $old_sourcing_document->car_no;

            $sourcing_document['document_date'] = $document_date;

            $sourcing_document['operation_attach_file'] = $new_operation_file_name;
            $sourcing_document['operation_updated_datetime'] = $old_sourcing_document->operation_updated_datetime;
            $sourcing_document['operation_remark'] = $old_sourcing_document->operation_remark;

            $sourcing_document['branch_manager_id'] = $old_sourcing_document->branch_manager_id;
            $sourcing_document['branch_manager_updated_datetime'] = $old_sourcing_document->branch_manager_updated_datetime;
            $sourcing_document['branch_manager_remark'] = $old_sourcing_document->branch_manager_remark;

            if($document_type == 1 ){
                $sourcing_document['document_status'] = 2;
                $sourcing_document['category_head_id'] = null;
                $sourcing_document['category_head_updated_datetime'] = null;
                $sourcing_document['category_head_remark'] = null;
            }else{
                $sourcing_document['document_status'] = $old_sourcing_document->document_status;
                $sourcing_document['category_head_id'] = $old_sourcing_document->category_head_id;
                $sourcing_document['category_head_updated_datetime'] = $old_sourcing_document->category_head_updated_datetime;
                $sourcing_document['category_head_remark'] = $old_sourcing_document->category_head_remark;
            }

            $sourcing_document['sourcing_attach_file'] = $new_sourcing_file_name;

            //recreate new document with new document id
            $new_sourcings_document = SourcingDocument::create($sourcing_document);

            /////send noti////
            $operation_id = $old_sourcing_document->operation_id;
            $branch_manager_id = $old_sourcing_document->branch_manager_id;
            $category_head_id = $old_sourcing_document->category_head_id;
            $message = "Your Document " . $old_sourcing_document->document_no . " is changed to " . $new_sourcings_document->document_no;
            // $aa = new DocumentNotification($message, (int)$document_id);
            $users = User::whereIn('id',[$operation_id,$branch_manager_id,$category_head_id])->get();

            foreach($users as $user){
                $type = 1;
                Notification::send($user, new DocumentNotification($message, $new_sourcings_document->id, $type));
            }

            //create reference no
            $reference_doc['new_reference_no'] = $new_sourcings_document->document_no;
            $reference_doc['old_reference_no'] = $old_doc_no;
            $reference_doc = ReferenceDocument::create($reference_doc);

            //update products
            // $sourcing_products = SourcingProduct::where('document_id', $old_sourcing_document->id)->get();
            foreach($selectedIDs as $product_id){
                $sourcing_product = SourcingProduct::where('id', $product_id)->first();
                    $update_product['document_id'] = $new_sourcings_document->id;
                    $sourcing_product->update($update_product);
                $sourcing_product_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_product->id])->update(['doc_id'=>$new_sourcings_document->id]);
            }
        }
        else{
            return response()->json([ 'error' => 'more_two_times'], 200);

        }

        return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_sourcings_document->document_no], 200);
    }

    public function sourcing_download_excel_file($document_id)
    {
        $sourcing = SourcingDocument::where('id', $document_id)->first();
        $attach_file_type = $sourcing->excel_attach_file;
        // Delete File
        $data = File::deleteDirectory(public_path('storage'));
        // Get File form ftp
        // dd($attach_file_type);
        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
        {
            $ftp_file = Storage::disk('ftp1')->get($attach_file_type);
        }else{
            try {
                $ftp_file = Storage::disk('ftp3')->get($attach_file_type);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                try {
                    $ftp_file = Storage::disk('ftp1')->get($attach_file_type);
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    $ftp_file = null;
                }
            }
        }

        // dd($ftp_file);
        //Copy to public storage
        Storage::disk('public')->put($attach_file_type, $ftp_file);

            $file="storage/$attach_file_type";
            return response()->download($file);
    }

    // get isuue no
    public function get_issue_no($issue_no,$branch_code){

        // $branch_id = Branch::where('branch_code',$branch_code)->first()->branch_id;
        $db_ext = DB::connection('pgsql2');
        $data = $db_ext->select("select brch.branch_id, brch.branch_code, isshd.icdocuno
                        from inventory.inv_issuestockhd isshd
                        left join master_data.master_branch brch on brch.branch_code = isshd.brchcode
                        where isshd.statusflag = 'A' and isshd.brchcode = '$branch_code' and isshd.icdocuno = '$issue_no'
                        ");
        // dd($data);
        if($data){
            $sourcing_doc = SourcingDocument::where('branch_id' , $data[0]->branch_id)
                                            ->where('issue_doc_no' , $issue_no)->pluck('document_no')->toArray();
           if($sourcing_doc){
                $data = ['duplicate', $sourcing_doc];
                return response()->json($data , 200);
           }else{
                return response()->json(['data' => $data[0]] , 200);
           }

        }else {
            return response()->json(null ,200);
        }
    }

    public function sourcing_detail_listing(Request $request)
    {
        // dd( LogisticsDocument::where('document_status', 16)->get());

            $result = SourcingDocument::query();
             $document_status     = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12,13])->orderBy('sorting_id')->get();
            $userBranches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $branches = BranchUser::where('user_id', auth()->user()->id)->get();
            // dd(Gate::allows('my-document-sourcing'));
            // dd(Gate::allows('my-document-bm'),Gate::allows('my-document-ch'));
            if (Gate::allows('my-document-operation') || Gate::allows('my-document-rgin') || Gate::allows('my-document-rgout')) {

                $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1)
                    ->where('operation_id', auth()->user()->id);
            } else if (Gate::allows('my-document-bm')) {

                $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1);
            } else if (Gate::allows('my-document-ch')) {
                // dd("jjjk");
                $result = $result->where('document_status', 2);
            } else if (Gate::allows('my-document-mm')) {
                $result = $result->where('document_status', 21);
            } else if(Gate::allows(('my-document-sourcing'))) {
                $result = $result->whereIn('document_status', [4,17]);
            } else if (Gate::allows('my-document-account-cn')) {
                $result = $result->whereIn('document_status', [9]);
            }

            $documents = $result->latest()->paginate(10);
            $categories = Category::get();


            return view('sourcing_documents.sourcing_detail_listing', compact(
                'branches',
                'document_status',
                'categories',
                'documents'
            ));

    }
    public function new_search_result_detail(Request $request)
    {
        // dd($request->all());

        $document_no            = $request->document_no;
        $from_date              = $request->document_from_date;
        $to_date                = $request->document_to_date;
        $branch_id              = $request->branch_id;
        $document_status        = $request->document_status;
        $category_id            = $request->category;
        $next_step              = $request->next_step;
        $result                 = SourcingDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();

        Session::put('old_from_date', $from_date) ;
        Session::put('branch_id', $branch_id) ;
        Session::put('old_to_date', $to_date) ;
        Session::put('branch_id', $branch_id) ;
        Session::put('document_status', $document_status) ;
        Session::put('category', $category_id) ;
        Session::put('next_step', $next_step) ;

        $userBranches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        if (Gate::allows('my-document-operation') || Gate::allows('my-document-rgin') || Gate::allows('my-document-rgout')) {

            $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1)
                ->where('operation_id', auth()->user()->id);
        } else if (Gate::allows('my-document-bm')) {

            $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1);
        } else if (Gate::allows('my-document-ch')) {
            $result = $result->where('document_status', 2);
        } else if (Gate::allows('my-document-mm')) {
            $result = $result->where('document_status', 21);
        }
        else if(Gate::allows(('my-document-sourcing'))) {
            $result = $result->whereIn('document_status', [4,17]);
        } else if (Gate::allows('my-document-account-cn')) {
            $result = $result->whereIn('document_status', [9]);
        }
        if(!empty($branch_id))
        {
            $result = $result->where('branch_id', $branch_id);
        }
        // if (!empty($document_no)) {
        //     $result = $result->where('document_no', 'like', '%'.$document_no.'%');
        // }
        if (!empty($document_no)) {

            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        if (!empty($from_date)) {
            $result = $result->where('created_at', 'like', '%'.$from_date.'%');
        }
        if (!empty($to_date)) {
            $result = $result->where('updated_at', 'like', '%'.$to_date.'%');
        }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id', $branch_id);
        }

        if (!empty($document_status)) {
            if($document_status[0]!=null || $document_status[0]!=0)
            {

                $result = $result->whereIn('document_status', $document_status);
            }

        }
        if (!empty($next_step)) {
            $result = $result->where('document_status', $next_step);
        }

        if (!empty($category_id)) {
            $result = $result->where('category_id', 'like', '%'.$category_id.'%');
        }

        $documents = $result->whereIn('document_type',[1,2,3])->latest()->paginate(10);
        // dd($documents);

         $document_status      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12,13])->orderBy('sorting_id')->get();

        return view('sourcing_documents.sourcing_detail_listing', compact(
            'branches',
            'document_status',
            'categories',
            'documents'
        ));

    }

    public function pending_document_export($document_type, $detail_type)
    {
        try {
            $today = date('Y-m-d');
            if($document_type == 1 && $detail_type == 10)
            {
                $status = 'SourcingSmallDocument-Export-';
            }
            elseif($document_type == 1 && $detail_type == 2)
            {
                $status = 'PendingSourcingSmallDocument-Export-';
            }
            elseif($document_type == 1 && $detail_type == 3)
            {
                $status = 'SourcingSmallRejectDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 11)
            {
                $status = 'SourcingBigDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 5)
            {
                $status = 'SourcingBigPendingDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 6)
            {
                $status = 'SourcingBigRejectDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 12)
            {
                $status = 'SourcingNeedAccessoryDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 8)
            {
                $status = 'SourcingNeedAccessoryPendingDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 9)
            {
                $status = 'SourcingNeedAccessoryRejectDocument-Export-';
            }
            return Excel::download(new SourcingPendingExport($document_type, $detail_type), $status . $today . '.xlsx');
        } catch (\Exception $e) {
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to Excel Export!');
        }
    }

    public function damage_rp(Request $request,$sourcing_type){


        // dd($request->document_search);
        // dd($request->all());
        // dd(Route::currentRouteName());
        if($sourcing_type == 'big'){
            $document_type = 2;
        }else if($sourcing_type == 'small'){
            $document_type = 1;
        }

        $request->type = $request->type ?? $document_type;
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.sourcing_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.sourcing_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.sourcing_accessary');
            // $document_status = DocumentStatus::get();
        }

        // $document_type          = isset($request->type) ? $request->type : '';

        $document_no            = $request->document_no;
        $document_from_date     = $request->document_from_date ?? Carbon::now()->firstOfMonth()->format("Y-m-d");
        $document_to_date       = $request->document_to_date ?? Carbon::now()->format("Y-m-d");
        $branch_id              = $request->branch_id;
        $document_status        = $request->document_status ?? [4];
        $category_id            = $request->category_id;
        $next_step              = $request->next_step;
        $brand                  = $request->brand;
        $result                 = SourcingDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12])->orderBy('sorting_id')->get();


        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);
        Session::put('brand', $brand);

        // dd($document_status);
        if (!empty($document_no)) {
            // dd(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'));
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        // if (!empty($document_from_date)) {
        //     $result = $result->where('created_at', 'like', '%'.$document_from_date.'%');
        // }
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }
        if (!empty($document_from_date) || !empty($document_to_date)) {
            // $result = $result->where('created_at', $document_from_date);
            if($document_from_date === $document_to_date)
            {
                $result = $result->whereDate('created_at', $document_from_date);
            }
            else
            {
                if($document_from_date)
                {
                    $from_date = Carbon::parse($document_from_date);
                }
                if($document_to_date)
                {
                    $to_date = Carbon::parse($document_to_date)->endOfDay();;
                }
                $result = $result->whereBetween('created_at', [$document_from_date , $document_to_date]);
            }
        }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id',$branch_id);
        }else{
            $user = Auth::guard()->user();
            $branch_ids =$user->user_branches->pluck('branch_id');
            $result = $result->whereIn("branch_id",$branch_ids);
        }


        if (!empty($document_status)) {
            if($document_status[0]!=null || $document_status[0]!=0)
            {

                $result = $result->whereIn('document_status', $document_status);
            }
        }
        if (!empty($next_step)) {
            $result = $result->where('document_status', $next_step);
        }

        if (!empty($category_id)) {
            $result = $result->where('category_id', $category_id);
        }
        if (!empty($brand)) {
        //    dd($brand);
           $product = SourcingProduct::whereIn('product_brand_name',$brand)->pluck('document_id')->toArray();
            $result = $result->whereIn('id', $product);
            // dd($result->get());
        }

        if($request->document_search == 'Search' || $request->document_search == null){
            $sourcing_documents = $result->where('document_type',$document_type)->latest()->paginate(10);
            $sourcing_documents->appends($request->all());

            return view('sourcing_documents.damage_rp', compact(
                'branches',
                // 'document_status',
                'document_type_title',
                'document_type',
                'categories',
                'document_from_date',
                'document_to_date',
                'branch_id',
                'document_status',
                'category_id',
                'sourcing_documents',
                'document_statuses',
                'sourcing_type'
            ));
        }else if($request->document_search == 'Export'){
            $sourcing_documents = $result->where('document_type',$document_type)->latest()->get();

            try{
                $response = Excel::download(new SourcingsDamageRpBigExport($sourcing_documents), "SourcingDamageReportBig".Carbon::now()->format('Y-m-d').".xlsx");
            }catch(Throwable $err){
                return redirect()->back()->with("error","There is an error in exporting Excel Sheet");
            }

            return $response;
        }

    }

    public function image_ready(Request $request){

        // dd($request->document_search);
        // dd($request->all());
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.sourcing_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.sourcing_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.sourcing_accessary');
            // $document_status = DocumentStatus::get();
        }

        $document_type          = isset($request->type) ? $request->type : '';

        $document_no            = $request->document_no;
        $document_from_date     = $request->document_from_date ?? Carbon::now()->firstOfMonth()->format("Y-m-d");
        $document_to_date       = $request->document_to_date ?? Carbon::now()->format("Y-m-d");
        $branch_id              = $request->branch_id;
        $document_status        = $request->document_status ?? [4];
        $category_id            = $request->category_id;
        $next_step              = $request->next_step;
        $brand                  = $request->brand;
        $result                 = SourcingDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,5,6,7,8,9,10,11,12])->orderBy('sorting_id')->get();


        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);
        Session::put('brand', $brand);

        // dd($document_status);
        if (!empty($document_no)) {
            // dd(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'));
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            }
            else
            {
                $document_id = SourcingProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        // if (!empty($document_from_date)) {
        //     $result = $result->where('created_at', 'like', '%'.$document_from_date.'%');
        // }
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }
        if (!empty($document_from_date) || !empty($document_to_date)) {
            // $result = $result->where('created_at', $document_from_date);
            if($document_from_date === $document_to_date)
            {
                $result = $result->whereDate('created_at', $document_from_date);
            }
            else
            {
                if($document_from_date)
                {
                    $from_date = Carbon::parse($document_from_date);
                }
                if($document_to_date)
                {
                    $to_date = Carbon::parse($document_to_date)->endOfDay();;
                }
                $result = $result->whereBetween('created_at', [$document_from_date , $document_to_date]);
            }
        }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id',$branch_id);
        }else{
            $user = Auth::guard()->user();
            $branch_ids =$user->user_branches->pluck('branch_id');
            $result = $result->whereIn("branch_id",$branch_ids);
        }


        if (!empty($document_status)) {
            if($document_status[0]!=null || $document_status[0]!=0)
            {

                $result = $result->whereIn('document_status', $document_status);
            }
        }
        if (!empty($next_step)) {
            $result = $result->where('document_status', $next_step);
        }

        if (!empty($category_id)) {
            $result = $result->where('category_id', $category_id);
        }
        if (!empty($brand)) {
        //    dd($brand);
           $product = SourcingProduct::whereIn('product_brand_name',$brand)->pluck('document_id')->toArray();
            $result = $result->whereIn('id', $product);
            // dd($result->get());
        }

        $sourcing_documents = $result->where('document_type',$document_type)->latest()->get();
        dispatch(new ImageReadyJob($sourcing_documents));


        return response()->json(["message"=>"Images are ready for excel export"]);
    }


}
