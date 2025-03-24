<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Imports\DivisionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use App\Imports\SubDepartmentImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AgileDepartmentImport;
use App\Exceptions\ExcelImportValidationException;

class SectionsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-fixed-analysis', ['only' => ['index']]);
        $this->middleware('permission:create-fixed-analysis', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-fixed-analysis', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-fixed-analysis', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        // $sections = Section::orderBy('id','asc')->paginate(10);
        $filter_name = $request->filter_name;
        $filter_division_id = $request->filter_division_id;
        $results = Section::query();
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();

        // dd($divisions);


        if (!empty($filter_name)) {
            $results = $results->where('name', 'like', '%'.$filter_name.'%');
        }

        if (!empty($filter_division_id)) {
            $results = $results->where('division_id', $filter_division_id);
        }

        $sections = $results->orderBy('id','asc')->paginate(10);

        return view("sections.index",compact("sections","statuses","divisions","departments","subdepartments"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:sections",
            "division_id" => "required",
            "department_id" => "required",
            "sub_department_id" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $section = new Section();
       $section->name = $request["name"];
       $section->slug = Str::slug($request["name"]);
       $section->division_id = $request["division_id"];
       $section->department_id = $request["department_id"];
       $section->sub_department_id = $request["sub_department_id"];
       $section->status_id = $request["status_id"];
       $section->user_id = $user_id;
       $section->save();
       return redirect(route("sections.index"))->with('success',"Department created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:sections,name,".$id],
            "edit_division_id" => "required",
            "edit_department_id" => "required",
            "edit_sub_department_id" => "required",
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $section = Section::findOrFail($id);
        $section->name = $request["edit_name"];
        $section->slug = Str::slug($request["edit_name"]);
        $section->division_id = $request["edit_division_id"];
        $section->department_id = $request["edit_department_id"];
        $section->sub_department_id = $request["edit_sub_department_id"];
        $section->status_id = $request["edit_status_id"];
        $section->user_id = $user_id;
        $section->save();
        return redirect(route("sections.index"))->with('success',"Department updated successfully");
    }

    public function destroy(string $id)
    {
        $section = Section::findOrFail($id);
        $section->delete();
        return redirect()->back()->with('success',"Department deleted successfully");
    }

    public function changestatus(Request $request){
        $section = Section::findOrFail($request["id"]);
        $section->status_id = $request["status_id"];
        $section->save();

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
        //     //     Excel::import(new SectionImport, $request->file('file'));
        //     // }
        //     Excel::import(new SectionImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new SectionImport, $file);

            \DB::commit();
            return redirect(route("sections.index"))->with('success',"Sub Department excel imported successfully");

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
