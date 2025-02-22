<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class EmployeesController extends Controller
{
    public function index(Request $request){

        // $employees = Employee::orderBy('id','asc')->paginate(10);
        $filter_name = $request->filter_name;
        $filter_division_id = $request->filter_division_id;
        $results = Employee::query();


        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();

        // dd($divisions);


        if (!empty($filter_name)) {
            $results = $results->where('name', 'like', '%'.$filter_name.'%');
        }

        if (!empty($filter_division_id)) {
            $results = $results->where('division_id', $filter_division_id);
        }

        $employees = $results->orderBy('id','asc')->paginate(10);

        return view("employees.index",compact("employees","statuses","divisions","departments","subdepartments","sections"));
    }

    public function create(Request $request){
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();

        return view("employees.create",compact("statuses","divisions","departments","subdepartments","sections"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:employees",
            "division_id" => "required",
            "department_id" => "required",
            "sub_department_id" => "required",
            "section_id" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $employee = new Employee();
       $employee->name = $request["name"];
       $employee->slug = Str::slug($request["name"]);
       $employee->division_id = $request["division_id"];
       $employee->department_id = $request["department_id"];
       $employee->sub_department_id = $request["sub_department_id"];
       $employee->section_id = $request["section_id"];
       $employee->status_id = $request["status_id"];
       $employee->user_id = $user_id;
       $employee->save();
       return redirect(route("employees.index"))->with('success',"Employee created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:employees,name,".$id],
            "edit_division_id" => "required",
            "edit_department_id" => "required",
            "edit_sub_department_id" => "required",
            "edit_section_id" => "required",
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $employee = Employee::findOrFail($id);
        $employee->name = $request["edit_name"];
        $employee->slug = Str::slug($request["edit_name"]);
        $employee->division_id = $request["edit_division_id"];
        $employee->department_id = $request["edit_department_id"];
        $employee->sub_department_id = $request["edit_sub_department_id"];
        $employee->section_id = $request["edit_section_id"];
        $employee->status_id = $request["edit_status_id"];
        $employee->user_id = $user_id;
        $employee->save();
        return redirect(route("employees.index"))->with('success',"Employee updated successfully");
    }

    public function destroy(string $id)
    {
        $employee = Employee::findOrFail($id);
        $employee->delete();
        return redirect()->back()->with('success',"Employee deleted successfully");
    }

    public function changestatus(Request $request){
        $employee = Employee::findOrFail($request["id"]);
        $employee->status_id = $request["status_id"];
        $employee->save();

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
        //     //     Excel::import(new EmployeeImport, $request->file('file'));
        //     // }
        //     Excel::import(new EmployeeImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new EmployeeImport, $file);

            \DB::commit();
            return redirect(route("employees.index"))->with('success',"Employee excel imported successfully");

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
