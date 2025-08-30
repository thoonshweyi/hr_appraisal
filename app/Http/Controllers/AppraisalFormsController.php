<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Criteria;
use App\Helpers\FCMHelper;
use App\Models\AssFormCat;
use App\Models\FormResult;
use App\Models\PeerToPeer;
use App\Models\PrintHistory;
use Illuminate\Http\Request;
use App\Helpers\PusherHelper;
use App\Models\AppraisalForm;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Models\AppraisalFormAssesseeUser;
use App\Notifications\AppraisalFormsNotify;
use Illuminate\Support\Facades\Notification;

class AppraisalFormsController extends Controller
{
    function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('permission:view-add-on', ['only' => ['index']]);
        $this->middleware('permission:create-add-on', ['only' => ['create', 'store']]);
        // $this->middleware('permission:edit-add-on', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-add-on', ['only' => ['destroy']]);
    }

    public function index(Request $request){

        $results = AppraisalForm::query();

        $user = Auth::user();
        $user_id = $user->id;

        $appraisalcycles = AppraisalCycle::where('status_id',1)->orderBy('id')->get();
        $branches = Branch::where('branch_active',true)->orderBy('branch_id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $subdepartments = SubDepartment::where('status_id',1)->orderBy('id')->get();


        $filter_assessor_user_id = $request->filter_assessor_user_id;
        $filter_employee_name = $request->filter_employee_name;
        $filter_employee_code = $request->filter_employee_code;
        $filter_appraisal_cycle_id = $request->filter_appraisal_cycle_id;
        $filter_branch_id = $request->filter_branch_id;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_subdepartment_id = $request->filter_subdepartment_id;


        if (!empty($filter_assessor_user_id)) {
            $results = $results->where('assessor_user_id', $filter_assessor_user_id);
        }

        if (!empty($filter_employee_name)) {
            $results = $results->whereHas('assessoruser',function($query) use($filter_employee_name){
                $query->whereHas('employee',function($query) use($filter_employee_name){
                    $query->where('employee_name','like', '%'.$filter_employee_name.'%');
                });
            });
        }


        if (!empty($filter_employee_code)) {
            $results = $results->whereHas('assessoruser',function($query) use($filter_employee_code){
                $query->whereHas('employee',function($query) use($filter_employee_code){
                    $query->where('employee_code', 'like' , '%'.$filter_employee_code.'%')->orWhere('employee_name', 'like', '%'.$filter_employee_code.'%');
                });
            });
        }

        if (!empty($filter_appraisal_cycle_id)) {
            $results = $results->where('appraisal_cycle_id', $filter_appraisal_cycle_id);
        }

        if (!empty($filter_branch_id)) {
            $results = $results->whereHas('assessoruser',function($query) use($filter_branch_id){
                $query->whereHas('employee',function($query) use($filter_branch_id){
                    $query->where('branch_id', $filter_branch_id);
                });
            });
        }

        if (!empty($filter_subdepartment_id)) {
            $results = $results->whereHas('assessoruser',function($query) use($filter_subdepartment_id){
                $query->whereHas('employee',function($query) use($filter_subdepartment_id){
                    $query->where('sub_department_id', $filter_subdepartment_id);
                });
            });
        }

        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('assessoruser',function($query) use($filter_position_level_id){
                $query->whereHas('employee',function($query) use($filter_position_level_id){
                    $query->where('position_level_id', $filter_position_level_id);
                });
            });
        }

        if($user->can("view-all-appraisal-form")){

        }else{
            $results = $results
            ->where('assessor_user_id',$user_id)
            ->whereHas('appraisalcycle',function($query){
                $todayStr = Carbon::now()->toDateString(); // Get only 'YYYY-MM-DD'
                $query->whereDate('action_start_date', "<=", $todayStr)
                      ->whereDate('action_end_date', ">=", $todayStr);
            });
        }

        $appraisalforms = $results->orderBy('id','asc')->paginate(10);



        return view("appraisalforms.index",compact('appraisalforms','branches','appraisalcycles', 'positionlevels' ,'subdepartments'));
    }


    public function create(Request $request){


        // dd($total_good);
        $filled_assformcat_ids = AppraisalForm::where('appraisal_cycle_id',$request->appraisal_cycle_id)
        ->where('assessor_user_id',$request->assessor_user_id)
        ->pluck('ass_form_cat_id');
        // dd($filled_assformcat_ids);



        $assessoruser = User::where('id',$request->assessor_user_id)->first();
        $appraisalcycle = AppraisalCycle::where('id',$request->appraisal_cycle_id)->first();


        $assformcat_ids = PeerToPeer::where('assessor_user_id', $assessoruser->id)
        ->where('appraisal_cycle_id', $appraisalcycle->id)
        ->distinct()
        ->pluck('ass_form_cat_id');

        // dd($assformcat_ids);
        $assformcats = AssFormCat::whereIn('id',$assformcat_ids)->whereNotIn('id',$filled_assformcat_ids)->get();





        return view("appraisalforms.create",compact('assessoruser','appraisalcycle','assformcats'));
    }

    public function store(Request $request){


        $this->validate($request,[
            "assessor_user_id" => "required",
            "appraisal_cycle_id" => "required",
            "ass_form_cat_id" => "required",

            "assessee_user_ids" => "required|array",
            "assessee_user_ids.*"=>"required|string",
        ],[
            "assessor_user_id.required" =>  __('appraisalcycle.assessor_user_id'),
            "appraisal_cycle_id.required" =>  __('appraisalcycle.appraisal_cycle_id'),
            "ass_form_cat_id.required" => __('appraisalcycle.ass_form_cat_id'),

            "assessee_user_ids.required" => __('appraisalcycle.assessee_user_ids'),
            'assessee_user_ids.*.required' => __('appraisalcycle.assessee_user_ids'),
        ]);



        \DB::beginTransaction();
        try {

            $assessor_user_id = $request->assessor_user_id;
            $ass_form_cat_id = $request->ass_form_cat_id;
            $appraisal_cycle_id = $request->appraisal_cycle_id;
            $assessee_user_ids = $request->assessee_user_ids;


            $user = Auth::user();
            $user_id = $user->id;
            $appraisalform = AppraisalForm::updateOrcreate([
                "assessor_user_id"=> $assessor_user_id,
                "ass_form_cat_id"=> $ass_form_cat_id,
                "appraisal_cycle_id"=> $appraisal_cycle_id,
            ],[
                "user_id"=> $user_id
            ]);

            foreach($assessee_user_ids as $assessee_user_id){
                AppraisalFormAssesseeUser::firstOrcreate([
                    "appraisal_form_id" => $appraisalform->id,
                    "assessee_user_id" => $assessee_user_id
                ]);
            }

            $assessor = User::find($assessor_user_id);
            $assformcat = AssFormCat::where('id',$ass_form_cat_id)->first();
            // Start FCM Push Notification
            // dd($assessor_user_id);
                // // {$assessor->employee->employee_name}
                // // $title = "Action Required: Appraisal Form Received";
                // $title = "ရာထူးတိုးဖောင်တစ်ခု လက်ခံရရှိခြင်း";

                // // dd($title);
                // // $message = "You have received a new appraisal form for assessment. Kindly review and submit your feedback within the given timeframe.";
                // $message = "သင်အကဲဖြတ်ပေးရန် ရာထူးတိုးဖောင်တစ်ခုရရှိပါသည်။ သတ်မှတ်အချိန်ကာလအတွင်း အကဲဖြတ်၍ဖောင်ကိုပြန်လည်၍ပေးပို့ပေးရန်ဖြစ်ပါသည်။";
                // $response = FCMHelper::sendFCMNotification($assessor_user_id,$title,$message,$appraisalform->id);
            // End FCM Push Notification



            // Start Laravel Database Notification
            $title = "You received new Appraisal Form \"$assformcat->name\"";
            Notification::send($assessor,new AppraisalFormsNotify($appraisalform->id,$assformcat->id,$title,$appraisal_cycle_id));
            // End Laravel Database Notification

            \DB::commit();

            return redirect()->back()->with('success',"Appraisal form sended to assessor successfully");
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }

    }

    public function show(Request $request,$id){

        $appraisalform = AppraisalForm::find($id);
        // dd($appraisalform);
            $this->authorize('view', $appraisalform);

        $assessee_ids = $appraisalform->assesseeusers->pluck('id');
        $assesseeusers = User::whereIn("id",$assessee_ids)
        ->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
        ->get();
        // dd($assessee_ids);

        $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();


        $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');


        $roles = Auth::user()->roles->pluck('name');
        $adminauthorize = $roles->contains('Admin') || $roles->contains('HR Authorized');
        // dd($adminauthorize);
        if($adminauthorize){
            return view("appraisalforms.show",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));
        }else{
            return view("appraisalforms.showmobile",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));
        }
    }


    public function edit(Request $request,$id){



        $appraisalform = AppraisalForm::find($id);
        // dd($appraisalform);
            $this->authorize('edit', $appraisalform);


        $assessee_ids = $appraisalform->assesseeusers->pluck('id');
        $assesseeusers = User::whereIn("id",$assessee_ids)
        ->with(['employee.branch',"employee.department","employee.subdepartment","employee.position","employee.positionlevel"])
        ->get()
        ->groupBy(function ($user) {
            return $user->employee->branch->branch_name ?? 'No Branch';
        });
        // dd($assessee_ids);

        $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->orderBy("id")->get();

        $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');

        $roles = Auth::user()->roles->pluck('name');
        $adminauthorize = $roles->contains('Admin') || $roles->contains('HR Authorized');
        // dd($adminauthorize);

        $preloadresults = $appraisalform->getPreloadResult($appraisalform->id);

        if($adminauthorize){
            return view("appraisalforms.edit",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak","preloadresults"));
        }else{
            // return view("appraisalforms.edit",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));
            return view("appraisalforms.editmobile",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak","preloadresults"));
        }

    }
    public function update(Request $request,$id){

        // dd('submitted');
        // dd($request);

        $this->validate($request, [
            "appraisalformresults" => "required|array",
            "appraisalformresults.*" => "required|array",
            "appraisalformresults.*.*" => "required", // Ensure all values inside each assessee are filled
        ], [
            'appraisalformresults.*.*.required' => __('appraisalform.appraisalformresults'),
        ]);

        \DB::beginTransaction();
        try{

            $user = Auth::user();
            $user_id = $user->id;

            $appraisalform = AppraisalForm::find($id);
                $this->authorize('edit', $appraisalform);
            $appraisalform->update([
                "assessed" => true,
                "modify_user_id" => $user_id,
                "status_id" => 19
            ]);


            $appraisalformresults = $request->appraisalformresults;
            $appraisalform->formresults()->delete();
            foreach($appraisalformresults as $assessee_id=>$appraisalformresult){
                foreach($appraisalformresult as $criteria_id=>$result){

                    $criteria = Criteria::find($criteria_id);
                    $alloweds = $criteria->getRatingScaleAttribute();


                    if(!in_array($result,$alloweds)){
                        return redirect()->back()->with("error","Your rating-value doesn't match the given-rating-scale-values!")
                        ->withInput();

                    }
                    $formresult = FormResult::create([
                        "appraisal_form_id" => $id,
                        "assessee_user_id" => $assessee_id,
                        "criteria_id" => $criteria_id,
                        "result" => $result,
                    ]);
                }
            }

            $type = "App\Notifications\AppraisalFormsNotify";
            // $getnoti = \DB::table("notifications")->where("notifiable_id",$user_id)->where("type",$type)->where('data->appraisalform_id',$id)->pluck('id');
            $getnotis = $user->unreadNotifications;

            foreach($getnotis as $getnoti){
                if($getnoti->type == $type && $getnoti->data['appraisalform_id'] == $id){
                    $getnoti->markAsRead();
                }
            }

            \DB::commit();


            $adminauthorize = adminHRAuthorize();
            if($adminauthorize){
                 return redirect(route("appraisalforms.index"))->with('success',"Appraisal Form updated successfully");
            }else{
                return redirect(route("appraisalforms.notification"))->with('success',"Appraisal Form updated successfully");
            }
        }catch(Exception $err){
            \DB::rollback();
            Log::info($err);

            return redirect()->back()->with("error","There is an error in submitting Appraisal Form.");
        }
    }

    public function savedraft(Request $request,$id){

        // dd($request->appraisalformresults);

        \DB::beginTransaction();
        try{
            $user = Auth::user();
            $user_id = $user->id;


            $appraisalform = AppraisalForm::find($id);
                $this->authorize('edit', $appraisalform);


            $appraisalformresults = $request->appraisalformresults;
            $appraisalformresults = array_filter(
                array_map('array_filter', $appraisalformresults)
            );
            $appraisalform->update([
                "modify_user_id" => $user_id,
                "status_id" => empty($appraisalformresults) ? 21 : 20 ,
            ]);


            // dd($appraisalform->formresults);
            $appraisalform->formresults()->delete();
            foreach($appraisalformresults as $assessee_id=>$appraisalformresult){
                foreach($appraisalformresult as $criteria_id=>$result){

                    $criteria = Criteria::find($criteria_id);
                    $alloweds = $criteria->getRatingScaleAttribute();


                    if(!in_array($result,$alloweds)){
                        return redirect()->back()->with("error","Your rating-value doesn't match the given-rating-scale-values!")
                        ->withInput();

                    }
                    $formresult = FormResult::create([
                        "appraisal_form_id" => $id,
                        "assessee_user_id" => $assessee_id,
                        "criteria_id" => $criteria_id,
                        "result" => $result,
                    ]);
                }
            }

            \DB::commit();

            $adminauthorize = adminHRAuthorize();
            if($adminauthorize){
                 return redirect()->route("appraisalcycles.edit",$appraisalform->appraisal_cycle_id)->with('success',"Appraisal Form Saved successfully")->with("js",true);
            }else{
                return redirect(route("appraisalforms.notification"))->with('success',"Appraisal Form Saved successfully");
            }
        }catch(Exception $err){
            \DB::rollback();

            return redirect()->back()->with("error","There is an error in submitting Appraisal Form.".$err);
        }
    }

    public function fillform(Request $request){

        $this->validate($request,[
            "assessor_user_id" => "required",
            "appraisal_cycle_id" => "required",
            "ass_form_cat_id" => "required",

            // "assessee_user_ids" => "required|array",
            // "assessee_user_ids.*"=>"required|string",
        ],[
            // 'assessee_user_ids.*.required' => 'Please Assessee User Values.',
        ]);

        \DB::beginTransaction();

        try{

            $assessor_user_id = $request->assessor_user_id;
            $ass_form_cat_id = $request->ass_form_cat_id;
            $appraisal_cycle_id = $request->appraisal_cycle_id;
            $assessee_ids = PeerToPeer::where('assessor_user_id', $assessor_user_id)
            ->where('appraisal_cycle_id', $appraisal_cycle_id)
            ->where('ass_form_cat_id',$ass_form_cat_id)
            ->get()
            ->pluck('assessee_user_id');
            // dd($assessee_ids);



            $user = Auth::user();
            $user_id = $user->id;
            $appraisalform = AppraisalForm::create([
                "assessor_user_id"=> $assessor_user_id,
                "ass_form_cat_id"=> $ass_form_cat_id,
                "appraisal_cycle_id"=> $appraisal_cycle_id,
                "user_id"=> $user_id
            ]);

            foreach($assessee_ids as $assessee_user_id){
                AppraisalFormAssesseeUser::create([
                    "appraisal_form_id" => $appraisalform->id,
                    "assessee_user_id" => $assessee_user_id
                ]);
            }

            $assesseeusers = $appraisalform->assesseeusers()
            ->with('employee.branch') // eager load employee and branch
            ->orderBy('id','asc')
            ->get()
            ->groupBy(function ($user) {
                return $user->employee->branch->branch_name ?? 'No Branch';
            });

            $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();

            $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
            $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
            $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
            $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
            $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');

            $html = view('appraisalforms.fill',  compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"))->render();
            // Log::info($html);
            return response()->json(['html' => $html]);
        }catch(Exception $err){
            return redirect()->back()->with("error","There is an error in fillingsubmitting Appraisal Form.".$err);

        }
    }

    public function printpdf($id)
    {
        $appraisalform = AppraisalForm::findOrFail($id);
        $assesseeusers = $appraisalform->assesseeusers;
        $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();

        $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');

        $stylesheet = file_get_contents(public_path('css/backend.css'));

        $pdf = PDF::loadView('appraisalforms.print', [
            'appraisalform' => $appraisalform,
            'assesseeusers' => $assesseeusers,
            'criterias' => $criterias,
            'total_excellent' => $total_excellent,
            'total_good' => $total_good,
            'total_meet_standard' => $total_meet_standard,
            'total_below_standard' => $total_below_standard,
            'total_weak' => $total_weak,
            'style' => $stylesheet
        ]);

        return $pdf->stream('appraisal_form.pdf');
        // return $pdf->download('appraisal_form.pdf'); // to force download
    }


    public function showprintframe($id)
    {

        $appraisalform = AppraisalForm::findOrFail($id);
        $assesseeusers = $appraisalform->assesseeusers()
        ->with('employee.branch') // eager load employee and branch
        ->orderBy('id','asc')
        ->get()
        ->groupBy(function ($user) {
            return $user->employee->branch->branch_name ?? 'No Branch';
        });
        $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();

        $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');


        return view('appraisalforms.print', compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));
    }

    public function groupedforms(Request $request)
    {
        $appraisalforms = AppraisalForm::where('appraisal_cycle_id', 2)
            ->get()->groupBy('assessor_user_id');

            $appraisalforms = AppraisalForm::where('appraisal_cycle_id', 2)
            ->get();

        // foreach ($appraisalforms as $key => $appraisalform) {
        //     echo($appraisalform);
        // }

        return response()->json($appraisalforms);
    }


    public function userforms(Request $request,$id){
        $filter_appraisal_cycle_id = $request->filter_appraisal_cycle_id;

        $appraisalforms = AppraisalForm::where('assessor_user_id', $id)
        ->orderBy('created_at', 'desc')
        ->with(['assformcat','appraisalcycle','status'])->get();

        if (!empty($filter_appraisal_cycle_id)) {
            $appraisalforms = $appraisalforms->where('appraisal_cycle_id', $filter_appraisal_cycle_id);
        }

        $printhistory = PrintHistory::where("assessor_user_id",$id)
                        ->where('appraisal_cycle_id', $filter_appraisal_cycle_id)->first();
        $printed_at = $printhistory ? Carbon::parse($printhistory->printed_at)->format('d-M-Y h:i:s') : null;
        return response()->json(["forms"=>$appraisalforms,"printed_at"=> $printed_at ]);
    }

    public function userdashboard(Request $request,$id){
        $filter_appraisal_cycle_id = $request->filter_appraisal_cycle_id;

        $appraisalforms = AppraisalForm::where('assessor_user_id', $id)
        ->orderBy('created_at', 'desc')
        ->with(['assformcat','appraisalcycle','status'])->get();

        $assessoruser = User::where('id',$id)->with(['employee'])->first();

        $formgroups = [
            "User Forms" =>  $appraisalforms->count(),
            "Finish Forms"=> $appraisalforms->where('status_id',19)->count(),
            "On-hold Forms"=> $appraisalforms->where('status_id',21)->count(),
            "In-progress Forms"=> $appraisalforms->where('status_id',20)->count(),
        ];


        return response()->json(['formgroups' => $formgroups,'assessoruser'=>$assessoruser]);
    }

    public function notification(Request $request){
        $notis = Auth::guard()->user()->unreadNotifications;
        $noti_datas = $notis->pluck('data');

        $appraisalform_ids = $noti_datas->pluck('appraisalform_id');
        // dd($appraisalform_ids);

        $appraisalforms = AppraisalForm::whereIn("id",$appraisalform_ids)
        ->whereHas('appraisalcycle',function($query){
                $todayStr = Carbon::now()->toDateString(); // Get only 'YYYY-MM-DD'
                $query->whereDate('action_start_date', "<=", $todayStr)
                      ->whereDate('action_end_date', ">=", $todayStr);
        })
        ->paginate(10);

        return view("appraisalforms.notification", ['appraisalforms' => $appraisalforms]);
    }

    public function printuserforms($user_id,$appraisal_cycle_id){
        $appraisal_cycle_id = $appraisal_cycle_id;

        $appraisalforms = AppraisalForm::where('assessor_user_id', $user_id)
        ->orderBy('created_at', 'desc')
        ->with(['assformcat','appraisalcycle','status'])->get();

        if (!empty($appraisal_cycle_id)) {
            $appraisalforms = $appraisalforms->where('appraisal_cycle_id', $appraisal_cycle_id);
        }

        // $assesseeusers = $appraisalform->assesseeusers;
        // $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();

        // $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        // $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        // $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        // $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        // $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');

        return view('appraisalforms.printuserforms', compact('appraisalforms'));
    }

}
