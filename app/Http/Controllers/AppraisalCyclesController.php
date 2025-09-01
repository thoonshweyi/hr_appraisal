<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use App\Models\Position;
use App\Helpers\FCMHelper;
use App\Models\BranchUser;
use App\Models\PeerToPeer;
use App\Models\SubSection;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Models\AppraisalCycle;
use App\Imports\DivisionImport;
use App\Imports\PositionImport;
use App\Models\AgileDepartment;
use App\Imports\DepartmentImport;
use App\Imports\SubDepartmentImport;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\AppraisalCycleImport;
use App\Imports\AgileDepartmentImport;
use Yajra\DataTables\Facades\DataTables;
use App\Models\AppraisalFormAssesseeUser;
use App\Exceptions\ExcelImportValidationException;


class AppraisalCyclesController extends Controller
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
            "action_start_date" => "required|date|after_or_equal:today|before_or_equal:action_end_date",
            "action_end_date" => "required|date|after_or_equal:action_start_date",
            // "action_start_time" => "required",
            // "action_end_time" => "required",
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
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();
        $sections = Section::where('status_id',1)->orderBy('id')->get();
        $subsections = SubSection::where('status_id',1)->orderBy('id')->get();
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();

        // $users = User::where('status',1)
        //         ->whereNotIn('id',[1])
        //         ->get();

        // dd($branches);

        // $participant_user_ids = PeerToPeer::where('appraisal_cycle_id',$id)->groupBy('assessor_user_id')->pluck("assessor_user_id");
        // // dd($participant_user_ids);

        // $participantusers = User::whereIn("id",$participant_user_ids)->get();
        // // dd($participantusers);



        // $assessee_user_ids = AppraisalFormAssesseeUser::whereHas("appraisalform",function($query) use($id){
        //     $query->where('appraisal_cycle_id',$id);
        // })
        // ->groupBy('assessee_user_id')->pluck("assessee_user_id");
        // $assesseeusers = User::whereIn("id",$assessee_user_ids)->get();


        return view("appraisalcycles.edit",compact("appraisalcycle","branches","positionlevels","statuses","subdepartments","sections","subsections"));
    }


    public function update(Request $request, string $id)
    {

        $this->validate($request,[
            "name" => ["required","max:50","unique:appraisal_cycles,name,".$id],
            "description" => "required",
            "start_date" => "required",
            "end_date" => "required",
            "action_start_date" => "required|date|after_or_equal:today|before_or_equal:action_end_date",
            "action_end_date" => "required|date|after_or_equal:action_start_date",
            // "action_start_time" => "required",
            // "action_end_time" => "required",
            "status_id" => "required|in:1,2",
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $appraisalcycle = AppraisalCycle::findOrFail($id);

        if(!$appraisalcycle->isBeforeActionStart()){
            return redirect(route("appraisalcycles.index"))->with('error',"AppraisalCycle can only be edited before action start.");
        }

        $appraisalcycle->name = $request["name"];
        $appraisalcycle->description = $request["description"];
        $appraisalcycle->start_date = $request["start_date"];
        $appraisalcycle->end_date = $request["end_date"];
        $appraisalcycle->action_start_date = $request["action_start_date"];
        $appraisalcycle->action_end_date = $request["action_end_date"];
        // $appraisalcycle->action_start_time = $request["action_start_time"];
        // $appraisalcycle->action_end_time = $request["action_end_time"];
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


   public function participantusers(Request $request, string $id){

        $participant_user_ids = PeerToPeer::where('appraisal_cycle_id',$id)->groupBy('assessor_user_id')->pluck("assessor_user_id");
        // dd($participant_user_ids);

        $participantusers = User::whereIn("id",$participant_user_ids);
        // dd($participantusers->where('id',39));



        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_subdepartment_id = $request->filter_subdepartment_id;
        $filter_section_id = $request->filter_section_id;
        $filter_sub_section_id = $request->filter_sub_section_id;


        // $results = PeerToPeer::query();
        $results = $participantusers;


        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_name){
                $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
            });
        }

        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_code){
                $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%')->orWhere('employee_name', 'like', '%'.$filter_employee_code.'%');
            });
        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_branch_id){
                $query->where('branch_id', $filter_branch_id);
            });
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
                $query->where('position_level_id', $filter_position_level_id);
            });
        }

        if (!empty($filter_subdepartment_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_subdepartment_id){
                $query->where('sub_department_id', $filter_subdepartment_id);
            });
        }

        if (!empty($filter_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_section_id){
                $query->where('section_id', $filter_section_id);
            });
        }

        if (!empty($filter_sub_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_sub_section_id){
                $query->where('sub_section_id', $filter_sub_section_id);
            });
        }


        $participantusers = $results
        ->orderBy("id", "desc")
        ->with(['employee.branch',"employee.department","employee.position","employee.positionlevel",
        'printhistory'])
        ->get();


        // dd($participantusers);
        // ->paginate(10);



        return DataTables::of($participantusers)
                ->addColumn('progress', function ($participantuser) use ($id) {
                    return "
                        <div class='d-flex justify-content-center align-items-center'>
                            <div id='progresses'  style='background : conic-gradient(steelblue {$participantuser->getSentPercentage($id)}%,#eee {$participantuser->getSentPercentage($id)}%)'>
                                    <span id='progressvalues'>{$participantuser->getSentPercentage($id)}%</span>
                            </div>
                        </div>
                    ";
                })
                ->addColumn('action', function ($participantuser) use ($id) {

                    $printbtn = ($participantuser->employee->positionlevel->id < 5 && !$participantuser->printhistory) ? "<a href='javascript:void(0);' data-user='$participantuser->id' class='text-warning mx-2 print_btn' title='Print'><i class='fas fa-print'></i></a>" : '';
                    return "
                        <div class='d-flex justify-content-center align-items-center'>
                            <form id='appraisalform' action='".route('appraisalforms.create')."' method='GET'>
                                <input type='hidden' name='assessor_user_id' value='$participantuser->id'>
                                <input type='hidden' name='appraisal_cycle_id' value='$id'/>
                               <button type='submit' class='btn btn-link p-0 m-0' title='Send'>
                                    <i class='far fa-paper-plane text-primary mr-2'></i>
                                </button>
                            </form>

                            $printbtn
                            <a href='javascript:void(0);' class='show-forms' data-user='$participantuser->id' title='Open'><i class='fas fa-chevron-down'></i></a>
                        </div>
                    ";
                })
                ->addColumn("appraisalformcount", function($user) use($id){
                    return $user->getAppraisalFormCount($id);
                })
                ->addColumn("allformcount", function($user) use($id){
                    return $user->getAllFormCount($id);
                })
                ->rawColumns(['progress', 'action']) // <-- Allow raw HTML
                ->make(true);
   }


   public function assesseeusers(Request $request, string $id){


        $assessee_user_ids = AppraisalFormAssesseeUser::whereHas("appraisalform",function($query) use($id){
            $query->where('appraisal_cycle_id',$id);
        })
        ->groupBy('assessee_user_id')->pluck("assessee_user_id");
        $assesseeusers = User::whereIn("id",$assessee_user_ids);



        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_subdepartment_id = $request->filter_subdepartment_id;
        $filter_section_id = $request->filter_section_id;
        $filter_sub_section_id = $request->filter_sub_section_id;

        // $results = PeerToPeer::query();
        $results = $assesseeusers;


        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_name){
                $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
            });
        }

        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_code){
                $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%')->orWhere('employee_name', 'like', '%'.$filter_employee_code.'%');;
            });
        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_branch_id){
                $query->where('branch_id', $filter_branch_id);
            });
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
                $query->where('position_level_id', $filter_position_level_id);
            });
        }

        if (!empty($filter_subdepartment_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_subdepartment_id){
                $query->where('sub_department_id', $filter_subdepartment_id);
            });
        }

        if (!empty($filter_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_section_id){
                $query->where('section_id', $filter_section_id);
            });
        }

        if (!empty($filter_sub_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_sub_section_id){
                $query->where('sub_section_id', $filter_sub_section_id);
            });
        }

        $assesseeusers = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
        ->get();
        // ->paginate(10);



        return DataTables::of($assesseeusers)
                ->addColumn('action', function ($assesseeuser) use ($id) {
                    return "
                        <a href='". route('assesseesummary.review',['assessee_user_id'=>$assesseeuser->id,'appraisal_cycle_id'=>$id])."'class='text-primary mr-2' title='Open' onclick=''><i class='far fa-eye'></i></i></a>
                    ";
                })
                ->rawColumns(['action']) // <-- Allow raw HTML
                ->make(true);

   }

   public function assessorusers(Request $request, string $id){
        $users = User::where('status',1)
        ->whereNotIn('id',[1]);


        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_subdepartment_id = $request->filter_subdepartment_id;
        $filter_user_id = $request->filter_user_id;
        $filter_section_id = $request->filter_section_id;
        $filter_sub_section_id = $request->filter_sub_section_id;

        // $results = PeerToPeer::query();
        $results = $users;

        clearFilterSection();
          // for getting employee info
        if(!empty($filter_user_id)){
            $results = $results->where("id",$filter_user_id);

            $user = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
            ->first();

            return response()->json([
                "user"=>$user
            ]);
        }

        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_name){
                $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
            });

            $request->session()->put('filter_employee_name', $filter_employee_name);
        }

        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_code){
                $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%')->orWhere('employee_name', 'like', '%'.$filter_employee_code.'%');;
            });

            $request->session()->put('filter_employee_code', $filter_employee_code);

        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_branch_id){
                $query->where('branch_id', $filter_branch_id);
            });

            $request->session()->put('filter_branch_id', $filter_branch_id);
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
                $query->where('position_level_id', $filter_position_level_id);
            });

            $request->session()->put('filter_position_level_id', $filter_position_level_id);

        }

        if (!empty($filter_subdepartment_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_subdepartment_id){
                $query->where('sub_department_id', $filter_subdepartment_id);
            });
            $request->session()->put('filter_subdepartment_id', $filter_subdepartment_id);
        }

        if (!empty($filter_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_section_id){
                $query->where('section_id', $filter_section_id);
            });
            $request->session()->put('filter_section_id', $filter_section_id);
        }

        if (!empty($filter_sub_section_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_sub_section_id){
                $query->where('sub_section_id', $filter_sub_section_id);
            });
            $request->session()->put('filter_sub_section_id', $filter_sub_section_id);
        }



        $results = $results->doesntHave('roles');

            $users = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
            ->get();


        return response()->json([
            "users"=>$users
        ]);
   }


    public function countdown(Request $request,string $id){
        $appraisalcycle = AppraisalCycle::find($id);
        $startdate = Carbon::parse($appraisalcycle->action_start_date)->startOfDay()->format('M d Y 00:00:00');
        // dd($startdate);

        // if(!$appraisalcycle->isBeforeActionStart()){
        //     return redirect(route("appraisalforms.index"))->with('error',"AppraisalCycle can only be counted before action start.");
        // }
        return view("appraisalcycles.countdown",compact('appraisalcycle','startdate'));
    }


    public function sendNotifications(Request $request){
        $assessor_user_id = $request->assessor_user_id;
        $appraisal_cycle_id = $request->appraisal_cycle_id;
        $appraisalforms = AppraisalForm::where('appraisal_cycle_id', $appraisal_cycle_id)
        ->where('assessor_user_id', $assessor_user_id)
        ->get();

        $responses = [];

        forEach($appraisalforms as $appraisalform){
                $title = "ရာထူးတိုးဖောင်တစ်ခု လက်ခံရရှိခြင်း";
                $message = "သင်အကဲဖြတ်ပေးရန် ရာထူးတိုးဖောင်တစ်ခုရရှိပါသည်။ သတ်မှတ်အချိန်ကာလအတွင်း အကဲဖြတ်၍ဖောင်ကိုပြန်လည်၍ပေးပို့ပေးရန်ဖြစ်ပါသည်။";
                $responses[] =  $response = FCMHelper::sendFCMNotification($assessor_user_id,$title,$message,$appraisalform->id);
        }

        return response()->json($responses);


    }

    public function compareEmployees(Request $request, string $id){


        // dd($request->empuser_ids);

        $empuser_ids = $request->empuser_ids;
        $users = User::whereIn('id',$empuser_ids);


        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_subdepartment_id = $request->filter_subdepartment_id;
        $filter_user_id = $request->filter_user_id;

        // $results = PeerToPeer::query();
        $results = $users;

          // for getting employee info
        if(!empty($filter_user_id)){
            $results = $results->where("id",$filter_user_id);

            $user = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
            ->first();

            return response()->json([
                "user"=>$user
            ]);
        }

        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_name){
                $query->where('employee_name', 'like', '%'.$filter_employee_name.'%');
            });

            $request->session()->put('filter_employee_name', $filter_employee_name);
        }

        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('employee',function($query) use($filter_employee_code){
                $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%')->orWhere('employee_name', 'like', '%'.$filter_employee_code.'%');;
            });

            $request->session()->put('filter_employee_code', $filter_employee_code);

        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_branch_id){
                $query->where('branch_id', $filter_branch_id);
            });

            $request->session()->put('filter_branch_id', $filter_branch_id);
        }


        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_position_level_id){
                $query->where('position_level_id', $filter_position_level_id);
            });

            $request->session()->put('filter_position_level_id', $filter_position_level_id);

        }

        if (!empty($filter_subdepartment_id)) {
            $results = $results->whereHas('employee',function($query) use($filter_subdepartment_id){
                $query->where('sub_department_id', $filter_subdepartment_id);
            });
            $request->session()->put('filter_subdepartment_id', $filter_subdepartment_id);
        }
        $results = $results->doesntHave('roles');

        $users = $results->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
        ->get();


        foreach($users as $user){
            $user->assessees = PeerToPeer::getRecentAssessees($user->id, $id);
            $user->assessors = PeerToPeer::getRecentAssessors($user->id, $id);
            // dd($user);
        }
        // dd($users[0]);

        return view("appraisalcycles.compare",compact("users"));

    }

}



