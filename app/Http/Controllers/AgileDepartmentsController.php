<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\DivisionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AgileDepartmentImport;
use App\Exceptions\ExcelImportValidationException;


class AgileDepartmentsController extends Controller
{
    public function index(Request $request){

        // $agiledepartments = AgileDepartment::orderBy('id','asc')->paginate(10);
        $filter_name = $request->filter_name;
        $filter_division_id = $request->filter_division_id;
        $results = AgileDepartment::query();
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        // dd($divisions);


        if (!empty($filter_name)) {
            $results = $results->where('name', 'like', '%'.$filter_name.'%');
        }

        if (!empty($filter_division_id)) {
            $results = $results->where('division_id', $filter_division_id);
        }

        $agiledepartments = $results->orderBy('id','asc')->paginate(10);

        return view("agiledepartments.index",compact("agiledepartments","statuses","divisions"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:agile_departments",
            "division_id" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $agiledepartment = new AgileDepartment();
       $agiledepartment->name = $request["name"];
       $agiledepartment->slug = Str::slug($request["name"]);
       $agiledepartment->division_id = $request["division_id"];
       $agiledepartment->status_id = $request["status_id"];
       $agiledepartment->user_id = $user_id;
       $agiledepartment->save();
       return redirect(route("agiledepartments.index"))->with('success',"Department created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:agile_departments,name,".$id],
            "edit_division_id" => "required",
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $agiledepartment = AgileDepartment::findOrFail($id);
        $agiledepartment->name = $request["edit_name"];
        $agiledepartment->slug = Str::slug($request["edit_name"]);
        $agiledepartment->division_id = $request["edit_division_id"];
        $agiledepartment->status_id = $request["edit_status_id"];
        $agiledepartment->user_id = $user_id;
        $agiledepartment->save();
        return redirect(route("agiledepartments.index"))->with('success',"Department updated successfully");
    }

    public function destroy(string $id)
    {
        $agiledepartment = AgileDepartment::findOrFail($id);
        $agiledepartment->delete();
        return redirect()->back()->with('success',"Department deleted successfully");
    }

    public function changestatus(Request $request){
        $agiledepartment = AgileDepartment::findOrFail($request["id"]);
        $agiledepartment->status_id = $request["status_id"];
        $agiledepartment->save();

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
        //     //     Excel::import(new AgileDepartmentImport, $request->file('file'));
        //     // }
        //     Excel::import(new AgileDepartmentImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new AgileDepartmentImport, $file);

            \DB::commit();
            return redirect(route("agiledepartments.index"))->with('success',"Department excel imported successfully");

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
