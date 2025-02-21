<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use App\Models\Position;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Imports\DivisionImport;
use App\Imports\PositionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use App\Imports\SubDepartmentImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AgileDepartmentImport;
use App\Exceptions\ExcelImportValidationException;

class PositionsController extends Controller
{
    public function index(Request $request){

        // $positions = Position::orderBy('id','asc')->paginate(10);
        $filter_name = $request->filter_name;
        $filter_division_id = $request->filter_division_id;
        $results = Position::query();
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

        $positions = $results->orderBy('id','asc')->paginate(10);

        return view("positions.index",compact("positions","statuses","divisions","departments","subdepartments","sections"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:positions",
            "division_id" => "required",
            "department_id" => "required",
            "sub_department_id" => "required",
            "section_id" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $position = new Position();
       $position->name = $request["name"];
       $position->slug = Str::slug($request["name"]);
       $position->division_id = $request["division_id"];
       $position->department_id = $request["department_id"];
       $position->sub_department_id = $request["sub_department_id"];
       $position->section_id = $request["section_id"];
       $position->status_id = $request["status_id"];
       $position->user_id = $user_id;
       $position->save();
       return redirect(route("positions.index"))->with('success',"Department created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:positions,name,".$id],
            "edit_division_id" => "required",
            "edit_department_id" => "required",
            "edit_sub_department_id" => "required",
            "edit_section_id" => "required",
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $position = Position::findOrFail($id);
        $position->name = $request["edit_name"];
        $position->slug = Str::slug($request["edit_name"]);
        $position->division_id = $request["edit_division_id"];
        $position->department_id = $request["edit_department_id"];
        $position->sub_department_id = $request["edit_sub_department_id"];
        $position->section_id = $request["edit_section_id"];
        $position->status_id = $request["edit_status_id"];
        $position->user_id = $user_id;
        $position->save();
        return redirect(route("positions.index"))->with('success',"Department updated successfully");
    }

    public function destroy(string $id)
    {
        $position = Position::findOrFail($id);
        $position->delete();
        return redirect()->back()->with('success',"Department deleted successfully");
    }

    public function changestatus(Request $request){
        $position = Position::findOrFail($request["id"]);
        $position->status_id = $request["status_id"];
        $position->save();

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
        //     //     Excel::import(new PositionImport, $request->file('file'));
        //     // }
        //     Excel::import(new PositionImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new PositionImport, $file);

            \DB::commit();
            return redirect(route("positions.index"))->with('success',"Sub Department excel imported successfully");

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
