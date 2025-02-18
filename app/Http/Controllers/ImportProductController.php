<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\DamageRemark;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Models\ImportProduct;
use App\Models\LogisticsDocument;
use App\Models\ReferenceDocument;
use App\Models\ImportProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\DocumentNotification;
use Illuminate\Support\Facades\Notification;

class ImportProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function connection()
    {
        return new ImportProduct();
    }

    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());

        $request->product_price = (int) str_replace(',', '', $request->product_price);
        $request->total_price = (int) str_replace(',', '', $request->total_price);
        $import_product['document_id'] = $request->document_id;
        $import_product['product_code_no'] = $request->product_code_no;
        $import_product['product_name'] = $request->product_name;
        $import_product['product_price'] =(int)$request->product_price;
        $import_product['product_unit'] = $request->product_unit;
        // $sysqty=$import_product['stock_quantity'];
        // $stock_sys=$request->stock_quantity;
        // $stock_qrt=$request->quantity;
        // if ($stock_qrt > $stock_sys) {
        //     return back()->withInput()->withErrors(['quantity' => 'Quantity cannot be greater than Stock Quantity']);
        // }

        $import_product['stock_quantity'] = $request->quantity;
        $import_product['system_quantity'] = $request->system_quantity;
        $import_product['total_price'] =$request->total_price;

        $import_product['remark'] =$request->remark;

        $import_product =ImportProduct::create($import_product);
        $logistics = LogisticsDocument::where('id',$import_product->document_id)->first();
        $percentage_images = ImportProductImage::where(['import_product_id'=>$import_product->id,'doc_id'=>$import_product->document_id])->get()->groupBy('row');

        return view('import_products.edit',compact('import_product','logistics','percentage_images'));
        // return redirect()->route('logistics_documents.edit', $request->document_id)->with('success', 'Products is successfully added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\ImportProduct  $importProduct
     * @return \Illuminate\Http\Response
     */
    public function show(ImportProduct $importProduct)
    {
        try {
            return $importProduct;
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("logistics_documents.index"))
                ->with('error', 'Fail to show Product!');
        }
    }


    public function edit($id)
    {
        $input_images = ImportProductImage::where('import_product_id', $id)->get();

        // Delete File
        // $data = File::deleteDirectory(public_path('storage'));
        // Get File form ftp
        // $ftp_file = Storage::disk('ftp')->get('import_product_test/'.'pr_181_1669370271_3.jpg');
        foreach($input_images as $input_image){
            if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
            {
                $ftp_file = Storage::disk('ftp1')->get($input_image->media_link);
            }else{
                try {
                    $ftp_file = Storage::disk('ftp3')->get($input_image->media_link);
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    try {
                        $ftp_file = Storage::disk('ftp1')->get($input_image->media_link);
                    } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                        $ftp_file = null;
                    }
                }
            }

            Storage::disk('public')->put($input_image->media_link, $ftp_file);
            $input_image = ImportProductImage::where('import_product_id', $id)->first();
        }
        // Copy to public storage
        $import_product = ImportProduct::where('id',$id)->first();
        $logistics = LogisticsDocument::where('id',$import_product->document_id)->first();
        // dd($import_product);
        $input_image = ImportProductImage::where('import_product_id', $id)->first();
        $percentage_images = ImportProductImage::where(['import_product_id'=>$import_product->id,'doc_id'=>$import_product->document_id])->get()->groupBy('row');
        // dd($percentage_images);
        return view('import_products.edit',compact('import_product','logistics','input_image','percentage_images'));
    }


    public function update(Request $request, ImportProduct $importProduct)
    {
        $import_product = ImportProduct::where('id',$importProduct->id)->withTrashed()->first();
        // dd($request->damage_percentage);
        if (Gate::allows('update-document-ch-complete') || Gate::allows('update-document-log-complete')) {
            $percentage_amount = $importProduct->product_price - ($importProduct->product_price * ($request->damage_percentage / 100));
            $update_product['percentage_amount'] = (int)$percentage_amount;
        }

        // $update_product['damage_percentage'] = $request->damage_percentage;
        $update_product['remark'] = $request->remark;
        $import_product->update($update_product);

        $logistics = LogisticsDocument::where('id',$importProduct->document_id)->first();
        $percentage_amount = ImportProduct::where('document_id',$logistics->id)->sum('percentage_amount');

        $update_logistics['percentage_total_amount'] = $percentage_amount;

        $logistics->update($update_logistics);
        $percentage_images = ImportProductImage::where(['import_product_id'=>$import_product->id,'doc_id'=>$import_product->document_id])->get()->groupBy('row');
        // dd($percentage_images);
        return redirect("import_products/$import_product->id/edit");
        // return view('import_products.edit',compact('import_product','logistics','percentage_images'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\ImportProduct  $importProduct
     * @return \Illuminate\Http\Response
     */
    public function import_product_destory($id)
    {
        $document = LogisticsDocument::where('id', request()->doc_id)->first();
        if($document->document_status == 1)
        {
            ImportProduct::where('id',$id)->forceDelete();
        }
        else{
            ImportProduct::where('id',$id)->delete();
        }

        return response()->json([
            'success' => 'Product deleted successfully!',
        ]);
    }

    public function midea_link_store(Request $request)
    {
        $filename = null;
            if ($request->media_link) {
                $request->validate([
                    'media_link' => 'required|max:10240|mimes:jpeg,jpg,png,pdf,mp4',
                ]);
                $filename = 'pr_' . auth()->id() . '_' . time() . '_' . $_FILES['media_link']['name'];
                try {
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        $aa = Storage::disk('ftp1')->put($filename, fopen($request->file('media_link'), 'r+'));
                    }else{
                        $aa = Storage::disk('ftp3')->put($filename, fopen($request->file('media_link'), 'r+'));
                    }

                    } catch (Exception $e) {
                    return 'Something went wrong with the connection ' . $e->getMessage();
                }
            }
            $request['import_product_id'] = $request->product_id;
            ImportProductImage::create($request->except(['media_link']) + ['media_link' => $filename]);

            return redirect()->route('import_products.edit',$request->product_id);

    }

    public function improt_product_list_by_document(Request $request)
    {
        // try {
            $document_id = (!empty($_GET["document_id"])) ? ($_GET["document_id"]) : ('');

            $result = $this->connection()->where('document_id', $document_id)->with('document','import_product_image')->orderby('id')->withTrashed()->get();
            // dd($result);
            return DataTables::of($result)
                ->addColumn('media_link_status', function ($data) {
                    if(isset($data->import_product_image)){
                        return 1;
                    }
                    return 2;
                })
                ->editColumn('product_code_no', function ($data) {
                    return $data->product_code_no ? $data->product_code_no : '';
                })
                ->editColumn('product_name', function ($data) {
                    return $data->product_name ? $data->product_name : '';
                })
                ->editColumn('product_unit', function ($data) {
                    return $data->product_unit ? $data->product_unit : '';
                })
                ->editColumn('damage_percentage', function ($data) {
                    // dd($data->damage_percentage);
                    if (isset($data->damage_percentage)) {
                        return number_format($data->damage_percentage);
                    }
                    return '';
                })
                ->editColumn('percentage_amount', function ($data) {
                    if (isset($data->percentage_amount)) {
                        return number_format($data->percentage_amount);
                    }
                    return '';
                })
                ->editColumn('product_price', function ($data) {
                    if (isset($data->product_price)) {
                        return number_format($data->product_price);
                    }
                    return '';
                })
                ->editColumn('total_price', function ($data) {
                    if (isset($data->total_price)) {
                        return number_format($data->total_price);
                    }
                    return '';
                })
                ->editColumn('stock_quantity', function ($data) {
                    return $data->stock_quantity;
                })
                ->addColumn('action', function ($data) {
                    return $data->document->document_status;
                })
                ->setRowClass(function ($data) {
                    return $data->deleted_at ? 'deleted-row' : '';
                })
                ->rawColumns(['action', 'operation', 'branch_manager',
                    'category_head', 'merchandising_manager', 'operation_rg_out',
                    'account_cn', 'operation_rg_in', 'account_db'])
                ->addIndexColumn()
                ->make(true);
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to get Products!');
        // }
    }

    public function product_image_by_product(Request $request)
    {
        // try {
            $product_id = (!empty($_GET["product_id"])) ? ($_GET["product_id"]) : ('');
            $result = ImportProductImage::where('import_product_id', $product_id);

            return DataTables::of($result)
            ->addColumn('checkBox', function ($data) {
                return 'checkBox';
            })
            ->addColumn('action', function ($data) {
                return 'action';
            })
                ->addIndexColumn()
                ->make(true);
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to get Products!');
        // }
    }
    public function import_image_destory($id)
    {
        $import_product_image = ImportProductImage::where('id',$id)->first();
        $import_product_image->delete();
        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
        {
            Storage::disk('ftp1')->delete($import_product_image->media_link);
        }else{
            Storage::disk('ftp3')->delete($import_product_image->media_link);
        }

        return response()->json([
            'success' => 'Image deleted successfully!',
        ]);
    }

    // update stock quantity
    public function update_stock_quantity(Request $request){
        $import_product = ImportProduct::where('id',$request->id)->first();
        $import_product['new_stock_qty'] = $request->stock_quantity;
        $import_product->update();
        return response()->json([$import_product , 200]);
    }

    //select product
    public function import_select_product($id)
    {
        $import_product = ImportProduct::where('id', $id)->withTrashed()->first();
        $percentage_images = ImportProductImage::where(['import_product_id' => $import_product->id])->get()->groupBy('row');
        $data = count($percentage_images);
        return response()->json($data);
    }

    public function import_next_step($id)
    {
        $import_products = ImportProduct::where('id',$id)->withTrashed()->get();
        $logistics = LogisticsDocument::where('id',$import_products[0]->document_id)->first();
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
        $percentage_images = ImportProductImage::where(['import_product_id'=>$import_products[0]->id])->get()->groupBy('row');

        // dd($import_products);
        $check_type = null;
        foreach($import_products as $check_product){
            if(!$check_product->finished_status){
                 $check_type = 1;
            }
        }
        $check_type = $check_type == 1 ? 1 : 2;
        // $ref_no = ReferenceDocument::where('old_reference_no', request()->all())->get();
        $ref_no = request()->ref;
        // dd($ref_no);
        return view('import_products.select_product',compact('branch','suppliers','user_role','categories','logistics','damage_remark_types','exchange_rate','check_type', 'ref_no', 'percentage_images'));
    }

    // public function move_to_other_doc(Request $request)
    // {
    //     $selectedIDs = $request->selectedIDs;
    //     $ref_no = $request->ref_no;
    //     $product_image = ImportProductImage::findOrFail($selectedIDs[0]);
    //     $import_product =ImportProduct::findOrFail($product_image->import_product_id);
    //     $old_logistics_document = LogisticsDocument::findOrFail($import_product->document_id);
    //     $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
    //     // dd($check_reference_no);
    //     if($check_reference_no < 2){
    //         $old_doc_no = $old_logistics_document->document_no;
    //         $doc_no = $this->generate_doc_no($request->type, now(), $old_logistics_document->branch_id);
    //         $doc_status = $request->type == 1 ? 2 : $old_logistics_document->document_status;
    //         $cat_head_id = $request->type == 1 ? null : $old_logistics_document->document_status;
    //         $cat_head_date = $request->type == 1 ? null : $old_logistics_document->category_head_updated_datetime;
    //         $cat_head_remark = $request->type == 1 ? null : $old_logistics_document->category_head_remark;

    //         $new_logistics_document = $old_logistics_document->copy(now(), $doc_no, $request->type, $doc_status, $cat_head_id, $cat_head_date, $cat_head_remark);

    //         $operation_id = $old_logistics_document->operation_id;
    //         $branch_manager_id = $old_logistics_document->branch_manager_id;
    //         $category_head_id = $old_logistics_document->category_head_id;
    //         $message = "Your Document " . $old_logistics_document->document_no . " is changed to " . $new_logistics_document->document_no;
    //         // $aa = new DocumentNotification($message, (int)$document_id);
    //         $users = User::whereIn('id',[$operation_id,$branch_manager_id,$category_head_id])->get();

    //         foreach($users as $user){
    //             $type = 1;
    //             Notification::send($user, new DocumentNotification($message, $new_logistics_document->id, $type));
    //         }

    //         //create reference no
    //         $reference_doc['new_reference_no'] = $new_logistics_document->document_no;
    //         $reference_doc['old_reference_no'] = $old_doc_no;
    //         $reference_doc = ReferenceDocument::create($reference_doc);
    //         $product_id = $import_product->id;
    //         $product = $import_product->replicate();
    //         $product->document_id = $new_logistics_document->id;
    //         $product->stock_quantity = null ;
    //         $product->save();
    //         $total = 0 ;
    //         foreach($selectedIDs as $id){
    //             $import_product_image  = ImportProductImage::findOrFail($id);
    //             // dd($id);
    //             $import_product_image->doc_id = $new_logistics_document->id ;
    //             $import_product_image->import_product_id = $product->id ;
    //             $import_product_image->save();
    //             // dd($import_product_image->seperate_qty);
    //             $total += $import_product_image->seperate_qty;

    //         }
    //         $total_price = $total * $product->product_price;
    //         $product->stock_quantity = $total;
    //         $product->total_price = $total_price;
    //         $product->save();
    //         $old_product = ImportProduct::findOrFail($product_id);
    //         $qty = ($old_product->stock_quantity - $total);
    //         $tot_price = $qty * $old_product->product_price;
    //         $old_product->stock_quantity = $qty;
    //         $old_product->total_price = $tot_price;
    //         // dd($old_product->stock_quantity);
    //         $old_product->save();

    //     }
    //     else{
    //         return response()->json([ 'error' => 'more_two_times'], 200);

    //     }

    //     return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_logistics_document->document_no], 200);

    // }

    public function move_to_other_doc(Request $request)
    {
        $selectedIDs = $request->selectedIDs;
        // dd($selectedIDs);
        $ref_no = $request->ref_no;
        $product_image = ImportProductImage::findOrFail($selectedIDs[0][0]);
        $import_product =ImportProduct::findOrFail($product_image->import_product_id);
        $old_logistics_document = LogisticsDocument::findOrFail($import_product->document_id);
        $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
        // dd($check_reference_no);
        if($check_reference_no < 2){
            $old_doc_no = $old_logistics_document->document_no;
            $doc_no = $this->generate_doc_no($request->type, now(), $old_logistics_document->branch_id);
            $doc_status = $request->type == 1 ? 2 : $old_logistics_document->document_status;
            $cat_head_id = $request->type == 1 ? null : $old_logistics_document->document_status;
            $cat_head_date = $request->type == 1 ? null : $old_logistics_document->category_head_updated_datetime;
            $cat_head_remark = $request->type == 1 ? null : $old_logistics_document->category_head_remark;

            $new_logistics_document = $old_logistics_document->copy(now(), $doc_no, $request->type, $doc_status, $cat_head_id, $cat_head_date, $cat_head_remark);

            $operation_id = $old_logistics_document->operation_id;
            $branch_manager_id = $old_logistics_document->branch_manager_id;
            $category_head_id = $old_logistics_document->category_head_id;
            $message = "Your Document " . $old_logistics_document->document_no . " is changed to " . $new_logistics_document->document_no;
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
            $product_id = $import_product->id;
            $product = $import_product->replicate();
            $product->document_id = $new_logistics_document->id;
            $product->stock_quantity = null ;
            $product->save();
            $total = 0 ;
            // dd($product->id);
            foreach($selectedIDs as $ids){
                foreach($ids as $id)
                {
                    $import_product_image  = ImportProductImage::findOrFail($id);
                    // dd($id);
                    $import_product_image->doc_id = $new_logistics_document->id ;
                    $import_product_image->import_product_id = $product->id ;
                    $import_product_image->save();
                }
                // dd($import_product_image->seperate_qty);
                $total += $import_product_image->seperate_qty;

            }
            $total_price = $total * $product->product_price;
            $product->stock_quantity = $total;
            $product->total_price = $total_price;
            $product->save();
            $old_product = ImportProduct::findOrFail($product_id);
            if(($old_product->stock_quantity - $total) == 0)
            {

                $old_product->forceDelete();
                return response()->json([ 'delete' => 'successfully','new_doc_no'=> $new_logistics_document->document_no], 200);
            }
            else
            {
                $qty = ($old_product->stock_quantity - $total);
                $tot_price = $qty * $old_product->product_price;
                $old_product->stock_quantity = $qty;
                $old_product->total_price = $tot_price;
                // dd($old_product->stock_quantity);
                $old_product->save();

            }

        }
        else{
            return response()->json([ 'error' => 'more_two_times'], 200);

        }

        return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_logistics_document->document_no], 200);

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
}
