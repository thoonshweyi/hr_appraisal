<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Branch;
use App\Models\Gender;
use App\Models\Status;
use App\Models\Section;
use App\Models\Division;
use App\Models\Employee;
use App\Models\Position;
use App\Models\AssFormCat;
use App\Models\BranchUser;
use App\Models\FormResult;
use App\Models\PeerToPeer;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\PositionLevel;
use App\Models\SubDepartment;
use App\Imports\SectionImport;
use App\Models\AppraisalCycle;
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
use App\Models\AppraisalFormAssesseeUser;
use App\Exceptions\ExcelImportValidationException;

class PeerToPeersController extends Controller
{
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

        // dd($branches);
        $users = User::where('status',1)->get();

        $appraisalcycles = AppraisalCycle::where('status_id',1)->orderBy('id')->get();


        $assformcats = AssFormCat::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();




        return view("peertopeers.create",compact("statuses","divisions","departments","subdepartments","sections","positions","branches","genders","positionlevels","users","appraisalcycles",'attachformtypes',"assformcats"));
    }


    public function store(Request $request)
    {
        $this->validate($request,[
             "assessor_user_id" => "required",
             "appraisal_cycle_id" => "required",
             "asssessee_user_ids" => "required|array",
             "asssessee_user_ids.*"=>"required|string",
             "ass_form_cat_ids" => "required|array",
             "ass_form_cat_ids.*"=>"required|string",

        ],[
            'asssessee_user_ids.*.required' => 'Please enter criteria name values.',
            'ass_form_cat_ids.*.required' => 'Please enter excellent values.',
        ]);


        \DB::beginTransaction();

        try {
            $assessor_user_id = $request->assessor_user_id;
            $asssessee_user_ids = $request->asssessee_user_ids;
            $ass_form_cat_ids = $request->ass_form_cat_ids;
            $appraisal_cycle_id = $request->appraisal_cycle_id;


            foreach($asssessee_user_ids as $idx=>$asssessee_user_id){
                $peertopeer = PeerToPeer::create([
                    "assessor_user_id" => $assessor_user_id,
                    "assessee_user_id" => $asssessee_user_ids[$idx],
                    "ass_form_cat_id" => $ass_form_cat_ids[$idx],
                    "appraisal_cycle_id" => $appraisal_cycle_id
                ]);
            }


            // Revoking Appraisal Form
            $this->revokeAppraisalForms($appraisal_cycle_id,$assessor_user_id,$ass_form_cat_ids);

            \DB::commit();
            return redirect(route("appraisalcycles.edit",$appraisal_cycle_id))->with('success',"Peer To Peer created successfully");;
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }
    }

    public function getAssessorAssessees(Request $request){
        $assessor_user_id = $request->assessor_user_id;
        $appraisal_cycle_id = $request->appraisal_cycle_id;


        // dd($assessor_user_id,$appraisal_cycle_id);


        $peertopeers = PeerToPeer::where('assessor_user_id',$assessor_user_id)
                        ->where('appraisal_cycle_id',$appraisal_cycle_id)
                        ->with(["assessoruser.employee"])
                        ->with(["assesseeuser.employee.branch","assesseeuser.employee.department","assesseeuser.employee.position","assesseeuser.employee.positionlevel"])
                        ->with(["assformcat"])
                        ->get();
        // dd($peertopeers);

        return response()->json($peertopeers);

    }


    public function destroy(string $id)
    {
        \DB::beginTransaction();

        try {

            $peertopeer = PeerToPeer::findOrFail($id);
            $peertopeer->delete();



            // Revoking Appraisal Form
            $appraisal_cycle_id = $peertopeer->appraisal_cycle_id;
            $assessor_user_id = $peertopeer->assessor_user_id;
            $ass_form_cat_id = $peertopeer->ass_form_cat_id;
            $this->revokeAppraisalForms($appraisal_cycle_id,$assessor_user_id,[$ass_form_cat_id]);

            \DB::commit();
            return redirect()->back()->with('success',"PeerToPeer deleted successfully");
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }
    }


    public function revokeAppraisalForms($appraisal_cycle_id,$assessor_user_id,$ass_form_cat_ids){
        $appraisalforms = AppraisalForm::where('appraisal_cycle_id', $appraisal_cycle_id)
        ->where('assessor_user_id', $assessor_user_id)
        ->whereIn('ass_form_cat_id', $ass_form_cat_ids)
        ->get();

        foreach ($appraisalforms as $form) {
            AppraisalFormAssesseeUser::where('appraisal_form_id', $form->id)->delete();
            FormResult::where('appraisal_form_id', $form->id)->delete();

            $form->delete();
        }
    }
}
