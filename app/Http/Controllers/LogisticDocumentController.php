<?php

namespace App\Http\Controllers;

use Exception;
use PDF as MPDF;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Document;
use App\Models\Supplier;
use App\Models\BranchUser;
use App\Models\DamageRemark;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Models\ImportProduct;
use App\Models\DocumentRemark;
use App\Models\DocumentStatus;
use Yajra\DataTables\DataTables;
use App\Models\LogisticsDocument;
use App\Models\ReferenceDocument;
use App\Models\ImportProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Jobs\ImageReadyLogisticsJob;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\LogisticPendingExport;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Exports\LogisticsDamageRpBigExport;
use App\Notifications\DocumentNotification;
use Illuminate\Support\Facades\Notification;

class LogisticDocumentController extends Controller
{

    protected function connection()
    {
        return new LogisticsDocument();
    }

    public function new_index(Request $request)
    {
        $logistics_documents = LogisticsDocument::paginate(10);
    // try {
        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,8,10,11])->orderBy('sorting_id')->get();
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.logistics_small_document');
            $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->where('document_status', '=', 9)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.logistics_big_document');
            $document_status = DocumentStatus::get();
        } else if($request->type == 3) {
            $document_type_title = __('nav.logistics_accessory_document');
            $document_status = DocumentStatus::get();
        }
        // dd($document_status);
        $finished_document = DocumentStatus::where('document_status', '=', 9)->get();
        $document_type = isset($request->type) ? $request->type : '';
        $categories = Category::get();

        $old_from_date = Session::get('old_from_date') ?? '';
        $old_to_date = Session::get('old_to_date') ?? '';
        $old_branch_id = Session::get('branch_id') ?? '';
        $old_document_status = Session::get('document_status') ?? '';
        $old_category_id = Session::get('category') ?? '';
        return view('logistics_documents.new_index', compact(
            'branches',
            'document_status',
            'document_type_title',
            'document_type',
            'categories',
            'old_from_date',
            'old_to_date',
            'old_branch_id',
            'old_document_status',
            'old_category_id',
            'logistics_documents'
        ));
    }

    public function new_search_result(Request $request)
    {
        // dd($request->all());
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.logistics_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();

        } else if ($request->type == 2) {
            $document_type_title = __('nav.logistics_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.logistics_need_accessory_document');
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
        $result                 = LogisticsDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        // $document_statuses      = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        // $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,8,9,10,11])->orderBy('sorting_id')->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,7,9,10,11,14])->orderBy('sorting_id')->get();

        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);

        // dd($document_status);
        if (!empty($document_no)) {
            // $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
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
        // dd($result->where('document_type',$document_type)->get());
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id', $branch_id);
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
            $result = $result->where('category_id',$category_id);
        }


        if(empty($document_no) && (empty($document_from_date) && empty($document_to_date)) && empty($branch_id) && empty($document_status) && empty($next_step) && empty($category_id)){
            $filterdate = Carbon::now()->subMonths(1);

            $result = $result->where('document_date', ">=", $filterdate)
            ->where('document_type',$document_type)
            ->whereNotIn('document_status', [3,5,7,18,21,22,23])
            ->where('document_date',">=",$filterdate)
            ->orWhereNotIn('document_status', [3,5,7,18,21,22,23])->whereIn("branch_id",$branch_ids)->where('document_type',$document_type);
        }

        $logistics_documents = $result->where('document_type',$document_type)->latest()->paginate(10);
        // dd($logistics_documents);
        // dd($result->toSql());
        $logistics_documents->appends($request->all());
        if($request->ajax()){
            return response()->json($logistics_documents, 200);
        }

        return view('logistics_documents.new_search_result', compact(
            'branches',
            'document_status',
            'document_type_title',
            'document_type',
            'categories',
            'document_from_date',
            'document_to_date',
            'branch_id',
            'document_status',
            'category_id',
            'logistics_documents',
            'document_statuses'
        ));
    }


    public function small()
    {
        clearSession();

        $user = Auth::guard()->user();
        // $branch_id = $user->branch_id;
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);
        // dd($filterdate);

        // \DB::enableQueryLog();

        $logistics_documents = LogisticsDocument::where('document_type','1')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,18,21,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,18,21,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','1')
        ->orderBy('updated_at','DESC')->paginate(10);

        // dd(\DB::getQueryLog());


        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,9,10,11,7,14])->orderBy('sorting_id')->get();

        // dd(auth()->user());
        return view('logistics_documents.small', compact('logistics_documents','branches','categories','document_statuses'));
        // dd($logistics_documents);
    }
    public function big()
    {
        clearSession();

        $user = Auth::guard()->user();
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);

        $logistics_documents = LogisticsDocument::where('document_type','2')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,18,21,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,18,21,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','2')
        ->orderBy('updated_at','DESC')->paginate(10);

        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,9,10,11,7,14])->orderBy('sorting_id')->get();

        return view('logistics_documents.big', compact('logistics_documents','branches','categories','document_statuses'));
        // dd($sourcing_documents);
    }

    //need accessory
    public function need_accessory()
    {
        clearSession();

        $user = Auth::guard()->user();
        $branch_ids =$user->user_branches->pluck('branch_id');

        $filterdate = Carbon::now()->subMonths(1);

        $logistics_documents = LogisticsDocument::where('document_type','3')
        ->whereIn("branch_id",$branch_ids)
        ->whereNotIn('document_status', [3,5,7,18,21,22,23])
        ->where('document_date',">=",$filterdate)
        ->orWhereNotIn('document_status', [3,5,7,18,21,22,23])->whereIn("branch_id",$branch_ids)->where('document_type','3')
        ->orderBy('updated_at','DESC')->paginate(10);

        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories = Category::get();
        // $document_statuses = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,9,10,11,7,14])->orderBy('sorting_id')->get();

        return view('logistics_documents.need_accessory', compact('logistics_documents','branches','categories','document_statuses'));
        // dd($logistics_documents);
    }

    public function index(Request $request)
    {
    // try {
        $branches = BranchUser::where('user_id', auth()->user()->id)->get();
        $document_status = DocumentStatus::get();
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.logistics_small_document');
            $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();
        } else if ($request->type == 2) {
            $document_type_title = __('nav.logistics_big_document');
            $document_status = DocumentStatus::get();
        }else if ($request->type == 3) {
            $document_type_title = __('nav.logistics_need_acPcessory_document');
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
        return view('logistics_documents.index', compact(
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

    public function create()
    {
        try {
            $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
            $document_remark_types = DocumentRemark::get();
            $categories = Category::get();
            $branches = BranchUser::where('user_id', auth()->user()->id)->with('branches')->get();
            return view('logistics_documents.create', compact('suppliers', 'document_remark_types', 'categories', 'branches'));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to load Create Form!');
        }
    }

    public function edit($id)
    {
        $logistics = LogisticsDocument::where('id',$id)->first();
        $branch = Branch::where('branch_id', $logistics->branch_id)->first();
        $suppliers = Supplier::select('vendor_id', 'vendor_code', 'vendor_name')->get();
        $user_role = Auth::user()->roles->pluck('name')->first();
        $document_remark_types = DocumentRemark::get();
        $categories = Category::get();
        // dd($logistics);
        // $document_status_name = DocumentStatus::select('document_status_name')->where('document_status', $document->document_status)->first()->document_status_name;
        $reference_nos = ReferenceDocument::where('old_reference_no',$logistics->document_no)->get();
        $old_reference_no = ReferenceDocument::where('new_reference_no',$logistics->document_no)->first();
        $currentURL = Session::get('currentURL') != null ? Session::get('currentURL') : "";
        return view('logistics_documents.edit',compact('branch','suppliers','user_role','categories','logistics', 'reference_nos','old_reference_no','currentURL'));
    }


    public function store(Request $request)
    {
        // try {
            $filename = null;
            request()->validate([
                'document_type' => 'required',
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

                    // Storage::disk('ftp1')->put($filename_2, fopen($request->file('operation_attach_file_2'), 'r+'));

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
            // dd($filename,$filename_2);
            $logistics_document = LogisticsDocument::create($request->except(['operation_attach_file','operation_attach_file_2','excel_attach_file']) + ['operation_attach_file' => $filename] + ['operation_attach_file_2' => $filename_2]
            + ['excel_attach_file' => $filename_3]);

            return redirect()->route('logistics_documents.edit', $logistics_document->id);
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("logistics_documents.index"))
        //         ->with('error', 'Fail to Store Department!');
        // }
    }

    public function update(Request $request,$id)
    {

        $logistics = LogisticsDocument::where('id', $id)->first();
        if ($logistics->document_status == 1 && Gate::allows('edit-document-operation-attach-file')) {
            $update_logistics['operation_id'] = Auth::id();
            $logistics['car_no'] = $request->car_no;

            $filename = "";
            if ($request->operation_attach_file || $request->excel_attach_file || $request->operation_attach_file_2) {

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
                        $filename= $logistics->operation_attach_file;

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
                            $filename_2= $logistics->operation_attach_file_2;

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
                        $filename_3 = $logistics->excel_attach_file;
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename = $filename ?? $logistics->operation_attach_file;
                $filename_2= $filename_2 ?? $logistics->operation_attach_file_2;
                $filename_3= $filename_3 ?? $logistics->excel_attach_file;

                $status = $logistics->update($request->except(['operation_attach_file','operation_attach_file_2','excel_attach_file']) +
                ['operation_attach_file' => $filename] + ['operation_attach_file_2' => $filename_2]  + ['excel_attach_file' => $filename_3]);
            } else {
                $filename = $logistics->operation_attach_file;
                $filename_2 = $logistics->operation_attach_file_2;
                $filename_3 = $logistics->excel_attach_file;
                $logistics->update($request->except(['operation_attach_file']) + ['operation_attach_file' => $filename]
                + ['operation_attach_file_2' => $filename_2]  + ['excel_attach_file' => $filename_3]);
            }

        }
        if ($logistics->document_status == 2 && Gate::allows('update-document-ch-complete')) {

            $update_logistics['category_head_id'] = Auth::id();
            $update_logistics['category_head_remark'] = $request->merchandising_remark;
            $logistics->update($update_logistics);

        }
        // dd($logistics->document_status);
        if (( $logistics->document_status == 17 || $logistics->document_status == 18 || $logistics->document_status == 19) && Gate::allows('update-document-acc-complete')) {

            $update_logistics['accounting_id'] = Auth::id();
            $update_logistics['issue_doc_no'] = $request->issue_no;
            $update_logistics['accounting_remark'] = $request->accounting_remark;

            $filename ='';
            $filename2 ='';
            if ($logistics->document_type == 2 ) {
                if( Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin'){
                    if($request->file1 == null){
                        $request->validate([
                            'issue_no' => 'required',
                            'issue_doc_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                            'account_cn_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                        ]);
                    }else{

                        if($request->account_cn_attach_file == null && $request->file2 ==null){
                            $request->validate([
                                'account_cn_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                            ]);
                        }
                        if($request->isuue_no == null) {
                            $request->validate([
                                'issue_no' => 'required',
                            ]);
                        }
                    }
                }
            }
            if ($request->isuue_doc_attach_file != '' && $request->account_cn_attach_file != '') {

                $filename = 'acc' . auth()->id() . '_' . time() . '_' . $_FILES['isuue_doc_attach_file']['name'];

                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename, fopen($request->file('isuue_doc_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename, fopen($request->file('isuue_doc_attach_file'), 'r+'));
                    }


                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename = $filename ?? $logistics->accounting_attach_file;
                $filename2 = 'acc' . auth()->id() . '_' . time() . '_' . $_FILES['account_cn_attach_file']['name'];
                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename2, fopen($request->file('account_cn_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename2, fopen($request->file('account_cn_attach_file'), 'r+'));
                    }


                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename2 = $filename2 ?? $logistics->accounting_attach_file2;

                $logistics->update($request->except(['isuue_doc_attach_file' ,'account_cn_attach_file']) + ['accounting_attach_file'=>$filename, 'accounting_attach_file2'=>$filename2]);
            }
            else{
                if ($request->issue_doc_attach_file) {
                    $filename = 'acc_' . auth()->id() . '_' . time() . '_' . $_FILES['issue_doc_attach_file']['name'];

                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename, fopen($request->file('issue_doc_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename, fopen($request->file('issue_doc_attach_file'), 'r+'));
                        }

                        } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }

                    $filename = $filename ?? $logistics->accounting_attach_file;
                    $logistics->update($request->except(['issue_doc_attach_file']) + ['accounting_attach_file' => $filename]);

                }
                if ($request->account_cn_attach_file) {

                    $filename2 = 'acc_' . auth()->id() . '_' . time() . '_' . $_FILES['account_cn_attach_file']['name'];
                    try {
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp1')->put($filename2, fopen($request->file('account_cn_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp3')->put($filename2, fopen($request->file('account_cn_attach_file'), 'r+'));
                        }


                        } catch (Exception $e) {
                        return 'Something went wrong with the connection ' . $e->getMessage();
                    }

                    $filename2 = $filename2 ?? $logistics->accounting_attach_file2;
                    $logistics->update($request->except(['account_cn_attach_file']) + ['accounting_attach_file2' => $filename2]);

                }
            }

            if($logistics->accounting_attach_file2 || $logistics->accounting_attach_file){
                $logistics->update($request->except(['issue_doc_attach_file','account_cn_attach_file']));
            }

        }

        // dd(( Gate::allows('update-document-log-complete')));
        if(($logistics->document_status == 16 || $logistics->document_status == 4 || $logistics->document_status == 19 || $logistics->document_status == 18) && ( Gate::allows('add-issue-no'))){
            // dd("kfj");
            $update_logistics['issue_doc_no'] = $request->issue_no;
            $update_logistics['logistics_id'] = Auth::id();
            $update_logistics['logistics_remark'] = $request->logistics_remark;
            $filename ='';
            $filename2 ='';
            if ($logistics->document_type == 2 ) {
                if( Auth::user()->roles->pluck('name')->first() == 'Accounting' || Auth::user()->roles->pluck('name')->first() == 'Admin' ){
                    // dd($request->file1 ==null);
                    if($request->file1 == null){
                        $request->validate([
                            'issue_no' => 'required',
                            'issue_doc_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                        ]);
                    }
                    else {
                        if($request->issue_no == null) {
                            $request->validate([
                                'issue_no' => 'required',
                            ]);
                        }
                    }
                }

            }
            if ($request->issue_doc_attach_file) {
                $filename = 'acc' . auth()->id() . '_' . time() . '_' . $_FILES['issue_doc_attach_file']['name'];

                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename, fopen($request->file('issue_doc_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename, fopen($request->file('issue_doc_attach_file'), 'r+'));
                    }
                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename = $filename ?? $logistics->accounting_attach_file;

                $logistics->update($request->except(['issue_doc_attach_file' ]) + ['accounting_attach_file'=>$filename]);
                // dd(LogisticsDocument::where('id', $id)->first());
            }

        }

        // dd(($logistics->document_status == 4 && (Auth::user()->roles->pluck('name')->first() == 'Logistics' || Auth::user()->roles->pluck('name')->first() == 'Admin') && Gate::allows('update-document-log-complete')));
        if((($logistics->document_status == 4 || $logistics->document_status == 19) && (Auth::user()->roles->pluck('name')->first() == 'Logistics' || Auth::user()->roles->pluck('name')->first() == 'Admin') && Gate::allows('update-document-log-complete'))){
            $update_logistics['logistics_id'] = Auth::id();
            $update_logistics['logistics_remark'] = $request->logistics_remark;
            $update_logistics['car_no'] = $request->car_no;
            $request->validate([
                'car_no' => 'required',
            ]);
            if ($logistics->document_type == 2 ) {
                $request->validate([
                    'logistics_remark'=> 'required',
                    'logistics_attach_file' => 'nullable|max:10240|mimes:jpeg,jpg,png,pdf,xlsx,xls,csv',
                ]);
            }

            // dd($request->logistic_attach_file);
            if($request->logistics_attach_file){
                $filename1 = 'lo' . auth()->id() . '_' . time() . '_' . $_FILES['logistics_attach_file']['name'];

                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp1')->put($filename1, fopen($request->file('logistics_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp3')->put($filename1, fopen($request->file('logistics_attach_file'), 'r+'));
                    }

                } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }

                $filename1 = $filename1 ?? $logistics->logistics_attach_file;
                $logistics->update($request->except(['logistics_attach_file']) + ['logistics_attach_file'=>$filename1]);
            }
            // dd($logistics);

        }

        $logistics->update($update_logistics);
        // dd( LogisticsDocument::where('id', $id)->first());
        return redirect()->route('logistics_documents.edit', $logistics->id)->with("updatesuccess","Your document is updated successfully");

    }


    public static function generate_doc_no($type, $date, $branch_id)
    {
        // try {
            $type == '1' ? ($prefix = 'LSD') : ($type == '2' ? ($prefix = 'LBD') : ($prefix = 'LNA'));

            $branch_prefix = Branch::select('branch_short_name')->where('branch_id', $branch_id)->first()->branch_short_name;
            $dateStr = str_replace("/", "-", $date);
            $date = date('Y/m/d H:i:s', strtotime($dateStr));

            $prefix = $prefix . $branch_prefix;
            $last_id = LogisticsDocument::select('id', 'document_no')->where('document_type', $type)
            ->whereDate('logistics_documents.document_date', '=', $date)
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
        $logistics = LogisticsDocument::where('id', $document_id)->first();
        $user_name = Auth::user()->name;
        if ($attach_type == 1) {
            $attach_file_name = 'Operation Attach File';
            $attach_file_type = $logistics->operation_attach_file;
        } else if ($attach_type == 2) {
            $attach_file_name = 'Merchandising Attach File';
            $attach_file_type = $logistics->merchandising_attach_file;
        } else if ($attach_type == 4) {
            $attach_file_name = 'Logistics Attach File';
            $attach_file_type = $logistics->logistics_attach_file;
        } else if ($attach_type == 9) {
            $attach_file_name = 'Accounting Attach File';
            $attach_file_type = $logistics->accounting_attach_file;
        } else if ($attach_type == 5) {
            $attach_file_name = 'RG IN Attach File';
            $attach_file_type = $logistics->operation_rg_in_attach_file;
        } else if ($attach_type == 6) {
            $attach_file_name = 'Accounting DB Attach File';
            $attach_file_type = $logistics->accounting_db_attach_file;
        } else if ($attach_type == 7) {
            $attach_file_name = 'Operation Attach File';
            $attach_file_type = $logistics->operation_attach_file_2;
        }


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
      $aa = Storage::disk('public')->put($attach_file_type, $ftp_file);
    //   dd($aa);
       if (substr($attach_file_type, -3) !== 'pdf' || substr($attach_file_type, -3) !== 'PDF') {
            return response()->file(public_path('storage/' . $attach_file_type));
        }
        elseif(substr($attach_file_type,-3)== 'xls' || substr($attach_file_type,-4)== 'xlsx' || substr($attach_file_type,-3)== 'csv')
        {

            $data = File::deleteDirectory(public_path('storage'));
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

            Storage::disk('public')->put($attach_file_type, $ftp_file);

            $file="storage/$attach_file_type";
            return response()->download($file);
        }

        $pdf = MPDF::loadView('logistics_documents.view_document_attach_file', compact('user_name', 'attach_file_name', 'attach_file_type'));
        // dd($pdf);
        return $pdf->stream($logistics->document_no . '_' . $attach_file_name . ".pdf");


    }

    public function logistics_bm_approve(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
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
                    return redirect()->route('logistics_small')
                    ->with('success', 'Branch Manager Checked Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Branch Manager Checked Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Branch Manager Checked Successfully');
                }
            }
            // return redirect()->route('logistics_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Branch Manager Checked Successfully');

    }

    public function logistics_bm_reject(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['branch_manager_id'] = Auth::id();
            $request['branch_manager_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 3;
            $request['reject_remark'] = $request->reason;

            $document->update($request->all());

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'BM manager Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Branch Manager Rejected Successfully');
                }
            }
            // return redirect()->route('logistics_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Branch Manager Rejected Successfully');

    }
    public function logistics_mm_reject(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['log_manager_reject_remark_id'] = Auth::id();
            $request['log_manager_reject_remark_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 20;
            $request['reject_remark'] = $request->reason;

            $document->update($request->all());

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Logistics Manager Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Logistics Manager Rejected Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Logistics Manager Rejected Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Logistics Manager Rejected Successfully');
                }
            }

            // return redirect()->route('logistics_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Logistics Manager Rejected Successfully');
        // } catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("logistics_documents.index"))
        //         ->with('error', 'Fail to Branch Manager Reject!');
        // }
    }
    // log reject
    public function logistics_reject($id, $reason)
    {
        // dd($id,);
        // try {
            if ($id == null) {
                return back()->with('error', 'Error');
            }
            // $logistic_id = $request->logistics_id;
            $document = LogisticsDocument::whereId($id)->first();
            // dd($document);
            // $document['logistics_reject_id'] = Auth::id();

            // $document['logistics_reject_updated_datetime'] = date('Y-m-d H:i:s');
            // $document['document_status'] = 23;
            // $document['reject_remark'] = $reason;
            // dd($request);

            $document->update(['logistics_reject_id' => Auth::id(), 'logistics_reject_updated_datetime' => date('Y-m-d H:i:s'), 'document_status'=> 23, 'reject_remark' => $reason ]);
            // dd($document);
            // reject noti
            $message = "Your Document " . $document->document_no . " is rejected ";
            $user = User::where('id',$document->operation_id)->get();
            // dd($user);
            $type = 1;
            Notification::send($user, new DocumentNotification($message, $id, $type));
            // end
            // dd($document);
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Logistics Staff Rejected Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Logistics Staff Rejected Successfully');
                }
                elseif($document->document_type==2)
                {
                    return redirect()->route('logistics_big')
                    ->with('success', 'Logistics Staff Rejected Successfully');
                }
                else{
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Logistics Staff Rejected Successfully');
                }
            }

            // redirect_page($document_type,'Sourcing Manager Rejected Successfully');
    }
    // end
    public function logistics_ch_approve(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['category_head_id'] = Auth::id();
            $request['category_head_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 4;

            $document->update($request->all());
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Merchandise acknowledge Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Merchandise acknowledge Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Merchandise acknowledge Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Merchandise acknowledge Successfully');
                }
            }

            // return redirect()->route('logistics_documents.index', 'type=' . $document->document_type)
            //     ->with('success', 'Merchandise acknowledge Successfully');

    }
    public function logistics_ch_reject(Request $request)
    {

        if ($request->logistics_id == null) {
            return redirect()->route('logistics_documents.index')
                ->with('error', 'Error');
        }
        $logistics_id = $request->logistics_id;
        $document = $this->connection()->where('id', $logistics_id)->first();
        $request['ch_reject_remark_id'] = Auth::id();
        $request['ch_reject_remark_updated_datetime'] = date('Y-m-d H:i:s');
        $request['document_status'] = 5;
        $request['reject_remark_id'] = $request->reason;

        $document->update($request->all());

        if(Session::get('currentURL') != null)
        {
            return redirect(Session::get('currentURL'))->with('success', 'Category Head Rejected Successfully');;
        }
        else
        {
            if($document->document_type==1)
            {
                return redirect()->route('logistics_small')
                ->with('success', 'Category Head Rejected Successfully');
            }
            elseif($document->document_type==2){
                return redirect()->route('logistics_big')
                ->with('success', 'Category Head Rejected Successfully');
            }
            elseif($document->document_type == 3){
                return redirect()->route('logistics_need_accessory')
                ->with('success', 'Category Head Rejected Successfully');
            }
        }
    }
    public function account_issue_approve(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['accounting_id'] = Auth::id();
            $request['accounting_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 19;

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
                    return redirect()->route('logistics_small')
                    ->with('success', 'Accounting Issued Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Accounting Issued Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Accounting Issued Successfully');
                }
            }


    }
    public function logistics_log_approve(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }

            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['logistics_id'] = Auth::id();
            $request['logistics_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 16;

            $document->update($request->all());

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Logistics Confirmed Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Logistics Confirmed Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Logistics Confirmed Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Logistics Confirmed Successfully');
                }
            }

    }

    public function logistics_log_mm_approve(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
            $request['sourcing_manger_id'] = Auth::id();
            $request['sourcing_manger_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 17;

            $document->update($request->all());
            // dd( $document);
            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Sourcing Manager Approve Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    return redirect()->route('logistics_small')
                    ->with('success', 'Sourcing Manager Approve Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Sourcing Manager Approve Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Sourcing Manager Approve Successfully');
                }
            }


    }
    public function finished_document(Request $request)
    {
        // try {
            $logistics_id = $request->logistics_id;
            if ($logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            // dd($request->all());
            if($request->file == null){
                $request->validate([
                    'account_cn_attach_file' => 'required|max:10240|mimes:jpeg,jpg,png,pdf',
                ]);
            }
            $document = $this->connection()->where('id', $logistics_id)->first();

            if($document->issue_doc_no == null && $document->document_type == 2){
                return redirect()->route('logistics_documents.edit',$logistics_id )
                    ->with('error', 'Issue no is required');
            }

            $request['finished_id'] = Auth::id();
            $request['finished_updated_datetime'] = date('Y-m-d H:i:s');
            $request['document_status'] = 18;
            if($document->issue_doc_datetime == ''){
                $request['issue_doc_datetime'] =  date('Y-m-d H:i:s');
                $request['account_issued_id'] = Auth::id();
            }else {
                $request['issue_doc_datetime'] = $document->issue_doc_datetime;
            }

            $document->update($request->all());

            if(Session::get('currentURL') != null)
            {
                return redirect(Session::get('currentURL'))->with('success', 'Finished document Successfully');;
            }
            else
            {
                if($document->document_type==1)
                {
                    // dd('hi');
                    return redirect()->route('logistics_small')
                    ->with('success', 'Finished document Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Finished document Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Finished document Successfully');
                }
            }

    }

    public function change_to_previous_status(Request $request)
    {
        // try {
            if ($request->logistics_id == null) {
                return redirect()->route('logistics_documents.index')
                    ->with('error', 'Error');
            }
            $logistics_id = $request->logistics_id;
            $document = $this->connection()->where('id', $logistics_id)->first();
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
            if (Gate::allows('update-document-ch-complete') && ($document_status == 4 || $document_status ==5)) {
                $request['category_head_id'] = null;
                $request['category_head_updated_datetime'] = null;
                $request['document_status'] = 2;
                $user = User::where('id', $document->category_head_id)->first();
            }
            if (Gate::allows('update-document-acc-complete') && $document_status == 19) {
                $request['accounting_id'] = null;
                $request['accounting_updated_datetime'] = null;
                $request['document_status'] = 2;
                $user = User::where('id', $document->accounting_id)->first();
            }
            if (Gate::allows('update-document-log-complete') && $document_status == 16) {
                $request['logistics_id'] = null;
                $request['logistics_updated_datetime'] = null;
                $request['document_status'] = 19;
                $user = User::where('id', $document->logistics_id)->first();
            }
            if (Gate::allows('update-document-logmm-complete') && ($document_status == 17 || $document_status == 20)) {
                $request['sourcing_manger_id'] = null;
                $request['sourcing_manger_updated_datetime'] = null;
                $request['document_status'] = 16;
                $user = User::where('id', $document->sourcing_manger_id)->first();
            }
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
                    return redirect()->route('logistics_small')
                    ->with('success', 'Change to Previous Level Successfully');
                }
                elseif($document->document_type==2){
                    return redirect()->route('logistics_big')
                    ->with('success', 'Change to Provious Level Successfully');
                }
                elseif($document->document_type == 3){
                    return redirect()->route('logistics_need_accessory')
                    ->with('success', 'Change to Provious Level Successfully');
                }
            }

    }

    public function logistics_destory($document_id)
    {
        // try {
            // dd("hello");
            $logistics = LogisticsDocument::where('id', $document_id)->first();
            $logistics->forceDelete();
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

    public function check_import_image(Request $request,$id)
    {
        $check_products = ImportProduct::where('document_id', $id)->get();
        foreach($check_products as $check_product){
            $checkImage = ImportProductImage::where('import_product_id', $check_product->id)->first();
            if(!$checkImage){
                return response()->json([ 'error' => 'checkImage'], 200);
            }
        }
        return response()->json([ 'success' => 'successfully'], 200);
    }

    public function check_damage_percentage(Request $request,$id)
    {

        $check_products = ImportProduct::where('document_id', $id)->get();
        $logistics_document = LogisticsDocument::where('id',$id)->first();
        // dd($check_products);
        if($logistics_document->document_type == 2){
            foreach($check_products as $check_product){
                $images = ImportProductImage::where('import_product_id', $check_product->id)->get();
                // dd($images);
                foreach($images as $image){
                    if(!$image->mer_percentage){
                        return response()->json([ 'error' => 'checkPercentage'], 200);
                    }
                }

            }
        }

        return response()->json([ 'success' => 'successfully'], 200);
    }
    public function check_log_percentage(Request $request,$id)
    {
        $check_products = ImportProduct::where('document_id', $id)->get();
        $logistics_document = LogisticsDocument::where('id',$id)->first();
        // dd($check_products);
        if($logistics_document->document_type == 2){
            foreach($check_products as $check_product){
                $images = ImportProductImage::where('import_product_id', $check_product->id)->get();
                foreach($images as $image){
                    if(!$image->log_percentage){
                        // dd($image);
                        return response()->json([ 'error' => 'checkPercentage'], 200);
                    }
                }

            }
        }

        return response()->json([ 'success' => 'successfully'], 200);
    }

    public function logistics_listing(Request $request)
    {
        // try {
            $branches = BranchUser::where('user_id', auth()->user()->id)->get();
            $document_status = DocumentStatus::get();
            if ($request->detail_type == 1) {
                $document_type_title = __('home.finish_log_small_doc');
                $document_type = '1';
                $document_status = DocumentStatus::where('document_status', 18)->get();
            } else if ($request->detail_type == 2) {
                $document_type_title = __('home.log_small_pending');
                $document_status = LogisticsDocument::whereIn('id', ['1','2','4','6','16','17','19'])->get();
                $document_type = '1';
            } else if ($request->detail_type == 3) {
                $document_type_title = __('home.reject_log_small_doc');
                $document_status = DocumentStatus::get();
                $document_type = '1';
            } else if ($request->detail_type == 4) {
                $document_type_title = __('home.finish_log_big_doc');
                $document_status = DocumentStatus::where('document_status', 18)->get();
                $document_type = '2';
            } else if ($request->detail_type == 5) {
                $document_type_title = __('home.log_big_pending');
                $document_status = DocumentStatus::get();
                $document_type = '2';
            } else if ($request->detail_type == 6) {
                $document_type_title = __('home.log_big_reject');
                $document_status = DocumentStatus::get();
                $document_type = '2';
            }else if ($request->detail_type == 7) {
                $document_type_title = __('home.finish_accessary_doc');
                $document_status = DocumentStatus::get();
                $document_type = '3';
            } else if ($request->detail_type == 8) {
                $document_type_title = __('home.log_accessory_pending');
                $document_status = DocumentStatus::get();
                $document_type = '3';
            } else if ($request->detail_type == 9) {
                $document_type_title = __('home.log_accessory_reject');
                $document_status = DocumentStatus::get();
                $document_type = '3';

            }
            $detail_type = isset($request->detail_type) ? $request->detail_type : '';
            // dd($detail_type);
            $categories = Category::get();

            $old_from_date = Session::get('old_from_date') ?? '';
            $old_to_date = Session::get('old_to_date') ?? '';
            $old_branch_id = Session::get('branch_id') ?? '';
            $old_document_status = Session::get('document_status') ?? '';
            $old_category_id = Session::get('category') ?? '';

            return view('logistics_documents.logistics_listing', compact(
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


    public function download_excel_file($document_id)
    {
        $logistics = LogisticsDocument::where('id', $document_id)->first();
        $attach_file_type = $logistics->excel_attach_file;
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

    // Issued
    public function logistics_issued_approve(Request $request){
        if ($request->logistics_id == null) {
            return back()
            ->with('error', 'Error');
        }
        $logistics_id = $request->logistics_id;
        $document = $this->connection()->where('id', $logistics_id)->first();
        $request['account_issued_id'] = Auth::id();
        $request['issue_doc_datetime'] = date('Y-m-d H:i:s');
        // dd($document->document_status);
       if($document->document_status == 4 || $document->document_status == 16 || $document->document_status == 21){
            $request['document_status'] = $document->document_status;
       }else{
            $request['document_status'] = 19;
       }
        // dd($request->all());
        $document->update($request->all());
        // $document->update(['account_issued_id' =>  Auth::id() , 'document_status' => $request['document_status'] , 'issue_doc_datetime' =>date('Y-m-d H:i:s')]);
        // dd($this->connection()->where('id', $logistics_id)->first());
        if($document->document_type==1)
        {
            // dd('hi');
            return redirect()->route('logistics_small')
            ->with('success', 'Accounting Issued Successfully');
        }
        elseif($document->document_type==2)
        {
            return redirect()->route('logistics_big')
            ->with('success', 'Accounting Issued Successfully');
        }
        else{
            return redirect()->route('logistics_need_accessory')
            ->with('success', 'Accounting Issued Successfully');
        }
    }
    // end

    public function search_result(Request $request)
    {
        // try{
        $document_no = (!empty($_GET["document_no"])) ? ($_GET["document_no"]) : ('');
        $fromDate = (!empty($_GET["document_from_date"])) ? ($_GET["document_from_date"]) : ('0');
        $fromDate = $fromDate ?? Session::get('old_from_date');
        $toDate = (!empty($_GET["document_to_date"])) ? ($_GET["document_to_date"]) : ('0');
        $toDate = $toDate ?? Session::get('old_to_date');
        $document_type = ($_GET["document_type"]) ? ($_GET["document_type"]) : ('');
        $document_branch = (!empty($_GET["document_branch"])) ? ($_GET["document_branch"]) : ('0');
        // $document_branch = $document_branch ?? Session::get('branch_id');
        $document_status = (!empty($_GET["document_status"])) ? ($_GET["document_status"]) : ('0');
        $document_status = $document_status ?? Session::get('document_status');

        $category = (!empty($_GET["category"])) ? ($_GET["category"]) : ('0');
        $category = $category ?? Session::get('category');
        $issue_doc_datetime = (!empty($_GET["issue_doc_datetime"])) ? ($_GET["issue_doc_datetime"]) : ('0');
        $issue_doc_datetime = $issue_doc_datetime ?? Session::get('issue_doc_datetime');
        $result = $this->connection();

        if ($fromDate != "0") {
            Session::put('old_from_date', $fromDate);
            $dateStr = str_replace("/", "-", $fromDate);
            $fromDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('logistics_documents.document_date', '>=', $fromDate);
        } else {
            Session::put('old_from_date', $fromDate);
        }
        if ($toDate != "0") {
            Session::put('old_to_date', $toDate);
            $dateStr = str_replace("/", "-", $toDate);
            $toDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('logistics_documents.document_date', '<=', $toDate);
        } else {
            Session::put('old_to_date', $toDate);
        }

        if ($document_type == 1) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_type == 2) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_type == 3) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_no != "") {
            // $result = $result->where('document_no', 'ilike', '%' . $document_no . '%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        if ($document_status != "0") {
            Session::put('document_status', $document_status);
            $result = $result->where('logistics_documents.document_status', $document_status);
        } else {
            Session::put('document_status', $document_status);
        }

        if ($document_branch != "0") {
            Session::put('branch_id', $document_branch);
            $result = $result->where('logistics_documents.branch_id', $document_branch);
        } else {
            Session::put('branch_id', $document_branch);
        }
        if ($category != "0") {
            Session::put('category', $category);
            $result = $result->where('logistics_documents.category_id', $category);
        } else {
            Session::put('category', $category);
        }
        if ($issue_doc_datetime != "0") {
            Session::put('issue_doc_datetime', $issue_doc_datetime);
            $result = $result->where('logistics_documents.issue_doc_datetime', $issue_doc_datetime);
        } else {
            Session::put('issue_doc_datetime', $issue_doc_datetime);
        }
        // dd($issue_doc_datetime);
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        $result = $result->whereIn('branch_id', $user_branches);
        $can_delete_document = Gate::allows('delete-document');

        $result = $result->with('Category', 'Products')->orderBy('updated_at', 'DESC')->get();

        return DataTables::of($result)
            ->editColumn('document_status', function ($data) {
                if($data->document_status == 4){
                    return 'Acknowledge';
                }
                    return $data->DocumentStatus->document_status_name;
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
            ->editColumn('logistics_updated_datetime', function ($data) {
                return $data->logistics_updated_datetime ? date('d/m/Y', strtotime($data->logistics_updated_datetime)) : '';
            })
            ->editColumn('sourcing_manger_updated_datetime', function ($data) {
                return $data->sourcing_manger_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_manger_updated_datetime)) : '';
            })
            ->editColumn('issue_doc_datetime', function ($data) {
                return $data->issue_doc_datetime ? date('d/m/Y', strtotime($data->issue_doc_datetime)) : '';
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

    public function logistics_search_result(Request $request)
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
            $result = $result->whereDate('logistics_documents.document_date', '>=', $fromDate);
        } else {
            Session::put('old_from_date', $fromDate);
        }
        if ($toDate != "0") {
            Session::put('old_to_date', $toDate);
            $dateStr = str_replace("/", "-", $toDate);
            $toDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('logistics_documents.document_date', '<=', $toDate);
        } else {
            Session::put('old_to_date', $toDate);
        }

        if ($document_type == 1) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_type == 2) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_type == 3) {
            $result = $result->where('logistics_documents.document_type', $document_type);
        }
        if ($document_no != "") {
            // $result = $result->where('document_no', 'ilike', '%' . $document_no . '%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
        if ($document_status != "0") {
            Session::put('document_status', $document_status);
            $result = $result->where('logistics_documents.document_status', $document_status);
        } else {
            Session::put('document_status', $document_status);
        }

        if ($document_branch != "0") {
            Session::put('branch_id', $document_branch);
            $result = $result->where('logistics_documents.branch_id', $document_branch);
        } else {
            Session::put('branch_id', $document_branch);
        }
        if ($category != "0") {
            Session::put('category', $category);
            $result = $result->where('logistics_documents.category_id', $category);
        } else {
            Session::put('category', $category);
        }
        $user_branches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
        $result = $result->whereIn('branch_id', $user_branches);

        $can_delete_document = Gate::allows('delete-document');

        if ($detail_type == 1) {
            $result = $result->where('document_status', '=', 18)->where('document_type', 1);
        }
        if ($detail_type == 2) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '16', '17', '19', '21'])->where('document_type', 1);
        }
        if ($detail_type == 3) {
            $result = $result->whereIn('document_status', ['3','5','20'])->where('document_type', 1);
        }
        if ($detail_type == 4) {
            $result = $result->where('document_status', '18')->where('document_type', 2);
        }
        if ($detail_type == 5) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '16', '17', '19', '21'])->where('document_type', 2);
        }
        if ($detail_type == 6) {
            $result = $result->whereIn('document_status', ['3', '5','20'])->where('document_type', 2);
        }
        if ($detail_type == 7) {
            $result = $result->whereIn('document_status', ['18'] )->where('document_type', 3);
        }
        if ($detail_type == 8) {
            $result = $result->whereIn('document_status', ['1', '2', '4', '16', '17', '19', '21'])->where('document_type', 3);
        }
        if ($detail_type == 9) {
            $result = $result->where('document_status', ['3', '5','20'])->where('document_type', 3);
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
                    return 'Acknowledge';
                }
                    return $data->DocumentStatus->document_status_name;
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
            ->editColumn('logistics_updated_datetime', function ($data) {
                return $data->logistics_updated_datetime ? date('d/m/Y', strtotime($data->logistics_updated_datetime)) : '';
            })
            ->editColumn('sourcing_manger_updated_datetime', function ($data) {
                return $data->sourcing_manger_updated_datetime ? date('d/m/Y', strtotime($data->sourcing_manger_updated_datetime)) : '';
            })
            ->editColumn('accounting_updated_datetime', function ($data) {
                return $data->accounting_updated_datetime ? date('d/m/Y', strtotime($data->accounting_updated_datetime)) : '';
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
            $logistic_doc = LogisticsDocument::where('branch_id' , $data[0]->branch_id)
                                            ->where('issue_doc_no' , $issue_no)->pluck('document_no')->toArray();
           if($logistic_doc){
                $data = ['duplicate', $logistic_doc];
                return response()->json($data , 200);
           }else{
                return response()->json(['data' => $data[0]] , 200);
           }

        }else {
            return response()->json(null ,200);
        }
    }

    //select product
    public function select_product($id)
    {

        $logistics = LogisticsDocument::where('id',$id)->first();
        $branch = Branch::where('branch_id', $logistics->branch_id)->first();
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
        $logistics_products = ImportProduct::where('document_id',$logistics->id)->get();
        $check_type = null;
        foreach($logistics_products as $check_product){
            if(!$check_product->finished_status){
                 $check_type = 1;
            }
        }
        $check_type = $check_type == 1 ? 1 : 2;
        $ref_no = request()->ref;
        return view('logistics_documents.select_product',compact('branch','suppliers','user_role','categories','logistics','damage_remark_types','exchange_rate','check_type', 'ref_no'));
    }

    public function move_to_other_doc(Request $request)
    {
        $selectedIDs = $request->selectedIDs;

        $document_id = $request->documentID;
        $ref_no = $request->ref_no;
        // dd($product_code);
        //check reference more 2 time

        //find original_doc document
        $old_logistic_document = LogisticsDocument::where('id',$document_id)->first();
        // dd($old_logistic_document);
        // $check_reference_no = ReferenceDocument::where('old_reference_no', $old_logistic_document->document_no)->count();
        $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
        // dd($check_reference_no);
        if($check_reference_no < 2){

           //find products with selectdIDs
            $old_doc_no = $old_logistic_document->document_no;
            $document_type = $request->type;
            $document_date = now();
            $new_operation_file_name =null;
            if($old_logistic_document->operation_attach_file){
                $old_data =  explode(".",$old_logistic_document->operation_attach_file);
                $new_operation_file_name =  $old_data[0] . '_' . time() . '_update.'. $old_data[1];
                if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp1')->copy($old_logistic_document->operation_attach_file, $new_operation_file_name);
                }else{
                    Storage::disk('ftp3')->copy($old_logistic_document->operation_attach_file, $new_operation_file_name);
                }

            }
            $new_logistic_file_name =null;
            if($old_logistic_document->logistics_attach_file){
                $old_data =  explode(".",$old_logistic_document->logistics_attach_file);
                $new_logistic_file_name =  $old_data[0] . '_' . time() . '_update.'. $old_data[1];
                if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp1')->copy($old_logistic_document->logistics_attach_file, $new_logistic_file_name);
                }else{
                    Storage::disk('ftp3')->copy($old_logistic_document->logistics_attach_file, $new_logistic_file_name);
                }

            }
            $logistics_document['document_no'] = $this->generate_doc_no($document_type, $document_date, $old_logistic_document->branch_id);
            $logistics_document['operation_id'] = $old_logistic_document->operation_id;
            $logistics_document['branch_id'] = $old_logistic_document->branch_id;
            $logistics_document['category_id'] = (int) $old_logistic_document->category_id;
            $logistics_document['document_type'] = (int) $document_type;
            $logistics_document['car_no'] =  $old_logistic_document->car_no;

            $logistics_document['document_date'] = $document_date;

            $logistics_document['operation_attach_file'] = $new_operation_file_name;
            $logistics_document['operation_updated_datetime'] = $old_logistic_document->operation_updated_datetime;
            $logistics_document['operation_remark'] = $old_logistic_document->operation_remark;

            $logistics_document['branch_manager_id'] = $old_logistic_document->branch_manager_id;
            $logistics_document['branch_manager_updated_datetime'] = $old_logistic_document->branch_manager_updated_datetime;
            $logistics_document['branch_manager_remark'] = $old_logistic_document->branch_manager_remark;

            if($document_type == 1 ){
                $logistics_document['document_status'] = 2;
                $logistics_document['category_head_id'] = null;
                $logistics_document['category_head_updated_datetime'] = null;
                $logistics_document['category_head_remark'] = null;
            }else{
                $logistics_document['document_status'] = $old_logistic_document->document_status;
                $logistics_document['category_head_id'] = $old_logistic_document->category_head_id;
                $logistics_document['category_head_updated_datetime'] = $old_logistic_document->category_head_updated_datetime;
                $logistics_document['category_head_remark'] = $old_logistic_document->category_head_remark;
            }

            $logistics_document['logistics_attach_file'] = $new_logistic_file_name;

            //recreate new document with new document id
            $new_logistics_document = LogisticsDocument::create($logistics_document);

            /////send noti////
            $operation_id = $old_logistic_document->operation_id;
            $branch_manager_id = $old_logistic_document->branch_manager_id;
            $category_head_id = $old_logistic_document->category_head_id;
            $message = "Your Document " . $old_logistic_document->document_no . " is changed to " . $new_logistics_document->document_no;
            // $aa = new DocumentNotification($message, (int)$document_id);
            $users = User::whereIn('id',[$operation_id,$branch_manager_id,$category_head_id])->get();

            foreach($users as $user){
                $type = 1;
                Notification::send($user, new DocumentNotification($message, $new_logistics_document->id, $type));
            }

            //create reference no
            $reference_doc['new_reference_no'] = $new_logistics_document->document_no;
            $reference_doc['old_reference_no'] = $old_doc_no;
            $reference_doc = ReferenceDocument::create($reference_doc);

            //update products
            // $logistics_products = logisticsProduct::where('document_id', $old_logistic_document->id)->get();
            foreach($selectedIDs as $product_id){
                $import_product = ImportProduct::where('id', $product_id)->first();
                    $update_product['document_id'] = $new_logistics_document->id;
                    $import_product->update($update_product);
                $logistics_product_images = ImportProductImage::where(['import_product_id'=>$import_product->id])->update(['doc_id'=>$new_logistics_document->id]);
            }
        }
        else{
            return response()->json([ 'error' => 'more_two_times'], 200);

        }

        return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_logistics_document->document_no], 200);
    }

    public function logistics_detail_listing(Request $request)
    {
        // dd( LogisticsDocument::where('document_status', 16)->get());
        try {
            $result = LogisticsDocument::query();
            $document_status = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,9,10,11,7,14])->orderBy('sorting_id')->get();
            $userBranches = BranchUser::where('user_id', auth()->user()->id)->pluck('branch_id')->toArray();
            $branches = BranchUser::where('user_id', auth()->user()->id)->get();
            // dd(Gate::allows('my-document-account-cn'));

            if (Gate::allows('my-document-operation') || Gate::allows('my-document-rgin') || Gate::allows('my-document-rgout')) {

                $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1)
                    ->where('operation_id', auth()->user()->id);
            } else if (Gate::allows('my-document-bm')) {

                $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1);
            } else if (Gate::allows('my-document-ch')) {
                $result = $result->where('document_status', 2);
            } else if (Gate::allows('my-document-mm')) {
                $result = $result->where('document_status', 16);
            } else if(Gate::allows(('my-document-log'))) {
                $result = $result->where('document_status', 4);

            } else if (Gate::allows('my-document-account-cn')) {
                $result = $result->whereIn('document_status', [17]);
            }

            $documents = $result->latest()->paginate(10);
            $categories = Category::get();


            return view('logistics_documents.logistics_detail_listing', compact(
                'branches',
                'document_status',
                'categories',
                'documents'
            ));
        } catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("logistics_documents.index"))
                ->with('error', 'Fail to load Data!');
        }
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
        $result                 = LogisticsDocument::query();
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
        // dd(Gate::allows('my-document-rgin'));
        if (Gate::allows('my-document-operation') || Gate::allows('my-document-rgin') || Gate::allows('my-document-rgout')) {

            $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1)
                ->where('operation_id', auth()->user()->id);
        }
        else if (Gate::allows('my-document-bm')) {

            $result = $result->whereIn('branch_id', $userBranches)->where('document_status', 1);
        }
        else if (Gate::allows('my-document-ch')) {
            $result = $result->where('document_status', 2);
        }
        else if (Gate::allows('my-document-mm')) {

            $result = $result->where('document_status', 16);
        }
        else if(Gate::allows(('my-document-log'))) {
            $result = $result->where('document_status', 4);

        }
        else if (Gate::allows('my-document-account-cn')) {
            $result = $result->whereIn('document_status', [17]);
        }
        if(!empty($branch_id))
        {
            $result = $result->where('branch_id', $branch_id);
        }
        if (!empty($document_no)) {
            // $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
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
            // dd($document_status);
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


        $document_status = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,9,10,11,7,14])->orderBy('sorting_id')->get();

        return view('logistics_documents.logistics_detail_listing', compact(
            'branches',
            'document_status',
            'categories',
            'documents'
        ));

    }

    public function pending_document_export($document_type, $detail_type)
    {
        try {
            // dd("hi");
            $today = date('Y-m-d');
            if($document_type == 1 && $detail_type == 10)
            {
                $status = 'LogisticsSmallDocument-Export-';
            }
            elseif($document_type == 1 && $detail_type == 2)
            {
                $status = 'PendingLogisticsSmallDocument-Export-';
            }
            elseif($document_type == 1 && $detail_type == 3)
            {
                $status = 'LogisticsSmallRejectDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 11)
            {
                $status = 'LogisticsBigDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 5)
            {
                $status = 'LogisticsBigPendingDocument-Export-';
            }
            elseif($document_type == 2 && $detail_type == 6)
            {
                $status = 'LogisticsBigRejectDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 12)
            {
                $status = 'LogisticsNeedAccessoryDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 8)
            {
                $status = 'LogisticsNeedAccessoryPendingDocument-Export-';
            }
            elseif($document_type == 3 && $detail_type == 9)
            {
                $status = 'LogisticsNeedAccessoryRejectDocument-Export-';
            }
            return Excel::download(new LogisticPendingExport($document_type, $detail_type), $status . $today . '.xlsx');
        } catch (\Exception $e) {
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to Excel Export!');
        }
    }


    public function damage_rp(Request $request, $logistic_type)
    {
        if($logistic_type == 'big'){
            $document_type = 2;
        }else if($logistic_type == 'small'){
            $document_type = 1;
        }
        // dd($document_type);
        $request->type = $request->type ?? $document_type;
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.logistics_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();

        } else if ($request->type == 2) {
            $document_type_title = __('nav.logistics_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.logistics_need_accessory_document');
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
        $result                 = LogisticsDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        // $document_statuses      = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        // $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,8,9,10,11])->orderBy('sorting_id')->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,7,9,10,11,14])->orderBy('sorting_id')->get();

        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);

        // dd($document_status);
        if (!empty($document_no)) {
            // $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
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
        // dd($result->where('document_type',$document_type)->get());
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id', $branch_id);
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
            $result = $result->where('category_id',$category_id);
        }

        // $result = $result->whereIn('document_status',[4,16,17,19,18]);

        // if(empty($document_no) && (empty($document_from_date) && empty($document_to_date)) && empty($branch_id) && empty($document_status) && empty($next_step) && empty($category_id)){
        //     $filterdate = Carbon::now()->subMonths(1);

        //     $result = $result->where('document_date', ">=", $filterdate)
        //     ->where('document_type',$document_type)
        //     ->whereNotIn('document_status', [3,5,7,18,21,22,23])
        //     ->where('document_date',">=",$filterdate)
        //     ->orWhereNotIn('document_status', [3,5,7,18,21,22,23])->whereIn("branch_id",$branch_ids)->where('document_type',$document_type);
        // }

        if($request->document_search == 'Search' || $request->document_search == null){


            $logistics_documents = $result->where('document_type',$document_type)->latest()->paginate(10);
            $logistics_documents->appends($request->all());

            if($request->ajax()){
                return response()->json($logistics_documents, 200);
            }

            return view('logistics_documents.damage_rp', compact(
                'branches',
                'document_status',
                'document_type_title',
                'document_type',
                'categories',
                'document_from_date',
                'document_to_date',
                'branch_id',
                'document_status',
                'category_id',
                'logistics_documents',
                'document_statuses',
                'logistic_type'
            ));
        }else if($request->document_search == 'Export'){
            // dd($document_type);
            $logistics_documents = $result->where('document_type',$document_type)->latest()->get();
            $response = Excel::download(new LogisticsDamageRpBigExport($logistics_documents), "LogisticsDamageReportBig".Carbon::now()->format('Y-m-d').".xlsx");

            return $response;
        }
    }

    public function image_ready(Request $request){
        if ($request->type == null) {
            $document_type_title = 'All';
        } else if ($request->type == 1) {
            $document_type_title = __('nav.logistics_small_document');
            // $document_status = DocumentStatus::where('document_status', '!=', 10)->where('document_status', '!=', 11)->get();

        } else if ($request->type == 2) {
            $document_type_title = __('nav.logistics_big_document');
            // $document_status = DocumentStatus::get();
        } else if ($request->type == 3) {
            $document_type_title = __('nav.logistics_need_accessory_document');
            // $document_status = DocumentStatus::get();
        }

        $document_type          = isset($request->type) ? $request->type : '';

        $document_no            = $request->document_no;
        $document_from_date     = $request->document_from_date ?? Carbon::now()->subMonths(6)->format("Y-m-d");
        $document_to_date       = $request->document_to_date ?? Carbon::now()->format("Y-m-d");
        $branch_id              = $request->branch_id;
        $document_status        = $request->document_status;
        $category_id            = $request->category_id;
        $next_step              = $request->next_step;
        $result                 = LogisticsDocument::query();
        $branches               = BranchUser::where('user_id', auth()->user()->id)->get();
        $categories             = Category::get();
        // $document_statuses      = DocumentStatus::select('*')->whereNotIn('document_status', [6,7,8,10,11,12,13,14,15,19])->get();
        // $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,8,9,10,11])->orderBy('sorting_id')->get();
        $document_statuses      = DocumentStatus::select('*')->whereIn('sorting_id', [1,2,3,4,6,7,9,10,11,14])->orderBy('sorting_id')->get();

        //session put data

        Session::put('document_no', $document_no);
        Session::put('document_from_date', $document_from_date);
        Session::put('document_to_date', $document_to_date);
        Session::put('branch_id', $branch_id);
        Session::put('document_status', $document_status);
        Session::put('category_id', $category_id);
        Session::put('next_step', $next_step);

        // dd($document_status);
        if (!empty($document_no)) {
            // $result = $result->where('document_no', 'like', '%'.$document_no.'%');
            if(str_contains($document_no, 'SS') || str_contains($document_no, 'SB')  || str_contains($document_no, 'SN') || str_contains($document_no, '-'))
            {
                $result = $result->where('document_no', 'ilike', '%'.$document_no.'%');
            }
            else
            {
                $document_id = ImportProduct::where('product_code_no', $document_no)->pluck('document_id')->toArray();
                $result = $result->whereIn('id', $document_id);
            }
        }
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
        // dd($result->where('document_type',$document_type)->get());
        // if (!empty($document_to_date)) {
        //     $result = $result->where('updated_at', 'like', '%'.$document_to_date.'%');
        // }

        if (!empty($branch_id)) {
            $result = $result->where('branch_id', $branch_id);
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
            $result = $result->where('category_id',$category_id);
        }
        $logistics_documents = $result->where('document_type',$document_type)->latest()->get();
        dispatch(new ImageReadyLogisticsJob($logistics_documents));


        return response()->json(["message"=>"Images are ready for excel export"]);
    }

}
