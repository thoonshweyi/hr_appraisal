<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\BranchUser;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Models\AttachFormType;
use App\Imports\DivisionImport;
use App\Imports\EmployeeImport;
use App\Imports\PositionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use App\Imports\SubDepartmentImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AgileDepartmentImport;
use App\Exceptions\ExcelImportValidationException;


class EmployeesController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-add-on', ['only' => ['index']]);
        $this->middleware('permission:create-add-on', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-add-on', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-add-on', ['only' => ['destroy']]);
    }

    public function index(Request $request){


        // $employees = Employee::orderBy('id','asc')->paginate(10);
        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;

        $results = Employee::query();

        // dd('hay');

        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $positions = Position::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();




        // dd($divisions);


        if (!empty($filter_employee_name)) {
            $results = $results->where('employee_name', 'like', '%'.$filter_employee_name.'%');
        }

        if (!empty($filter_employee_code)) {
            $results = $results->where('employee_code', 'like' , '%'.$filter_employee_code.'%');
        }

        if (!empty($filter_branch_id)) {
            $results = $results->where('branch_id', $filter_branch_id);
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->where('position_level_id', $filter_position_level_id);
        }


        $employees = $results->orderBy('id','asc')->paginate(10);

        return view("employees.index",compact("employees","statuses","divisions","departments","subdepartments","sections","positions","branches","positionlevels","attachformtypes"));
    }

    public function create(Request $request){
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $positions = Position::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();

        $genders = Gender::where('status_id',1)->orderBy('id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();


        // dd($branches);


        return view("employees.create",compact("statuses","divisions","departments","subdepartments","sections","positions","branches","genders","positionlevels","attachformtypes"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "employee_name" => "required|max:50|unique:employees",
            "division_id" => "required",
            "department_id" => "required",
            "sub_department_id" => "required",
            "section_id" => "required",
            "status_id" => "required|in:1,2",

            'beginning_date'=> "required",
            "employee_code"=> "required",
            "branch_id"=> "required",
            "age"=> "required",
            "gender_id"=> "required",
            'position_level_id'=> "required",
            "nrc"=> "required",
            "father_name"=> "required",
            "attach_form_type_id"=> "required",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $employee = new Employee();
       $employee->employee_name = $request["employee_name"];
       $employee->nickname = $request["nickname"];
       $employee->division_id = $request["division_id"];
       $employee->department_id = $request["department_id"];
       $employee->sub_department_id = $request["sub_department_id"];
       $employee->section_id = $request["section_id"];
       $employee->position_id = $request["position_id"];
       $employee->status_id = $request["status_id"];
       $employee->user_id = $user_id;

       $employee->beginning_date = $request["beginning_date"];
       $employee->employee_code = $request["employee_code"];
       $employee->branch_id = $request["branch_id"];
       $employee->age = $request["age"];
       $employee->gender_id = $request["gender_id"];
       $employee->position_level_id = $request["position_level_id"];
       $employee->nrc = $request["nrc"];
       $employee->father_name = $request["father_name"];
       $employee->attach_form_type_id = $request["attach_form_type_id"];


       $employee->save();

       $empuser = User::firstOrCreate([
            "name"=> $request["employee_name"],
            "employee_id"=> $request["employee_code"],
            "password"=> Hash::make($request["employee_code"])
        ]);
        $userBranch['user_id'] = $empuser->id;
        $userBranch['branch_id'] = $request["branch_id"];
        BranchUser::firstOrCreate($userBranch);


       return redirect(route("employees.index"))->with('success',"Employee created successfully");;
    }


    public function edit(Request $request, string $id){
        $employee = Employee::find($id);
        // dd($employee);

        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $divisions = Division::where('status_id',1)->orderBy('id')->get();
        $departments = AgileDepartment::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $positions = Position::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();

        $genders = Gender::where('status_id',1)->orderBy('id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();

        // dd($branches);


        return view("employees.edit",compact("employee","statuses","divisions","departments","subdepartments","sections","positions","branches","genders","positionlevels","attachformtypes"));
    }


    public function update(Request $request, string $id)
    {

        $this->validate($request,[
            "employee_name" => ["required","max:50","unique:employees,employee_name,".$id],
            "division_id" => "required",
            "department_id" => "required",
            "sub_department_id" => "required",
            "section_id" => "required",
            "status_id" => "required|in:1,2",

            'beginning_date'=> "required",
            "employee_code"=> "required|unique:employees,employee_code,".$id,
            "branch_id"=> "required",
            "age"=> "required",
            "gender_id"=> "required",
            'position_level_id'=> "required",
            "nrc"=> "required",
            "father_name"=> "required",
            "attach_form_type_id"=> "required",
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $employee = Employee::findOrFail($id);
        $employee->employee_name = $request["employee_name"];
        $employee->nickname = $request["nickname"];
        $employee->division_id = $request["division_id"];
        $employee->department_id = $request["department_id"];
        $employee->sub_department_id = $request["sub_department_id"];
        $employee->section_id = $request["section_id"];
        $employee->position_id = $request["position_id"];
        $employee->status_id = $request["status_id"];
        $employee->user_id = $user_id;

        $employee->beginning_date = $request["beginning_date"];
        $employee->employee_code = $request["employee_code"];
        $employee->branch_id = $request["branch_id"];
        $employee->age = $request["age"];
        $employee->gender_id = $request["gender_id"];
        $employee->position_level_id = $request["position_level_id"];
        $employee->nrc = $request["nrc"];
        $employee->father_name = $request["father_name"];
        $employee->attach_form_type_id = $request["attach_form_type_id"];


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
        // ini_set('max_execution_time', 300);

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new EmployeeImport, $file);

            \DB::commit();
            return redirect(route("employees.index"))->with('success',"Employee excel imported successfully");

        }catch (ExcelImportValidationException $e) {
            // If validation fails, show the error message to the user
            \DB::rollback();
            return redirect(route("employees.index"))->with('validation_errors', $e->getMessage());
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect(route("employees.index"))->with('error', "System Error:".$e->getMessage());
        }
   }


   public function updateprofilepicture(Request $request,$id){
    $request->validate([
        'image'=>"required|image|mimes:jpeg,png,jpg,gif|max:10485760"
    ]);
    $employee = Employee::findOrFail($id);


    $user = Auth::user();
    $user_id = $user['id'];
    if($request->hasFile('image')){
        // Single Image Update
        $file = $request->file("image");
        $fname = $file->getClientOriginalName();
        $imagenewname = uniqid($user_id)."-".$employee['id'].$fname;
        $file->move(public_path("assets/img/employees/"),$imagenewname);
        $filepath = "assets/img/employees/".$imagenewname;


        // Remove Old Image
        if($employee->image){
            $oldfilepath = public_path($employee->image);
            if(file_exists($oldfilepath)){
                unlink($oldfilepath);
            }
        }
        $employee->image = $filepath;
        $employee->save();
    }

    // Recalculate profile Score

    return redirect()->back()->with('success','Upload Successfully');
}

}
