<?php

namespace App\Http\Controllers;

use PDF as MPDF;
use Carbon\Carbon;
use App\Models\Branch;
use App\Models\Product;
use App\Models\Document;
use Illuminate\Http\Request;
use App\Models\DocumentStatus;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    protected function connection()
    {
        return new Product();
    }

    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request){
        // dd('yes');
        try {
            $checkProductCount = Product::where('document_id', $request->document_id)->count();
            if ($checkProductCount <= 20) {
                $filename = null;
                if ($request->product_attach_file){
                    $request->validate([
                        'product_attach_file' => 'required|max:4096|mimes:jpeg,jpg,png,pdf',
                    ]);
                    ////store other server///
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp')->delete($request->product_attach_file);
                        $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                        Storage::disk('ftp')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp2')->delete($request->product_attach_file);
                        $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                        Storage::disk('ftp2')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                    }

                    // File::delete(public_path('images/attachFile/' . $request->product_attach_file));
                    // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];
                    // $request->product_attach_file->move(public_path('images/attachFile'), $filename);
                }
                $request['operation_remark'] = $request->operation_remark;
                $request['operation_actual_quantity'] = $request->operation_actual_quantity;
                $request['merchandising_actual_quantity'] = $request->merchandising_actual_quantity;
                $request['operation_rg_out_actual_quantity'] = $request->operation_rg_out_actual_quantity;
                $request['operation_rg_in_actual_quantity'] = $request->operation_rg_in_actual_quantity;
                // dd($request->document_date);
                $products = Product::where('product_code_no', $request->product_code_no)->pluck('document_id');
                // dd($products);
                $currentDate = Carbon::now();
                $twoWeekAgo = Carbon::now()->subDays(14);
                $documents = Document::whereIn('id' , $products)
                            ->where('branch_id',$request->branch_id)
                            ->whereNotIn('document_status', [3,5,7,20,22])
                            ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
                            ->whereNull('deleted_at')
                            ->pluck('document_date')->toArray();
                // dd($documents);
                $allDatesWithinRange = collect($documents)->every(function ($date) use ($currentDate, $twoWeekAgo) {
                    return (new Carbon($date))->lessThan($currentDate) && (new Carbon($date))->greaterThan($twoWeekAgo);
                });
                $id= Document::whereIn('id' , $products)
                            ->where('branch_id',$request->branch_id)
                            ->whereNotIn('document_status', [3 , 5,7,20,22])
                            ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
                            ->whereNull('deleted_at')
                            ->pluck('id')->toArray();
                // dd($id);
                if(!empty($documents)){
                    $doc= Document::whereIn('id' , $products)
                                    ->where('branch_id',$request->branch_id)
                                    ->whereNotIn('document_status', [3 , 5,7,20,22])
                                    ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
                                    ->whereNull('deleted_at')
                                    ->pluck('document_no')->toArray();
                    $qty=  Product::whereIn('document_id' , $id)
                                    ->where('product_code_no' , $request->product_code_no)
                                    ->pluck('return_quantity')->toArray();
                    $total = 0;
                    foreach($qty as $item){
                        $total += $item;
                    }
                    $data= ['YES' , $doc ,$qty , $total] ;
                    if ($request->product_id == "") {
                        if($allDatesWithinRange){
                            return response()->json($data , 200);
                        }else{
                            Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                            return response()->json('NO');
                        }
                    } else {
                        if($allDatesWithinRange){
                            return response()->json($data , 200);
                        }else{
                            $product = Product::where('id', $request['product_id'])->first();
                            $old_file = $product->product_attach_file;
                            if($request->product_attach_file != null){
                                if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                                {
                                    Storage::disk('ftp')->delete($old_file);
                                }else{
                                    Storage::disk('ftp2')->delete($old_file);
                                }

                            }
                            $filename = $filename ?? $product->product_attach_file;
                            $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                            return response()->json('Update');
                        }

                    }
                }else {
                        if ($request->product_id == "") {
                                Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                                return response()->json('NO');
                        } else {
                                $product = Product::where('id', $request['product_id'])->first();

                                $old_file = $product->product_attach_file;
                                if($request->product_attach_file != null){
                                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                                    {
                                        Storage::disk('ftp')->delete($old_file);
                                    }else{
                                        Storage::disk('ftp2')->delete($old_file);
                                    }
                                }
                                $filename = $filename ?? $product->product_attach_file;
                                $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                                return response()->json('Update');
                        }

                }
        }
        else {
            return response()->json('Fail');
        }
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to Manage Product!');
        }

    }

    // click save => sweetalert()->route()
    public function storedata(Request $request)
    {
        try {

            $checkProductCount = Product::where('document_id', $request->document_id)->count();
            if ($checkProductCount <= 20) {
                $filename = null;
                if ($request->product_attach_file) {
                    $request->validate([
                        'product_attach_file' => 'required|max:4096|mimes:jpeg,jpg,png,pdf',
                    ]);
                    ////store other server///
                    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                    {
                        Storage::disk('ftp')->delete($request->product_attach_file);
                        $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                        Storage::disk('ftp')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                    }else{
                        Storage::disk('ftp2')->delete($request->product_attach_file);
                        $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                        Storage::disk('ftp2')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                    }

                    // File::delete(public_path('images/attachFile/' . $request->product_attach_file));
                    // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];
                    // $request->product_attach_file->move(public_path('images/attachFile'), $filename);
                }
                $request['operation_remark'] = $request->operation_remark;
                $request['operation_actual_quantity'] = $request->operation_actual_quantity;
                $request['merchandising_actual_quantity'] = $request->merchandising_actual_quantity;
                $request['operation_rg_out_actual_quantity'] = $request->operation_rg_out_actual_quantity;
                $request['operation_rg_in_actual_quantity'] = $request->operation_rg_in_actual_quantity;

                $products = Product::where('product_code_no', $request->product_code_no)->pluck('document_id');
                // $doc_date = $request->document_date;
                $currentDate = Carbon::now();
                $twoWeekAgo = Carbon::now()->subDays(14);
                $id= Document::whereIn('id' , $products)
                                ->where('branch_id',$request->branch_id)
                                ->whereNotIn('document_status', [3 , 5,7,20,22])
                                ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
                                ->whereNull('deleted_at')
                                ->pluck('id')->toArray();
                // dd($id);
                if ($request->product_id == "") {
                    Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                    return response()->json('NO');
                } else {
                        $product = Product::where('id', $request['product_id'])->first();
                        // dd($request->product_attach_file);
                        $old_file = $product->product_attach_file;
                        if($request->product_attach_file != null){
                            if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                            {
                                Storage::disk('ftp')->delete($old_file);
                            }else{
                                Storage::disk('ftp2')->delete($old_file);
                            }

                        }
                        $filename = $filename ?? $product->product_attach_file;
                        $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                        return response()->json('Update');

                }
            }
            return response()->json('Fail');
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to Manage Product!');
        }
    }

    // // store data
    // public function storeDatas(Request $request)
    // {
    //     try {
    //         // dd($request->all());
    //         $checkProductCount = Product::where('document_id', $request->document_id)->count();
    //         if ($checkProductCount <= 20) {
    //             $filename = null;
    //             if ($request->product_attach_file){
    //                 $request->validate([
    //                     'product_attach_file' => 'required|max:4096|mimes:jpeg,jpg,png,pdf',
    //                 ]);
    //                 ////store other server///
    //                 Storage::disk('ftp')->delete($request->product_attach_file);
    //                 $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
    //                 Storage::disk('ftp')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
    //                 // File::delete(public_path('images/attachFile/' . $request->product_attach_file));
    //                 // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];
    //                 // $request->product_attach_file->move(public_path('images/attachFile'), $filename);
    //             }
    //             $request['operation_remark'] = $request->operation_remark;
    //             $request['operation_actual_quantity'] = $request->operation_actual_quantity;
    //             $request['merchandising_actual_quantity'] = $request->merchandising_actual_quantity;
    //             $request['operation_rg_out_actual_quantity'] = $request->operation_rg_out_actual_quantity;
    //             $request['operation_rg_in_actual_quantity'] = $request->operation_rg_in_actual_quantity;
    //             // dd($request->document_date);
    //             $products = Product::where('product_code_no', $request->product_code_no)->pluck('document_id');
    //             // dd($products);
    //             $currentDate = Carbon::now();
    //             $twoWeekAgo = Carbon::now()->subDays(14);
    //             $documents = Document::whereIn('id' , $products)
    //                         ->where('branch_id',$request->branch_id)
    //                         ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                         ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                         ->whereNull('deleted_at')
    //                         ->pluck('document_date')->toArray();
    //             $allDatesWithinRange = collect($documents)->every(function ($date) use ($currentDate, $twoWeekAgo) {
    //                 return (new Carbon($date))->lessThan($currentDate) && (new Carbon($date))->greaterThan($twoWeekAgo);
    //             });
    //             $id= Document::whereIn('id' , $products)
    //                         ->where('branch_id',$request->branch_id)
    //                         ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                         ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                         ->whereNull('deleted_at')
    //                         ->pluck('id')->toArray();
    //             // dd($id);
    //             if(!empty($documents)){
    //                 $doc= Document::whereIn('id' , $products)
    //                                 ->where('branch_id',$request->branch_id)
    //                                 ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                                 ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                                 ->whereNull('deleted_at')
    //                                 ->pluck('document_no')->toArray();
    //                 $qty=  Product::whereIn('document_id' , $id)
    //                                 ->where('product_code_no' , $request->product_code_no)
    //                                 ->pluck('return_quantity')->toArray();
    //                 // dd($allDatesWithinRange);
    //                 // dd($qty);
    //                 $data= ['YES' , $doc ,$qty];

    //                     $return_qty = Product::whereIn('document_id' , $id)->where('product_code_no' , $request->product_code_no)->pluck('return_quantity')->toArray();
    //                     $total=0;
    //                     foreach($return_qty as $qty){
    //                         $total += $qty;
    //                     }
    //                     // dd($total);
    //                     if ($request->product_id == "") {
    //                         $qty = $request->stock_quantity  - ($total + $request->return_quantity) ;
    //                         if($qty >= 0){
    //                             if($allDatesWithinRange){
    //                                 return response()->json($data , 200);
    //                             }else{
    //                                 Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
    //                                 return response()->json('NO');
    //                             }
    //                         }else {
    //                             $doc= Document::whereIn('id' , $products)
    //                                             ->where('branch_id',$request->branch_id)
    //                                             ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                                             ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                                             ->whereNull('deleted_at')
    //                                             ->pluck('document_no')->toArray();
    //                             $qty=  Product::whereIn('document_id' , $id)
    //                                             ->where('product_code_no' , $request->product_code_no)
    //                                             ->pluck('return_quantity')->toArray();
    //                             $ava_qty = $request->stock_quantity - $total;
    //                             $data= ['QTY' , $doc ,$qty , $ava_qty];
    //                             return response()->json($data , 200);
    //                         }
    //                     } else {
    //                         $origin_qty = Product::where('id', $request['product_id'])->pluck('return_quantity')->toArray();
    //                         $total1 = $total - ($origin_qty[0]) ;
    //                         $qty = $request->stock_quantity  - ($total1 + $request->return_quantity);
    //                         if($qty >= 0){
    //                             if($allDatesWithinRange){
    //                                 return response()->json($data , 200);
    //                             }else{
    //                                 $product = Product::where('id', $request['product_id'])->first();
    //                                 $old_file = $product->product_attach_file;
    //                                 Storage::disk('ftp')->delete($old_file);
    //                                 $filename = $filename ?? $product->product_attach_file;
    //                                 $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
    //                                 return response()->json('Update');
    //                             }
    //                         }else {
    //                             $doc= Document::whereIn('id' , $products)
    //                                         ->where('branch_id',$request->branch_id)
    //                                         ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                                         ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                                         ->whereNull('deleted_at')
    //                                         ->pluck('document_no')->toArray();
    //                             $qty=  Product::whereIn('document_id' , $id)
    //                                             ->where('product_code_no' , $request->product_code_no)
    //                                             ->pluck('return_quantity')->toArray();
    //                             $ava_qty = $request->stock_quantity - $total1;
    //                             $data= ['QTY' , $doc ,$qty , $ava_qty];
    //                             return response()->json($data , 200);
    //                         }

    //                         // File::delete(public_path('images/attachFile/' . $product->product_attach_file));
    //                         // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];

    //                         // $request->product_attach_file->move(public_path('images/attachFile'), $filename);

    //                 }
    //             }else {
    //                 $return_qty = Product::whereIn('document_id' , $id)->where('product_code_no' , $request->product_code_no)->pluck('return_quantity')->toArray();
    //                 $total=0;
    //                 foreach($return_qty as $qty){
    //                     $total += $qty;
    //                 }
    //                     // dd($return_qty);
    //                     if ($request->product_id == "") {
    //                         $qty = $request->stock_quantity  - ($total + $request->return_quantity) ;

    //                         if($qty >= 0){
    //                             Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
    //                             return response()->json('NO');
    //                         }else {
    //                             $doc= Document::whereIn('id' , $products)
    //                                             ->where('branch_id',$request->branch_id)
    //                                             ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                                             ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                                             ->whereNull('deleted_at')
    //                                             ->pluck('document_no')->toArray();
    //                             $qty=  Product::whereIn('document_id' , $id)
    //                                             ->where('product_code_no' , $request->product_code_no)
    //                                             ->pluck('return_quantity')->toArray();
    //                             $ava_qty = $request->stock_quantity - $total;
    //                             $data= ['QTY' , $doc ,$qty , $ava_qty];
    //                             return response()->json($data , 200);
    //                         }
    //                     } else {
    //                         $origin_qty = Product::where('id', $request['product_id'])->pluck('return_quantity')->toArray();
    //                         $total1 = $total - ($origin_qty[0]) ;
    //                         $qty = $request->stock_quantity  - ($total1 + $request->return_quantity);
    //                         if($qty >= 0){
    //                             $product = Product::where('id', $request['product_id'])->first();
    //                             $old_file = $product->product_attach_file;
    //                             Storage::disk('ftp')->delete($old_file);
    //                             $filename = $filename ?? $product->product_attach_file;
    //                             $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
    //                             return response()->json('Update');
    //                         }else {
    //                            $doc= Document::whereIn('id' , $products)
    //                                             ->where('branch_id',$request->branch_id)
    //                                             ->whereNotIn('document_status', [3 , 5,7,20,22])
    //                                             ->whereBetween('document_date', [$twoWeekAgo, $currentDate])
    //                                             ->whereNull('deleted_at')
    //                                             ->pluck('document_no')->toArray();
    //                             $qty=  Product::whereIn('document_id' , $id)
    //                                             ->where('product_code_no' , $request->product_code_no)
    //                                             ->pluck('return_quantity')->toArray();
    //                             $ava_qty = $request->stock_quantity - $total;
    //                             $data= ['QTY' , $doc ,$qty , $ava_qty];
    //                             return response()->json($data , 200);
    //                         }

    //                         // File::delete(public_path('images/attachFile/' . $product->product_attach_file));
    //                         // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];

    //                         // $request->product_attach_file->move(public_path('images/attachFile'), $filename);
    //                     }

    //             }
    //     }
    //     else {
    //         return response()->json('Fail');
    //     }
    //     } catch (\Exception$e) {
    //         Log::debug($e->getMessage());
    //         return redirect()
    //             ->intended(route("documents.index"))
    //             ->with('error', 'Fail to Manage Product!');
    //     }
    // }

    // branch
    public function branchstore(Request $request)
    {
        // try {
        // dd('hiiii');
            if($request->each_row_div){
                $arr = array_filter($request->each_row_div,function($e){
                    return $e > 0 ;
                });
                $final_arr = array_values($arr);
                // dd($request->all());
                $product = Product::where('id', $request['product_id'])->first();
                // dd($final_arr);
                for($i = 0 ; $i < count($final_arr) ; $i++){
                    $item = new Product;
                    $item->document_id = $product->document_id;
                    $item->product_code_no = $product->product_code_no;
                    $item->product_name = $product->product_name;
                    $item->product_unit = $product->product_unit;
                    $item->stock_quantity = $product->stock_quantity;
                    $item->return_quantity = $final_arr[$i];
                    $item->operation_actual_quantity = $final_arr[$i];
                    $item->merchandising_actual_quantity = $final_arr[$i];
                    $item->merchandising_actual_quantity = $final_arr[$i];
                    $item->save();
                }
                // dd('yes');
                $old_file = $product->product_attach_file;
                if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                {
                    Storage::disk('ftp')->delete($old_file);
                }else{
                    Storage::disk('ftp2')->delete($old_file);
                }

                Product::find($product->id)->forceDelete();
                return back()->with('success','Product Split Success');
            }else{
                // dd('hi');
                $checkProductCount = Product::where('document_id', $request->document_id)->count();
                if ($checkProductCount <= 40) {
                    $filename = null;
                    if ($request->product_attach_file) {
                        // dd("yes");
                        $request->validate([
                            'product_attach_file' => 'required|max:4096|mimes:jpeg,jpg,png,pdf',
                        ]);
                        ////store other server///
                        if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                        {
                            Storage::disk('ftp')->delete($request->product_attach_file);
                            $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                            Storage::disk('ftp')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                        }else{
                            Storage::disk('ftp2')->delete($request->product_attach_file);
                            $filename = 'op_' . auth()->id() . '_' . time() . '_'.$_FILES['product_attach_file']['name'];
                            Storage::disk('ftp2')->put($filename, fopen($request->file('product_attach_file'), 'r+'));
                        }

                        // File::delete(public_path('images/attachFile/' . $request->product_attach_file));
                        // $filename = 'op_' . auth()->id() . '_' . time() . '_' . $_FILES['product_attach_file']['name'];
                        // $request->product_attach_file->move(public_path('images/attachFile'), $filename);
                    }
                    // dd('nooooo');
                    $request['operation_remark'] = $request->operation_remark;
                    $request['operation_actual_quantity'] = $request->operation_actual_quantity;
                    $request['merchandising_actual_quantity'] = $request->merchandising_actual_quantity;
                    $request['operation_rg_out_actual_quantity'] = $request->operation_rg_out_actual_quantity;
                    $request['operation_rg_in_actual_quantity'] = $request->operation_rg_in_actual_quantity;
                    $request['vat_no_vat'] = $request->l_to_l_vat_novat;

                    if ($request->product_id != "") {
                        $product = Product::where('id', $request['product_id'])->first();
                        $product = Product::where('id', $request['product_id'])->first();
                        if ($product) {
                            $avg_cost = $request['cost'];
                            $product->avg_cost = $avg_cost;

                            $rg = $product->rg_out_doc_no;

                            // Calculate total based on operation_rg_in_actual_quantity or operation_rg_out_actual_quantity
                            if (is_numeric($request['operation_rg_in_actual_quantity']) && $avg_cost) {
                                $total = $avg_cost * $request['operation_rg_in_actual_quantity'];
                                $request['avg_cost_total'] = $total;
                            } elseif (is_numeric($request['operation_rg_out_actual_quantity']) && $avg_cost) {
                                $total = $avg_cost * $request['operation_rg_out_actual_quantity'];
                                $request['avg_cost_total'] = $total;
                            }

                            // Handle invoice_no validation
                            if ($request->invoice_no && substr($rg, 0, 2) != 'RG') {
                                $validator = Validator::make($request->all(), [
                                    'invoice_no' => 'max:30'
                                ]);
                                if ($validator->fails()) {
                                    return back()->withErrors($validator)->withInput();
                                }
                                $request['invoice'] = $request->invoice_no;
                            }

                            // Handle file name assignment
                            $filename = $filename ?? $product->product_attach_file;
                            $product->update($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                        } else {
                            return back()->with('error', 'Product not found.');
                        }
                    } else {
                        // Handle product creation logic if product_id is empty
                        Product::create($request->except(['product_attach_file']) + ['product_attach_file' => $filename]);
                    }

                    return redirect()->route('documents.edit', $request->document_id)->with('success', 'Product successfully added!');
                }

                return redirect()->route('documents.edit', $request->document_id)->with('error', 'Please add only 20 products to one document!');
            }
        }

    public function show($id)
    {
        // try {

            $product = Product::withTrashed()->find($id);
            $product_code = $product->product_code_no;
            $doc_id = $product->document_id;
            $data = Document::where('id',$doc_id)->first();
            $branch_code = $data->branches->branch_code;

            if($product->avg_cost == null){


                $doc_no = $product->rg_out_doc_no;
                $qty = $product->operation_rg_out_actual_quantity;



                // dd('yes');

            // $conn      = DB::connection('pgsql2');
                // $avg_cost  = $conn->select("
                // select icost::numeric(19,2) from gl.istockcard where brchcode = '$branch_code' and docuno = '$doc_no' and productcode = '$product_code'
                // ");
                // $avg_cost = $avg_cost[0]->icost;
                // $total_cost = number_format($avg_cost * $qty, 2, '.', '');
                // $product->avg_cost_total = $total_cost;
                // $product->avg_cost = $avg_cost;

            }
            $conn = DB::connection('pgsql2');

            // $rg_data = $conn->select("");
            $get_doc_no_query = $conn->select("select aa.productcode,aa.docuno as receive_no,(aa.goodamnt/aa.goodqty)::numeric(19,2) as cost,aa.brchcode ,aa.docudate
            from inventory.stc_stockcard aa,
                (
                select productcode,brchcode,max(docudate) docudate
                from inventory.stc_stockcard
                where brchcode= '$branch_code'
                and docutype='307'
                and productcode= '$product_code'
                group by productcode,brchcode
                )bb
            where aa.productcode=bb.productcode and aa.brchcode=bb.brchcode
            and aa.docudate=bb.docudate
            and aa.productcode='$product_code'
            and docutype='307'  ");
            if (!empty($get_doc_no_query)) {
                $first_rg_out_doc_no = $get_doc_no_query[0]->receive_no;
            } else {
                $first_rg_out_doc_no = "";
            }


            $stock = $conn->select("select

                inventory.stock_poerp.sum as stock_sum,
                master_data.master_product_unit.product_unit_code as product_unit
                from master_data.master_product
                left join master_data.master_product_unit on master_data.master_product.main_product_unit_id = master_data.master_product_unit.product_unit_id
                left join master_data.master_product_multiprice on master_data.master_product.product_code = master_data.master_product_multiprice.product_code
                and master_data.master_product.main_product_unit_id = master_data.master_product_multiprice.product_unit_id
                left join inventory.stock_poerp on master_data.master_product.product_code = inventory.stock_poerp.productcode
                left join master_data.master_product_brand on master_data.master_product.product_brand_id = master_data.master_product_brand.product_brand_id
                left join master_data.master_branch on master_data.master_branch.branch_id = master_data.master_product_multiprice.branch_id
                and master_data.master_branch.branch_code = inventory.stock_poerp.brchcode
                where master_data.master_branch.branch_code = '$branch_code'
                ---and inventory.stock_poerp.sum != 0
                and master_data.master_product.inactive = 'A'
                and master_data.master_product_multiprice.barcode_code = '$product_code'");
                // logger($stock);
                if($stock){

                    Product::withTrashed()->find($id)->update([
                        'stock_quantity' => number_format($stock[0]->stock_sum,2,'.','')
                    ]);
                }
                $product =  Product::withTrashed()->find($id);
                $product->first_rg_out_doc_no = $first_rg_out_doc_no;
            return $product;
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to show Product!');
        // }
    }

    public function destroy(Product $product)
    {
        try {
            $product->delete();
            $product->delete_by = auth()->user()->id;
            $product->save();
            return response()->json([
                'success' => 'Product deleted successfully!',
            ]);
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to delete Product!');
        }
    }

    public function restore($id)
    {
        try {
            $product = Product::withTrashed()->find($id);
            $product->restore();
            return response()->json([
                'success' => 'Product restored successfully!',
            ]);
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to restore Product!');
        }
    }

    protected function get_product_by_id($id, $branch_code)
    {
        // try {
        $branch_id = Branch::where('branch_code',$branch_code)->first()->branch_id;
        $db_ext = DB::connection('pgsql2');
        // $spr = $db_ext->table('master_data.master_product')
        // ->select('inventory.stock_poerp.brchcode as branch_code','master_data.master_product.product_code',
        // 'master_data.master_product.product_name1 as product_name', 'master_data.master_product_multiprice.product_price1 as product_price',
        // 'inventory.stock_poerp.sum as stock_sum',
        // 'master_data.master_product_unit.product_unit_code as product_unit',
        // )
        // ->join('master_data.master_product_unit','master_data.master_product.main_product_unit_id','master_data.master_product_unit.product_unit_id')
        // ->join('master_data.master_product_multiprice','master_data.master_product.product_code','master_data.master_product_multiprice.product_code')
        // ->join('inventory.stock_poerp','master_data.master_product.product_code','inventory.stock_poerp.productcode')
        // ->where('master_data.master_product.product_code',$id)
        // ->where('master_data.master_product.inactive','A')
        // ->where('inventory.stock_poerp.brchcode',$branch_code)
        // ->where('master_data.master_product_multiprice.branch_id',$branch_id)
        // ->where('inventory.stock_poerp.sum','!=','0')
        // ->get();

        //   $spr = $db_ext->select("select
        //             inventory.stock_poerp.brchcode as branch_code,master_data.master_product.product_code,
        //             master_data.master_product.product_name1 as product_name,
        //             master_data.master_product_brand.product_brand_name,
        //             master_data.master_product_multiprice.product_price1 as product_price,
        //             inventory.stock_poerp.sum as stock_sum,
        //             master_data.master_product_unit.product_unit_code as product_unit
        //             from master_data.master_product
        //             left join master_data.master_product_unit on master_data.master_product.main_product_unit_id = master_data.master_product_unit.product_unit_id
        //             left join master_data.master_product_multiprice on master_data.master_product.product_code = master_data.master_product_multiprice.product_code
        //             and master_data.master_product.main_product_unit_id = master_data.master_product_multiprice.product_unit_id
        //             left join inventory.stock_poerp
        //             on master_data.master_product.product_code = inventory.stock_poerp.productcode
        //             left join master_data.master_product_brand
        //             on master_data.master_product.product_brand_id = master_data.master_product_brand.product_brand_id
        //             left join master_data.master_branch
        //             on master_data.master_branch.branch_id = master_data.master_product_multiprice.branch_id
        //             and master_data.master_branch.branch_code = inventory.stock_poerp.brchcode
        //             where master_data.master_branch.branch_code = '$branch_code'
        //             ---and inventory.stock_poerp.sum != 0
        //             and master_data.master_product.inactive = 'A'
        //             and master_data.master_product_multiprice.product_code = '$id'");


          $spr = $db_ext->select("select
                    inventory.stock_poerp.brchcode as branch_code,master_data.master_product.product_code,
                    master_data.master_product.product_name1 as product_name,
                    master_data.master_product_brand.product_brand_name,
                    case
                    when master_data.master_product_multiprice.product_price1 != 0 then master_data.master_product_multiprice.product_price1
                    when master_data.master_product_multiprice.product_price1 = 0 then (select product_price1 from master_data.master_product_multiprice where branch_id = 1 and barcode_code= '$id')
                    end as product_price,
                    inventory.stock_poerp.sum as stock_sum,
                    master_data.master_product_unit.product_unit_code as product_unit
                    from master_data.master_product
                    left join master_data.master_product_unit on master_data.master_product.main_product_unit_id = master_data.master_product_unit.product_unit_id
                    left join master_data.master_product_multiprice on master_data.master_product.product_code = master_data.master_product_multiprice.product_code
                    and master_data.master_product.main_product_unit_id = master_data.master_product_multiprice.product_unit_id
                    left join inventory.stock_poerp on master_data.master_product.product_code = inventory.stock_poerp.productcode
                    left join master_data.master_product_brand on master_data.master_product.product_brand_id = master_data.master_product_brand.product_brand_id
                    left join master_data.master_branch on master_data.master_branch.branch_id = master_data.master_product_multiprice.branch_id
                    and master_data.master_branch.branch_code = inventory.stock_poerp.brchcode
                    where master_data.master_branch.branch_code = '$branch_code'
                    ---and inventory.stock_poerp.sum != 0
                    and master_data.master_product.inactive = 'A'
                    and master_data.master_product_multiprice.barcode_code = '$id'");
        if ($spr) {
            return response()->json(['data' => $spr[0]], 200);
        } else {
            return response()->json(null, 200);
        }
        // } catch (\Exception$e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("documents.index"))
        //         ->with('error', 'Fail to get Products!');
        // }
    }

    public function product_list_by_document(Request $request)
    {
        try {
            $document_id = (!empty($_GET["document_id"])) ? ($_GET["document_id"]) : ('');
            $result = $this->connection()->where('document_id', $document_id)->with('document')->orderby('id')->withTrashed()->get();
            return DataTables::of($result)
                ->editColumn('product_code_no', function ($data) {
                    if($data->deleted_at){
                        return $data->product_code_no ? $data->product_code_no.',,1' : '';

                    }else{
                        return $data->product_code_no ? $data->product_code_no : '';
                    }
                })
                ->editColumn('product_name', function ($data) {
                    if($data->deleted_at){
                        return $data->product_name ? $data->product_name.',,1' : '';
                    }else{
                        return $data->product_name ? $data->product_name : '';
                    }
                })
                ->editColumn('product_unit', function ($data) {
                    if($data->deleted_at){
                        return $data->product_unit ? $data->product_unit.',,1' : '';
                    }else{
                        return $data->product_unit ? $data->product_unit : '';
                    }
                })
                ->editColumn('return_quantity', function ($data) {
                    if($data->deleted_at){
                        return $data->return_quantity.',,1';
                    }else{
                        return $data->return_quantity;
                    }
                })
                ->editColumn('operation_actual_quantity', function ($data) {
                    if($data->deleted_at){
                        return $data->operation_actual_quantity.',,1';
                    }else{
                        return $data->operation_actual_quantity;
                    }
                })
                ->editColumn('merchandising_actual_quantity', function ($data) {
                    if($data->deleted_at){
                        return $data->merchandising_actual_quantity.',,1';
                    }else{
                        return $data->merchandising_actual_quantity;
                    }

                })
                ->editColumn('operation_rg_out_actual_quantity', function ($data) {
                    if($data->deleted_at){
                        return $data->operation_rg_out_actual_quantity.',,1';
                }else{
                        return $data->operation_rg_out_actual_quantity;
                    }
                })
                ->editColumn('operation_rg_in_actual_quantity', function ($data) {
                    if($data->deleted_at){
                        // return '0'.',,1'.',,'.$data->operation_rg_in_actual_quantity;
                        return $data->operation_rg_in_actual_quantity.',,1';
                    }else{
                        return $data->operation_rg_in_actual_quantity;
                    }

                })
                ->editColumn('cost', function ($data) {
                    if($data->deleted_at){
                        return '0'.',,1'.',,'.$data->avg_cost;
                    }else{
                        return $data->avg_cost;
                    }

                })
                ->editColumn('total_cost', function ($data) {
                    if($data->deleted_at){
                        return $data->operation_rg_out_actual_quantity * $data->avg_cost;
                    }else{

                        return $data->operation_rg_out_actual_quantity * $data->avg_cost;

                    }

                })
                ->editColumn('db_total_cost', function ($data) {
                    if($data->deleted_at){
                        return $data->operation_rg_in_actual_quantity * $data->avg_cost;
                    }else{
                        return $data->operation_rg_in_actual_quantity * $data->avg_cost;
                    }

                })
                ->addColumn('action', function ($data) {
                    if($data->deleted_at){
                        return $data->document->document_status.',,24';
                    }else{
                        return $data->document->document_status;
                    }

                })
                ->rawColumns(['action', 'operation', 'branch_manager',
                    'category_head', 'merchandising_manager', 'operation_rg_out',
                    'account_cn', 'operation_rg_in', 'account_db'])
                ->addIndexColumn()
                ->make(true);
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to get Products!');
        }
    }

    public function view_product_attach_file($product_id)
    {
        // dd('yes');
        try {
            $product = Product::where('id', $product_id)->first();
            $user_name = Auth::user()->name;
            // dd($product->product_attach_file);
            // Delete File
            $data = File::deleteDirectory(public_path('storage'));
            // Get File form ftp
            if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
            {
                $ftp_file = Storage::disk('ftp')->get($product->product_attach_file);
            }else{
                try {
                    $ftp_file = Storage::disk('ftp2')->get($product->product_attach_file);
                } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                    try {
                        $ftp_file = Storage::disk('ftp')->get($product->product_attach_file);
                    } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                        $ftp_file = null;
                    }
                }
            }
            // dd($ftp_file);
            // dd("hello");
            // Copy to public storage
            Storage::disk('public')->put($product->product_attach_file, $ftp_file);

            if (substr($product->product_attach_file, -3) == 'pdf' || substr($product->product_attach_file, -3) == 'PDF') {
                return response()->file(public_path('storage/' . $product->product_attach_file));
            }
            $pdf = MPDF::loadView('products.view_product_attach_file', compact('product', 'user_name'));

            return $pdf->stream($product->product_code_no . "_attached_File.pdf", array("Attachment" => false));
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to get Product Attach File!');
        }
    }

    public function rg_check(Request $request){
        $rg_no = strtoupper($request->data);
        // logger($request->all());
        $doc_id = $request->doc_id;
        $data = Document::where('id',$doc_id)->first();
        $products = Product::find($request->id);
        $product_code = $products->product_code_no;
        $qty = $products->operation_rg_out_actual_quantity;
        $branch_code = (string)$data->branches->branch_code;

        $conn           = DB::connection('pgsql2');
        // $rg_data  = $conn->select("
        //         select approve_price,receive_no from purchaseorder.receive_dt
        //         where receive_no = '$rg_no' and product_code = '$product_code'
        //     ");

        $rg_data = $conn->select("
           select receive_no as doc_no, approve_price as cost from purchaseorder.receive_dt
            where receive_no = '$rg_no' and product_code = '$product_code'

            union all

            select transferdocno as doc_no, 0::numeric as cost from inventory.trs_transferinhd hd
            inner join inventory.trs_transferindt dt on dt.transferid = hd.transferid
            where hd.transferdocno = '$rg_no' and dt.productcode = '$product_code'

			union all

			select adj_hd.just_docuno as doc_no,cost_unit as cost from inventory.just_stock_hd adj_hd
			inner join inventory.just_stock_dt adj_dt on adj_hd.just_docuno=adj_dt.just_docuno
			where adj_hd.just_docuno='$rg_no' and adj_dt.barcode='$product_code'
        ");

        $rg_data = $rg_data[0];
        // dd($rg_data);
        $inv = $conn->select("
            select invoiceno
            from purchaseorder.vcinvoicehd
            where receiveno = '$rg_no'
        ");
        // $inv = $inv[0]->invoice_no ?? '';
        $inv = $inv[0]->invoiceno ?? '';
                // $rg
        if( $rg_data){
            // dd($avg_cost.'_'.$total_cost.'_'.$qty);
            $product = Product::find($products->id);
            if(substr($rg_no,0,2) == 'RG' || substr($rg_no,0,2) == 'IC' ){
                $avg_cost = (float)str_replace(['฿',','],'',$rg_data->cost);
                $total_cost = (float)number_format($avg_cost * $qty, 2, '.', '');
                $product->avg_cost_total = $total_cost;
                $product->avg_cost = $avg_cost;
            }
            if (substr($rg_no, 0, 2) == 'RG') {
                $product->invoice = $inv;
            } elseif (substr($rg_no, 0, 2) == 'IC') {
                $product->invoice = $rg_data->doc_no;
            }
            // else{
            //     $product->invoice = $rg_data->doc_no;
            // }
            $product->rg_out_doc_no = $rg_data->doc_no;
            $product->save();
            return response()->json([
                'rg_doc'    =>  $rg_data->doc_no,
                'avg_cost'  =>  $avg_cost ?? 0,
                'invoice'  =>  $inv,
                'total_cost'=>  $total_cost ?? 0
            ],200);
        }else{
            return response()->json(['msg'=>'RG DOC no Not Found'],404);
        }
    }

    public static function imagenABase64($ruta_relativa_al_public)
    {
        try {
            $path = $ruta_relativa_al_public;
            $type = pathinfo($path, PATHINFO_EXTENSION);
            $data = File::get($path);

            $base64 = "";
            if ($type == "svg") {
                $base64 = "data:image/svg+xml;base64," . base64_encode($data);
            } else {
                $base64 = "data:image/" . $type . ";base64," . base64_encode($data);
            }
            return $base64;
        } catch (\Exception$e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("documents.index"))
                ->with('error', 'Fail to Open File!');
        }
    }

    public function count_total_cost(Request $request)
    {
        $product = $this->connection()->where('id',$request->id)->first();
        if($product->avg_cost > 0 && $product->stock_quantity >= $request->val)
        {
            $total = $product->avg_cost * $request->val;
            $product->update(['avg_cost_total'=>$total,'operation_rg_out_actual_quantity'=>$request->val]);
            return response()->json(['total'=>$total],200);
        }
    }
}
