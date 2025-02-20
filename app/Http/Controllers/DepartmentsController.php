<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\DeptGroup;
use App\Models\Department;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\DeptGroupImport;
use App\Imports\DepartmentImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;

class DepartmentsController extends Controller
{
    public function index(){

        $departments = Department::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $deptgroups = DeptGroup::where('status_id',1)->orderBy('id')->get();
        // dd($deptgroups);
        return view("departments.index",compact("departments","statuses","deptgroups"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:departments",
            "code" => "required",
            "dept_group_id" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $department = new Department();
       $department->code = $request["code"];
       $department->name = $request["name"];
       $department->slug = Str::slug($request["name"]);
       $department->dept_group_id = $request["dept_group_id"];
       $department->status_id = $request["status_id"];
       $department->user_id = $user_id;
       $department->save();
       return redirect(route("departments.index"))->with('success',"Department created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:departments,name,".$id],
            "edit_code" => "required",
            "edit_dept_group_id" => "required",
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $department = Department::findOrFail($id);
        $department->code = $request["edit_code"];
        $department->name = $request["edit_name"];
        $department->slug = Str::slug($request["edit_name"]);
        $department->dept_group_id = $request["edit_dept_group_id"];
        $department->status_id = $request["edit_status_id"];
        $department->user_id = $user_id;
        $department->save();
        return redirect(route("departments.index"))->with('success',"Department updated successfully");
    }

    public function destroy(string $id)
    {
        $department = Department::findOrFail($id);
        $department->delete();
        return redirect()->back()->with('success',"Department deleted successfully");
    }

    public function changestatus(Request $request){
        $department = Department::findOrFail($request["id"]);
        $department->status_id = $request["status_id"];
        $department->save();

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
        //     //     Excel::import(new DepartmentImport, $request->file('file'));
        //     // }
        //     Excel::import(new DepartmentImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new DepartmentImport, $file);

            \DB::commit();
            return redirect(route("departments.index"))->with('success',"Department excel imported successfully");

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
