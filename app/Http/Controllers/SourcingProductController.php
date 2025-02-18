<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Supplier;
use App\Models\DamageRemark;
use App\Models\ExchangeRate;
use Illuminate\Http\Request;
use App\Models\ImportProduct;
use Illuminate\Support\Carbon;
use App\Models\SourcingProduct;
use App\Models\SourcingDocument;
use App\Models\ReferenceDocument;
use App\Models\DamageRemarkReason;
use App\Models\SourcingProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use App\Notifications\DocumentNotification;
use Illuminate\Support\Facades\Notification;

class SourcingProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    protected function connection(){

        return new SourcingProduct;
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

            //   dd($request->all());
        $request->product_price = (int) str_replace(',', '', $request->product_price);
        $request->total_price = (int) str_replace(',', '', $request->total_price);
        $request->validate([
            'quantity' => 'required',
        ]);
        $sourcing_product['document_id'] = $request->document_id;
        $sourcing_product['product_code_no'] = $request->product_code_no;
        $sourcing_product['product_name'] = $request->product_name;
        $sourcing_product['product_price'] =$request->product_price;
        $sourcing_product['product_brand_name'] =$request->product_brand_name;
        $sourcing_product['product_unit'] = $request->product_unit;
        // $stock_sys=$request->stock_quantity;
        // $stock_qrt=$request->quantity;
        // if ($stock_qrt > $stock_sys) {
        //     return back()->withInput()->withErrors(['quantity' => 'Quantity cannot be greater than Stock Quantity']);
        // }
        $sourcing_product['stock_quantity'] =$request->quantity;
        $sourcing_product['system_quantity'] =$request->system_quantity;

        $sourcing_product['total_price'] =$request->total_price;
        $sourcing_product['remark'] =$request->remark;

        $sourcing_product = SourcingProduct::create($sourcing_product);
        $sourcings = SourcingDocument::where('id',$sourcing_product->document_id)->first();

        $damage_remark_types = DamageRemark::get();
        $exchange_rates= ExchangeRate::select('type','sell')->whereIn('type', ['Baht','USD','Yuan'])->whereDate('created_at',Carbon::today())->get()->toArray();
        $percentage_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_product->id,'doc_id'=>$sourcing_product->document_id])->get()->groupBy('row');
        $reasons = DamageRemarkReason::get();
        // dd($percentage_images);
        return view('sourcing_products.edit',compact('sourcing_product','sourcings','exchange_rates','damage_remark_types','percentage_images','reasons'));

        // return redirect()->route('sourcing_documents.edit', $request->document_id)->with('success', 'Products is successfully added!');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SourcingProdut  $sourcingProdut
     * @return \Illuminate\Http\Response
     */
    public function show(SourcingProduct $sourcingProduct)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SourcingProdut  $sourcingProdut
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {

        $input_images = SourcingProductImage::where('sourcing_product_id', $id)->get();
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
            $input_image = SourcingProductImage::where('sourcing_product_id', $id)->first();
        }

        $sourcing_product = SourcingProduct::where('id',$id)->withTrashed()->first();
        // dd($sourcing_product);
        $sourcings = SourcingDocument::where('id',$sourcing_product->document_id)->first();

        $input_image = SourcingProductImage::where('sourcing_product_id', $id)->first();
        $damage_remark_types = DamageRemark::get();
        $exchange_rates= ExchangeRate::select('type','sell')->whereIn('type', ['Baht','USD','Yuan'])->whereDate('created_at',Carbon::today())->get()->toArray();
        $percentage_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_product->id])->get()->groupBy('row');
        $reasons = DamageRemarkReason::get();

