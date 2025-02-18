<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{ImportProductImage,ImportProduct,SourcingProductImage,SourcingProduct,SourcingDocument};
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PercentageController extends Controller
{
    public function logistic_percentage(Request $request)
    {

        // dd($request->all());
        if(!$request->row )
        {
            return back()->with('error', 'ပုံထည့်ရန် Add Row Buttonအားနှိပ်ပေးပါ။');
        }
        else{
            if(count($request->row)!=0)
        {
            if($request->doc_type=='Sourcing')
            {
                foreach($request->row as $row)
                {
                    $product_imges = 'media_link'.$row;
                    $seperate_qty  = 'seperate_qty'.$row;
                    // dd($seperate_qty);
                    for($i=0; $i<count($request[$product_imges]);$i++)
                    {
                       // dd($request[$product_imges][$i]->getClientOriginalName());
                        // $filename = 'per_' . auth()->id() . '_' . rand(0,9999) . '_' . $request[$product_imges][$i]->getClientOriginalExtension();
                        $filename = 'per_' . auth()->id() . '_' . rand(0,9999) . '_' . $request[$product_imges][$i]->getClientOriginalName();
                        // dd($filename);
                        try {
                            if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                            {
                                $aa = Storage::disk('ftp1')->put($filename, fopen($request[$product_imges][$i], 'r+'));
                            }else{
                                $aa = Storage::disk('ftp3')->put($filename, fopen($request[$product_imges][$i], 'r+'));
                            }

                            } catch (Exception $e) {
                            return 'Something went wrong with the connection ' . $e->getMessage();
                        }
                        $pro_percentage                          = new SourcingProductImage();
                        // dd($new_row);
                        $pro_percentage->doc_id                  = $request->doc_id;

                        $pro_percentage->sourcing_product_id     = $request->sourcing_product_id;
                        $pro_percentage->media_link              = $filename;
                        $pro_percentage->mer_percentage          = 0;
                        $pro_percentage->sourcing_percentage     = 0;
                        $pro_percentage->seperate_qty            = $request[$seperate_qty][0];
                        $pro_percentage->doc_type                = $request->doc_type;
                        $pro_percentage->row                     = $row;
                        $pro_percentage->discount_amount         = 0;
                        $pro_percentage->after_discount_amount   = 0;
                        $pro_percentage->save();
                        //  dd($pro_percentage);
                        // dd($request->sourcing_product_id);
                    }
                }
                return redirect()->route('sourcing_products.edit',$request->sourcing_product_id);
            }
            else{
                foreach($request->row as $row)
                {
                    // dd($request->all());
                    $product_imges = 'media_link'.$row;

                    $seperate_qty  = 'seperate_qty'.$row;

                    for($i=0; $i<count($request[$product_imges]);$i++)
                   {
                        // $filename = 'per_' . auth()->id() . '_' . rand(0,9999) . '_' . $request[$product_imges][$i]->getClientOriginalExtension();
                        $filename = 'per_' . auth()->id() . '_' . rand(0,9999) . '_' . $request[$product_imges][$i]->getClientOriginalName();
                        try {
                            if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
                            {
                                $aa = Storage::disk('ftp1')->put($filename, fopen($request[$product_imges][$i], 'r+'));
                            }else{
                                $aa = Storage::disk('ftp3')->put($filename, fopen($request[$product_imges][$i], 'r+'));
                            }

                            } catch (Exception $e) {
                            return 'Something went wrong with the connection ' . $e->getMessage();
                        }
                     $pro_percentage                        = new ImportProductImage();
                     $pro_percentage->doc_id                = $request->doc_id;
                     $pro_percentage->import_product_id     = $request->import_product_id;
                     $pro_percentage->media_link            = $filename;
                     $pro_percentage->mer_percentage        = 0;
                     $pro_percentage->log_percentage        = 0;
                     $pro_percentage->seperate_qty          = $request[$seperate_qty][0];
                     $pro_percentage->doc_type              = $request->doc_type;
                     $pro_percentage->row                   = $row;
                     $pro_percentage->save();
                    //  dd($pro_percentage);
                   }
                }
                return redirect()->route('import_products.edit',$request->import_product_id);
            }

        }
        }


    }

    public function img_delete($id)
    {
        ImportProductImage::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Success Deleted image',
            ]);
    }
    public function sourcing_img_delete($id)
    {
        SourcingProductImage::find($id)->delete();
        return response()->json([
            'success' => true,
            'message' => 'Success Deleted image',
            ]);
    }

    public function sourcing_img_update(Request $request, $id)
    {

        $percentage_ids = $request->percentage_id;
        $rows           = $request->row;
        $product_id     = $request->sourcing_product_id;
        $doc_id         = $id;
        $mer_percentage = $request->mer_percentage;
        $sourcing_percentage = $request->sourcing_percentage;
        if($mer_percentage!=null)
        {
            if(!in_array(null,$mer_percentage) )
        {
            $amount =0;
            $after_discount_amt = 0;
            $import_product = SourcingProduct::whereId($product_id)->first();
            for($i=0; $i<count($rows);$i++)
            {
                $data                       = [
                    'mer_percentage'        =>$mer_percentage[$i],
                ];

                DB::table('sourcing_product_images')->where(['row'=>$rows[$i],'sourcing_product_id'=>$product_id,'doc_id'=>$id])->update($data);

                $product_image = SourcingProductImage::where(['sourcing_product_id'=>$product_id,'doc_id'=>$id,'row'=>$rows[$i]])->first();
                // dd($product_image);
                $amount        += discount_amt($product_image,$import_product);
                // dd($amount);
                $after_discount_amt += after_discount_amt($product_image,$import_product);
            }
            $import_product->update(['damage_percentage'=>$amount,'percentage_amount'=>$after_discount_amt]);
            // dd($amount);
            // $after_discount_amt += after_discount_amt($p_img,$import_product);
            // $amount        += discount_amt($p_img,$import_product);
            // foreach($product_imges as $p_img)
            // {
            //
            //     $after_discount_amt += after_discount_amt($p_img,$import_product);

            // }


        }
        }

        if($sourcing_percentage!=null)
        {

            if(!in_array(null,$sourcing_percentage) )
        {
            for($i=0; $i<count($rows);$i++)
            {
                $data                       = [
                    'sourcing_percentage'        =>$sourcing_percentage[$i],
                ];

                DB::table('sourcing_product_images')->where(['row'=>$rows[$i],'sourcing_product_id'=>$product_id,'doc_id'=>$id])->update($data);

            }
            // $sourcing_img = SourcingProductImage::where(['row'=>$rows[$i],'sourcing_product_id'=>$product_id,'doc_id'=>$id])->get();
            // dd($sourcing_img);
            // return back();
        }
        }
        // dd('f');
        return redirect()->route('sourcing_products.edit',$product_id);
        // return back();
    }

    public function img_update(Request $request, $id)
    {
        // dd($request->all());
        $rows           = $request->row;
        $product_id     = $request->import_product_id;
        $doc_id         = $id;
        $mer_percentage = $request->mer_percentage;
        $log_percentage = $request->log_percentage;
        if($mer_percentage!=null)
        {
            if(!in_array(null,$mer_percentage) )
        {
            for($i=0; $i<count($rows);$i++)
            {
                $data                       = [
                    'mer_percentage'        =>$mer_percentage[$i],
                ];

                DB::table('import_product_images')->where(['row'=>$rows[$i],'import_product_id'=>$product_id,'doc_id'=>$id])->update($data);
                $product_imge = ImportProductImage::where(['import_product_id'=>$product_id,'doc_id'=>$id,'row'=>$rows[$i]])->first();
            }
            // $import_product = ImportProduct::whereId($product_id)->first();
            // dd($import_product);

            // dd($product_imges);
                $amount        += discount_amt($p_img,$import_product);
                // dd($amount);
                $after_discount_amt += after_discount_amt($p_img,$import_product);

            // dd($import);
            $import_product->update(['damage_percentage'=>$amount,'percentage_amount'=>$after_discount_amt]);
            // dd($import_product);
        }
        }
        // dd('hi');
        if($log_percentage!=null)
        {
            // dd('true');
            if(!in_array(null,$log_percentage) )
        {
            for($i=0; $i<count($rows);$i++)
            {
                $data                       = [
                    'log_percentage'        =>$log_percentage[$i],
                ];

                DB::table('import_product_images')->where(['row'=>$rows[$i],'import_product_id'=>$product_id,'doc_id'=>$id])->update($data);
            }
            return back();
        }
        }
        // dd('f');
        return back();
    }
    public function check_sourcing_percentage($id){
        $sourcing = SourcingDocument::find($id);
        $data = check_sourcing_percentage($sourcing);
       return response()->json($data, 200);
    }
    public function check_mer_percentage($id){
        $sourcing = SourcingDocument::find($id);
        $data = check_mer_percentage($sourcing);
       return response()->json($data, 200);
    }

    public function percentage_update($row,Request $request)
    {
        $doc_id = $request->doc_id;
        $product_id = $request->product_id;
        $mer_percentage = $request->mer_percentage;
        $import_product = ImportProduct::whereId($product_id)->first();
        $product_image=ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->first();
        $discount_amt = ($mer_percentage/100)*$product_image->seperate_qty*$import_product->product_price;
        $after_discount_amt = (($product_image->seperate_qty*$import_product->product_price) -$discount_amt);
        $update = ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->update(['mer_percentage'=>$mer_percentage,'discount_amount'=>$discount_amt,'after_discount_amount'=>$after_discount_amt]);
        // dd($discount_amt, $after_discount_amt);
        $response = ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->get();
        // dd($response);
        $amounts=0;
        $after_amounts = 0;
        $images =ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->select('row','discount_amount','after_discount_amount')->distinct()->get();
// dd($images);
        foreach($images as $image)
        {
            $amounts+=$image->discount_amount;
            $after_amounts += $image->after_discount_amount;
            // dd($image->discount_amount);
        }
        $import_product->update(['damage_percentage'=>$amounts,'percentage_amount'=>$after_amounts]);
        // dd($import_product);
        $data =[$response,$amounts,$after_amounts];
        return response()->json($data, 200);
    }
    public function log_percentage_update($row,Request $request)
    {

        $doc_id = $request->doc_id;
        $product_id = $request->product_id;
        $log_percentage = $request->log_percentage;
        $seperate_qty = $request->seperate_qty;
        // dd($seperate_qty);
        if($seperate_qty!=null && $log_percentage==null)
        {
            ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->update(['seperate_qty'=>$seperate_qty]);
        }
        else{
            ImportProductImage::where(['row'=>$row,'import_product_id'=>$product_id])->update(['log_percentage'=>$log_percentage]);
        }

        return response()->json($seperate_qty, 200);
    }

    public function sourcing_percentage_update($row,Request $request)
    {
        $doc_id = $request->doc_id;
        $product_id = $request->product_id;
        $mer_percentage = $request->mer_percentage;
        // dd($mer_percentage);
        $import_product = SourcingProduct::whereId($product_id)->first();
        $product_image=SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->first();
        // dd($row,$product_id,$doc_id);
        $discount_amt = (($mer_percentage/100)*$product_image->seperate_qty*$import_product->product_price);
        $after_discount_amt = (($product_image->seperate_qty*$import_product->product_price) -$discount_amt);
        // dd($discount_amt, $after_discount_amt);
        $update = SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->update(['mer_percentage'=>$mer_percentage,'discount_amount'=>$discount_amt,'after_discount_amount'=>$after_discount_amt]);
        // dd($discount_amt);
        $response = SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->get();
        $amounts=0;
        $after_amounts = 0;
        $images =SourcingProductImage::where('sourcing_product_id',$product_id)->select('row','discount_amount','after_discount_amount')->distinct()->get();

        foreach($images as $image)
        {
            $amounts+=$image->discount_amount;
            $after_amounts += $image->after_discount_amount;
        }
        // dd($after_amounts,$amounts);
        $import_product->update(['damage_percentage'=>$amounts,'percentage_amount'=>$after_amounts]);
        $data =[$response,$amounts,$after_amounts];
        return response()->json($data, 200);
    }
    public function sourcing_log_percentage_update($row,Request $request)
    {
        $doc_id = $request->doc_id;
        $product_id = $request->product_id;
        $log_percentage = $request->log_percentage;
        $import_product = SourcingProduct::whereId($product_id)->first();
        $seperate_qty = $request->seperate_qty;


        if($seperate_qty!=null && $log_percentage==null)
        {
            SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->update(['seperate_qty'=>$seperate_qty]);
            // dd('qty');
        }
        else{
            $image = SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id])->update(['sourcing_percentage'=>$log_percentage]);
            // dd(SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id])->get());
        }
        $total_damage_qty = total_damage_qty($import_product);
        // dd($total_damage_qty);
        // $update = SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->update(['sourcing_percentage'=>$log_percentage]);
        // // dd(SourcingProductImage::where(['row'=>$row,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->get());
        return response()->json($total_damage_qty, 200);
    }

    public function sourcing_qty_update($id,Request $request)
    {
        $qty = $request->qty;
        $doc_id = $request->doc_id;
        $product_id = $request->product_id;
        if($qty != null)
        {
            SourcingProductImage::where(['row'=>$id,'sourcing_product_id'=>$product_id,'doc_id'=>$doc_id])->update(['seperate_qty'=>$qty]);
        }
        return response()->json('success', 200);
    }

}
