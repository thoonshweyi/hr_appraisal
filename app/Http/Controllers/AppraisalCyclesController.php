<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use App\Models\AppraisalCycle;
use App\Models\Position;
use App\Models\BranchUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Imports\DivisionImport;
use App\Imports\AppraisalCycleImport;
use App\Imports\PositionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use App\Imports\SubDepartmentImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AgileDepartmentImport;
use App\Exceptions\ExcelImportValidationException;


class AppraisalCyclesController extends Controller
{
    public function index(Request $request){


        // $appraisalcycles = AppraisalCycle::orderBy('id','asc')->paginate(10);
        $filter_name = $request->filter_appraisalcycle_name;
        $filter_appraisalcycle_code = $request->filter_appraisalcycle_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;

        $results = AppraisalCycle::query();

        // dd('hay');

        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $positions = Position::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();



        // dd($divisions);


        if (!empty($filter_appraisalcycle_name)) {
            $results = $results->where('appraisalcycle_name', 'like', '%'.$filter_appraisalcycle_name.'%');
        }

        if (!empty($filter_appraisalcycle_code)) {
            $results = $results->where('appraisalcycle_code', $filter_appraisalcycle_code);
        }

        if (!empty($filter_branch_id)) {
            $results = $results->where('branch_id', $filter_branch_id);
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->where('position_level_id', $filter_position_level_id);
        }


        $appraisalcycles = $results->orderBy('id','asc')->paginate(10);

        return view("appraisalcycles.index",compact("appraisalcycles","statuses","divisions","departments","subdepartments","sections","positions","branches","positionlevels"));
    }

    public function create(Request $request){
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();

        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();

        // dd($branches);


        return view("appraisalcycles.create",compact("statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:appraisal_cycles",
            "description" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "action_start_date" => "required",
            "action_end_date" => "required",
            "action_start_time" => "required",
            "action_end_time" => "required",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $appraisalcycle = new AppraisalCycle();
       $appraisalcycle->name = $request["name"];
       $appraisalcycle->description = $request["description"];
       $appraisalcycle->start_date = $request["start_date"];
       $appraisalcycle->end_date = $request["end_date"];
       $appraisalcycle->action_start_date = $request["action_start_date"];
       $appraisalcycle->action_end_date = $request["action_end_date"];
       $appraisalcycle->action_start_time = $request["action_start_time"];
       $appraisalcycle->action_end_time = $request["action_end_time"];
       $appraisalcycle->status_id = $request["status_id"];
       $appraisalcycle->user_id = $user_id;


       $appraisalcycle->save();



       return redirect(route("appraisalcycles.index"))->with('success',"AppraisalCycle created successfully");;
    }


    public function edit(Request $request, string $id){
        $appraisalcycle = AppraisalCycle::find($id);
        // dd($appraisalcycle);

        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $positions = Position::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();

        $genders = Gender::where('status_id',1)->orderBy('id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();

        // dd($branches);


        return view("appraisalcycles.edit",compact("appraisalcycle","statuses","divisions","departments","subdepartments","sections","positions","branches","genders","positionlevels"));
    }


    public function update(Request $request, string $id)
    {

        $this->validate($request,[
            "name" => ["required","max:50","unique:appraisal_cycles,name,".$id],
            "description" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "action_start_date" => "required",
            "action_end_date" => "required",
            "action_start_time" => "required",
            "action_end_time" => "required",
            "status_id" => "required|in:1,2",
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $appraisalcycle = AppraisalCycle::findOrFail($id);
        $appraisalcycle->name = $request["name"];
        $appraisalcycle->description = $request["description"];
        $appraisalcycle->start_date = $request["start_date"];
        $appraisalcycle->end_date = $request["end_date"];
        $appraisalcycle->action_start_date = $request["action_start_date"];
        $appraisalcycle->action_end_date = $request["action_end_date"];
        $appraisalcycle->action_start_time = $request["action_start_time"];
        $appraisalcycle->action_end_time = $request["action_end_time"];
        $appraisalcycle->status_id = $request["status_id"];
        $appraisalcycle->user_id = $user_id;


        $appraisalcycle->save();
        return redirect(route("appraisalcycles.index"))->with('success',"AppraisalCycle updated successfully");
    }

    public function destroy(string $id)
    {
        $appraisalcycle = AppraisalCycle::findOrFail($id);
        $appraisalcycle->delete();
        return redirect()->back()->with('success',"AppraisalCycle deleted successfully");
    }

    public function changestatus(Request $request){
        $appraisalcycle = AppraisalCycle::findOrFail($request["id"]);
        $appraisalcycle->status_id = $request["status_id"];
        $appraisalcycle->save();

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
        //     //     Excel::import(new AppraisalCycleImport, $request->file('file'));
        //     // }
        //     Excel::import(new AppraisalCycleImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new AppraisalCycleImport, $file);

            \DB::commit();
            return redirect(route("appraisalcycles.index"))->with('success',"AppraisalCycle excel imported successfully");

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
