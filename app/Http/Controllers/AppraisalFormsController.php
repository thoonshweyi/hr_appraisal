<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Criteria;
use App\Models\AssFormCat;
use App\Models\PeerToPeer;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\Auth;
use App\Models\AppraisalFormAssesseeUser;

class AppraisalFormsController extends Controller
{

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
}