// dd($percentage_images);
        return view('sourcing_products.edit',compact('sourcing_product','sourcings','input_image','exchange_rates','damage_remark_types','percentage_images', 'reasons'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SourcingProdut  $sourcingProdut
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request,$id)
    {
        // dd($request->all());

        $request->baht_value = (float) str_replace(',', '', $request->baht_value);
        $request->kyat_value = (float) str_replace(',', '', $request->kyat_value);

        $sourcing_product = SourcingProduct::where('id',$id)->first();
        // if (Gate::allows('update-document-ch-complete') || Gate::allows('update-document-log-complete')) {
        //     $percentage_amount = $sourcing_product->product_price - ($sourcing_product->product_price * ($request->damage_percentage / 100));
        //     $update_product['percentage_amount'] = (int)$percentage_amount;
        //     $update_product['damage_percentage'] = $request->damage_percentage;
        // }
        $update_product['remark'] = $request->remark;
        // dd(Gate::allows('update-document-log-complete'));
        if (Gate::allows('update-document-log-complete')) {
            $exchange_rate = ExchangeRate::where('sell', $request->currency_type)->whereDate('created_at',Carbon::today())->pluck('type')->first();
            $update_product['baht_value'] = $request->baht_value;
            $update_product['kyat_value'] = $request->kyat_value;
            $update_product['currency_type'] = $exchange_rate;
            $update_product['damage_remark_types'] = $request->damage_remark_types;
            if($request->damage_remark_types == 3)
            {
                $update_product['damage_remark_reasons'] = $request->damage_remark_reasons;
            }
            else
            {
                $update_product['damage_remark_reasons'] = null;
            }

        }
        // dd($update_product);
        $sourcing_product->update($update_product);

        // $percentage_amount = SourcingProduct::where('document_id',$request->sourcing_id)->sum('percentage_amount');
        $baht_value = SourcingProduct::where('document_id',$request->sourcing_id)->sum('baht_value');
        $kyat_value = SourcingProduct::where('document_id',$request->sourcing_id)->sum('kyat_value');
        $sourcings = SourcingDocument::where('id',$request->sourcing_id)->first();

        // $update_sourcing['percentage_total_amount'] = $percentage_amount;
        $update_sourcing['bahit_total_amount'] = $baht_value;
        $update_sourcing['kyat_total_amount'] =$kyat_value;

// dd($sourcing_product);

        $sourcings->update($update_sourcing);

        // $exchange_rates= ExchangeRate::select('sell')->where('type', 'Baht')->whereDate('created_at',Carbon::today())->first();
        // if($exchange_rates){
        //     $exchange_rate = $exchange_rate->sell;
        // }else{
        //     $exchange_rate =  0;
        // }

        $exchange_rates= ExchangeRate::select('type','sell')->whereIn('type', ['Baht','USD','Yuan'])->whereDate('created_at',Carbon::today())->get()->toArray();
        $damage_remark_types = DamageRemark::get();
        $reasons = DamageRemarkReason::get();
        // dd($reasons);
        $percentage_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_product->id,'doc_id'=>$sourcing_product->document_id])->get()->groupBy('row');
        // dd($percentage_images);
        return view('sourcing_products.edit',compact('sourcing_product','sourcings','exchange_rates','damage_remark_types','percentage_images','reasons'));
    }

    // delete
    public function delete($id){

        $document = SourcingDocument::where('id', request()->doc_id)->first();
        if($document->document_status == 1)
        {
            SourcingProduct::where('id',$id)->forceDelete();
        }else{

            SourcingProduct::where('id',$id)->delete();
        }
        // dd($sourcing_product);
        return response()->json([
            'success' => 'Product deleted successfully!',
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SourcingProduct  $sourcingProduct
     * @return \Illuminate\Http\Response
     */
    public function sourcing_product_destory($id)
    {
        SourcingProduct::where('id',$id)->delete();
        return response()->json([
            'success' => 'Product deleted successfully!',
        ]);
    }

    public function sourcing_product_list_by_document(Request $request)
    {
        // try {
            $document_id = (!empty($_GET["document_id"])) ? ($_GET["document_id"]) : ('');

            $result = $this->connection()->where('document_id', $document_id)->with('document','sourcing_product_image')->orderby('id')->withTrashed()->get();
            // dd($result);

            return DataTables::of($result)
                ->addColumn('media_link_status', function ($data) {
                    if(isset($data->sourcing_product_image)){
                        return 1;
                    }
                    return 2;
                })
                ->editColumn('finished_status', function ($data) {
                    if (isset($data->finished_status)) {
                        return 1;
                    }
                    return 2;
                })
                ->editColumn('damage_remark_types', function ($data) {
                    if($data->damage_remark_types == 1 ){
                        return 'Claim';
                    }
                    else if($data->damage_remark_types == 2){
                        return 'Replacement';
                    }
                    else if($data->damage_remark_types == 3){
                        return "Cannot Claim";
                    }else{
                        return '';
                    }
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
                ->editColumn('stock_quantity', function ($data) {
                    return $data->stock_quantity;
                })
                ->editColumn('product_price', function ($data) {
                    if (isset($data->product_price)) {
                        // dd($data->product_price);
                        return number_format($data->product_price);
                    }
                    return '';
                })
                ->editColumn('total_price', function ($data) {
                    if (isset($data->total_price)) {
                        // dd($data->total_price);
                        return number_format($data->total_price);
                    }
                    return '';
                })
                ->editColumn('kyat_value', function ($data) {
                    if (isset($data->kyat_value)) {
                        return number_format($data->kyat_value, 2, '.', ',');
                    }
                    return '';
                })
                ->editColumn('damage_percentage', function ($data) {
                    // dd($data);
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
                ->editColumn('baht_value', function ($data) {
                    if (isset($data->baht_value)) {
                        return $data->baht_value;
                    }
                    return '';
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
            $result = SourcingProductImage::where('sourcing_product_id', $product_id);

            return DataTables::of($result)
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

    public function sourcing_midea_link_store(Request $request)
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
            $request['sourcing_product_id'] = $request->product_id;
            SourcingProductImage::create($request->except(['media_link']) + ['media_link' => $filename]);
            return redirect()->route('sourcing_products.edit',$request->product_id);

    }

    public function sourcing_image_destory($id)
    {
        $sourcing_product_image = SourcingProductImage::where('id',$id)->first();
        $sourcing_product_image->delete();
        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
        {
            Storage::disk('ftp1')->delete($sourcing_product_image->media_link);
        }else{
            Storage::disk('ftp3')->delete($sourcing_product_image->media_link);
        }

        return response()->json([
            'success' => 'Image deleted successfully!',
        ]);
    }
    public function update_product_status($id)
    {
        $sourcing_product = SourcingProduct::where('id',$id)->first();
        $update_product['finished_status'] = 1;
        $sourcing_product->update($update_product);

        $sourcing_document = SourcingDocument::where('id', $sourcing_product->document_id)->first();
        return redirect()->route('sourcing_documents.edit', $sourcing_document->id)->with('success', 'Products is successfully added!');


    }

    // update stock quantity
    public function update_stock_quantity(Request $request){
        $sourcing_product = SourcingProduct::where('id',$request->id)->first();
        $sourcing_product['new_stock_qty'] = $request->stock_quantity;
        $sourcing_product->update();
        return response()->json([$sourcing_product , 200]);
    }

    //select product
    public function sourcing_select_product($id)
    {
        $sourcing_product = SourcingProduct::where('id',$id)->withTrashed()->first();
        $sourcings = SourcingDocument::where('id',$sourcing_product->document_id)->first();
        // $input_image = SourcingProductImage::where('sourcing_product_id', $id)->first();
        $percentage_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_product->id])->get()->groupBy('row');
        $data = count($percentage_images);
        return response()->json($data);
    }

    public function sourcing_next_step($id)
    {
        $sourcing_products = SourcingProduct::where('id',$id)->withTrashed()->get();
        $sourcing = SourcingDocument::where('id',$sourcing_products[0]->document_id)->first();
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
        $percentage_images = SourcingProductImage::where(['sourcing_product_id'=>$sourcing_products[0]->id])->get()->groupBy('row');

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
        return view('sourcing_products.select_product',compact('branch','suppliers','user_role','categories','sourcing','damage_remark_types','exchange_rate','check_type', 'ref_no', 'percentage_images'));
    }

    // public function move_to_other_doc(Request $request)
    // {
    //     $selectedIDs = $request->selectedIDs;
    //     $ref_no = $request->ref_no;
    //     $product_image = SourcingProductImage::findOrFail($selectedIDs[0]);
    //     $sourcing_product = SourcingProduct::findOrFail($product_image->sourcing_product_id);
    //     $old_sourcing_document = SourcingDocument::findOrFail($sourcing_product->document_id);
    //     $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
    //     // dd($check_reference_no);
    //     if($check_reference_no < 2){
    //         $old_doc_no = $old_sourcing_document->document_no;
    //         $doc_no = $this->generate_doc_no($request->type, now(), $old_sourcing_document->branch_id);
    //         $doc_status = $request->type == 1 ? 2 : $old_sourcing_document->document_status;
    //         $cat_head_id = $request->type == 1 ? null : $old_sourcing_document->document_status;
    //         $cat_head_date = $request->type == 1 ? null : $old_sourcing_document->category_head_updated_datetime;
    //         $cat_head_remark = $request->type == 1 ? null : $old_sourcing_document->category_head_remark;

    //         $new_sourcings_document = $old_sourcing_document->copy(now(), $doc_no, $request->type, $doc_status, $cat_head_id, $cat_head_date, $cat_head_remark);

    //         $operation_id = $old_sourcing_document->operation_id;
    //         $branch_manager_id = $old_sourcing_document->branch_manager_id;
    //         $category_head_id = $old_sourcing_document->category_head_id;
    //         $message = "Your Document " . $old_sourcing_document->document_no . " is changed to " . $new_sourcings_document->document_no;
    //         // $aa = new DocumentNotification($message, (int)$document_id);
    //         $users = User::whereIn('id',[$operation_id,$branch_manager_id,$category_head_id])->get();

    //         foreach($users as $user){
    //             $type = 1;
    //             Notification::send($user, new DocumentNotification($message, $new_sourcings_document->id, $type));
    //         }

    //         //create reference no
    //         $reference_doc['new_reference_no'] = $new_sourcings_document->document_no;
    //         $reference_doc['old_reference_no'] = $old_doc_no;
    //         $reference_doc = ReferenceDocument::create($reference_doc);
    //         $product_id = $sourcing_product->id;
    //         $product = $sourcing_product->replicate();
    //         $product->document_id = $new_sourcings_document->id;
    //         $product->stock_quantity = null ;
    //         $product->save();
    //         $total = 0 ;
    //         foreach($selectedIDs as $id){
    //             $sourcing_product_image  = SourcingProductImage::findOrFail($id);
    //             // dd($id);
    //             $sourcing_product_image->doc_id = $new_sourcings_document->id ;
    //             $sourcing_product_image->sourcing_product_id = $product->id ;
    //             $sourcing_product_image->save();
    //             // dd($sourcing_product_image->seperate_qty);
    //             $total += $sourcing_product_image->seperate_qty;

    //         }
    //         // dd($total);
    //         $total_price = $total * $product->product_price;
    //         $product->stock_quantity = $total;
    //         $product->total_price = $total_price;
    //         $product->save();
    //         $old_product = SourcingProduct::findOrFail($product_id);
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

    //     return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_sourcings_document->document_no], 200);

    // }

    public function move_to_other_doc(Request $request)
    {
        $selectedIDs = $request->selectedIDs;
        // dd($selectedIDs);
        $ref_no = $request->ref_no;
        $product_image = SourcingProductImage::findOrFail($selectedIDs[0][0]);
        $sourcing_product = SourcingProduct::findOrFail($product_image->sourcing_product_id);
        $old_sourcing_document = SourcingDocument::findOrFail($sourcing_product->document_id);
        $check_reference_no = ReferenceDocument::where('old_reference_no', $ref_no)->orWhere('new_reference_no', $ref_no)->count();
        // dd($check_reference_no);
        if($check_reference_no < 2){
            $old_doc_no = $old_sourcing_document->document_no;
            $doc_no = $this->generate_doc_no($request->type, now(), $old_sourcing_document->branch_id);
            $doc_status = $request->type == 1 ? 2 : $old_sourcing_document->document_status;
            $cat_head_id = $request->type == 1 ? null : $old_sourcing_document->document_status;
            $cat_head_date = $request->type == 1 ? null : $old_sourcing_document->category_head_updated_datetime;
            $cat_head_remark = $request->type == 1 ? null : $old_sourcing_document->category_head_remark;

            $new_sourcings_document = $old_sourcing_document->copy(now(), $doc_no, $request->type, $doc_status, $cat_head_id, $cat_head_date, $cat_head_remark);
            // dd($new_sourcings_document);
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
            $product_id = $sourcing_product->id;
            $product = $sourcing_product->replicate();
            // dd($product);
            $product->document_id = $new_sourcings_document->id;
            $product->stock_quantity = null ;
            $product->save();
            // dd($product_id);
            $total = 0 ;
            // dd($selectedIDs);
            foreach($selectedIDs as $ids){
                foreach($ids as $id)
                {
                    $sourcing_product_image  = SourcingProductImage::findOrFail($id);
                    // dd($sourcing_product_image);
                    $sourcing_product_image->doc_id = $new_sourcings_document->id ;
                    $sourcing_product_image->sourcing_product_id = $product->id ;
                    $sourcing_product_image->save();
                    // dd($sourcing_product_image->seperate_qty);
                }
                $total += $sourcing_product_image->seperate_qty;

            }
            // dd($total);
            $total_price = $total * $product->product_price;
            $product->stock_quantity = $total;
            $product->total_price = $total_price;
            $product->save();

            $old_product = SourcingProduct::findOrFail($product_id);
            if(($old_product->stock_quantity - $total) == 0)
            {
                $old_product->forceDelete();
                return response()->json([ 'delete' => 'successfully','new_doc_no'=> $new_sourcings_document->document_no], 200);
            }
            else
            {
                $qty = ($old_product->stock_quantity - $total);
                // dd($qty);
                $tot_price = $qty * $old_product->product_price;
                $old_product->stock_quantity = $qty;
                $old_product->total_price = $tot_price;
                $old_product->save();
                // dd($old_product);
            }


        }
        else{
            return response()->json([ 'error' => 'more_two_times'], 200);

        }

        return response()->json([ 'success' => 'successfully','new_doc_no'=> $new_sourcings_document->document_no], 200);

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

}
