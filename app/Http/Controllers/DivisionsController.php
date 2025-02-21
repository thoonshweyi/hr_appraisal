<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\DivisionImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;


class DivisionsController extends Controller
{
    public function index(){

        $divisions = Division::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("divisions.index",compact("divisions","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:dept_groups",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $division = new Division();
       $division->name = $request["name"];
       $division->slug = Str::slug($request["name"]);
       $division->status_id = $request["status_id"];
       $division->user_id = $user_id;
       $division->save();
       return redirect(route("divisions.index"))->with('success',"Division created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:dept_groups,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $division = Division::findOrFail($id);
        $division->name = $request["edit_name"];
        $division->slug = Str::slug($request["edit_name"]);
        $division->status_id = $request["edit_status_id"];
        $division->user_id = $user_id;
        $division->save();
        return redirect(route("divisions.index"))->with('success',"Division updated successfully");
    }

    public function destroy(string $id)
    {
        $division = Division::findOrFail($id);
        $division->delete();
        return redirect()->back()->with('success',"Division deleted successfully");
    }

    public function changestatus(Request $request){
        $division = Division::findOrFail($request["id"]);
        $division->status_id = $request["status_id"];
        $division->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }


   public function excel_import(Request $request)
   {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:2048',
        ]);


          // Multi Images Upload
        //   if($request->hasFile('file')){
        //     // dd('hay');
        //     // foreach($request->file("file") as $image){
        //     //     Excel::import(new DivisionImport, $request->file('file'));
        //     // }
        //     Excel::import(new DivisionImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new DivisionImport, $file);

            \DB::commit();
            return redirect(route("divisions.index"))->with('success',"Division excel imported successfully");

        }catch (ExcelImportValidationException $e) {
            // If validation fails, show the error message to the user
            \DB::rollback();
            return back()->with('validation_errors', $e->getMessage());
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }




   }

}
