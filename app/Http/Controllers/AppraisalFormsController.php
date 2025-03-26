<?php

namespace App\Http\Controllers;

use PDF;
use Exception;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Branch;
use App\Models\Criteria;
use App\Models\AssFormCat;
use App\Models\FormResult;
use App\Models\PeerToPeer;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\Auth;
use App\Models\AppraisalFormAssesseeUser;

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


        $filter_assessor_user_id = $request->filter_assessor_user_id;
        $filter_employee_name = $request->filter_employee_name;
        $filter_appraisal_cycle_id = $request->filter_appraisal_cycle_id;
        $filter_branch_id = $request->filter_branch_id;


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

        if($user->can("view-all-appraisal-form")){

        }else{
            $results = $results
            ->where('assessor_user_id',$user_id)
            ->whereHas('appraisalcycle',function($query){
                $now = Carbon::now();
                $query->where('action_start_date',"<=",$now)
                ->where('action_end_date','>=',$now);
            });
        }

        $appraisalforms = $results->orderBy('id','asc')->paginate(10);



        return view("appraisalforms.index",compact('appraisalforms','branches','appraisalcycles'));
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
            'assessee_user_ids.*.required' => 'Please Assessee User Values.',
        ]);

        $assessor_user_id = $request->assessor_user_id;
        $ass_form_cat_id = $request->ass_form_cat_id;
        $appraisal_cycle_id = $request->appraisal_cycle_id;
        $assessee_user_ids = $request->assessee_user_ids;


        $user = Auth::user();
        $user_id = $user->id;
        $appraisalform = AppraisalForm::create([
            "assessor_user_id"=> $assessor_user_id,
            "ass_form_cat_id"=> $ass_form_cat_id,
            "appraisal_cycle_id"=> $appraisal_cycle_id,
            "user_id"=> $user_id
        ]);

        foreach($assessee_user_ids as $assessee_user_id){
            AppraisalFormAssesseeUser::create([
                "appraisal_form_id" => $appraisalform->id,
                "assessee_user_id" => $assessee_user_id
            ]);
        }

       return redirect()->back()->with('success',"Appraisal form sended to assessor successfully");


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


        return view("appraisalforms.show",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));

    }


    public function edit(Request $request,$id){

        $appraisalform = AppraisalForm::find($id);
        // dd($appraisalform);
            $this->authorize('edit', $appraisalform);
            

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


        return view("appraisalforms.edit",compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));

    }
    public function update(Request $request,$id){

        // dd('submitted');

        $this->validate($request, [
            "appraisalformresults" => "required|array",
            "appraisalformresults.*" => "required|array",
            "appraisalformresults.*.*" => "required", // Ensure all values inside each assessee are filled
        ], [
            'appraisalformresults.*.*.required' => 'Please enter a rating value for each assessee.',
        ]);

        \DB::beginTransaction();
        try{

            $user = Auth::user();
            $user_id = $user->id;

            $appraisalform = AppraisalForm::find($id);
                $this->authorize('edit', $appraisalform);
            $appraisalform->update([
                "assessed" => true,
                "modify_user_id" => $user_id
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

            \DB::commit();
            return redirect(route("appraisalforms.index"))->with('success',"Appraisal Form updated successfully");
        }catch(Exception $err){
            \DB::rollback();

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
            $appraisalform->update([
                "modify_user_id" => $user_id
            ]);

            $appraisalformresults = $request->appraisalformresults;

            $appraisalformresults = array_filter(
                array_map('array_filter', $appraisalformresults)
            );



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
            return redirect(route("appraisalforms.index"))->with('success',"Appraisal Form Saved successfully");
        }catch(Exception $err){
            \DB::rollback();

            return redirect()->back()->with("error","There is an error in submitting Appraisal Form.".$err);
        }
    }

    public function fillform(Request $request){
        // dd('hay');

        $appraisal_cycle_id = $request->appraisal_cycle_id;
        $assessor_user_id = $request->assessor_user_id;
        $ass_form_cat_id = $request->ass_form_cat_id;

        $assessee_ids = PeerToPeer::where('assessor_user_id', $assessor_user_id)
        ->where('appraisal_cycle_id', $appraisal_cycle_id)
        ->where('ass_form_cat_id',$ass_form_cat_id)
        ->get()
        ->pluck('assessee_user_id');
        // dd($assessee_ids);

        $assesseeusers = User::whereIn("id",$assessee_ids)
        ->with(['employee.branch',"employee.department","employee.position","employee.positionlevel"])
        ->get();

        $criterias = Criteria::where("ass_form_cat_id",$ass_form_cat_id)->get();




        return response()->json(["assesseeusers"=>$assesseeusers,"criterias"=>$criterias]);
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
        $assesseeusers = $appraisalform->assesseeusers;
        $criterias = Criteria::where("ass_form_cat_id",$appraisalform->ass_form_cat_id)->get();

        $total_excellent =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('below_standard');
        $total_weak =  Criteria::where('ass_form_cat_id',$appraisalform->ass_form_cat_id)->sum('weak');


        return view('appraisalforms.print', compact('appraisalform','assesseeusers',"criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak"));
    }

}
