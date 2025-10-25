<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppraisalCycle;
use App\Models\PasswordEncode;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\PasswordEncodeImport;

class PasswordEncodesController extends Controller
{
    public function index(){
        $apprasialcycles = AppraisalCycle::all();
        $passwordencodes = PasswordEncode::paginate(10);

        return view("passwordencodes.index",compact("apprasialcycles","passwordencodes"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "appraisal_cycle_id"=> "required",
            "remark" => "required",
            'file' => 'required|mimes:xls,xlsx',
        ]);

        \DB::beginTransaction();

        try {

            $user = Auth::user();
            $user_id = $user->id;

            $passwordencode = new PasswordEncode();
            $passwordencode->appraisal_cycle_id = $request["appraisal_cycle_id"];
            $passwordencode->remark = $request["remark"];
            $passwordencode->user_id = $user_id;
            $passwordencode->status_id = 1;


            $file = $request->file('file');
            Excel::import(new PasswordEncodeImport, $file);

            // Single Image Upload
            if(file_exists($request["file"])){
                $file = $request["file"];
                $fname = $file->getClientOriginalName();
                $imagenewname = uniqid($user_id).$passwordencode['id'].$fname;
                // $file->move(public_path("roles/img"),$imagenewname);
                $file->move(public_path("assets/img/passwordencodes"),$imagenewname);
                
                $filepath = "assets/img/passwordencodes/".$imagenewname;
                $passwordencode->image = $filepath;
            }    
            $passwordencode->save();

            \DB::commit();
            return redirect(route("passwordencodes.index"))->with('success',"Employee excel imported successfully");

        }catch (ExcelImportValidationException $e) {
            // If validation fails, show the error message to the user
            Log::info($e);
            \DB::rollback();
            return redirect(route("passwordencodes.index"))->with('validation_errors', $e->getMessage());
        } catch (\Exception $e) {
            Log::info($e);
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect(route("passwordencodes.index"))->with('error', "System Error:".$e->getMessage());
        }
    }
}
