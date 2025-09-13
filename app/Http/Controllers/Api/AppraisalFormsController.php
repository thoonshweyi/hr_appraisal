<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Http\Controllers\Controller;
use App\Notifications\AppraisalFormsNotify;
use Illuminate\Support\Facades\Notification;
use App\Http\Controllers\Api\AppraisalFormsController;

class AppraisalFormsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function sendnotis(Request $request){
        try{
            $getselectedids = $request->selectedids ?? [];
            $appraisalforms = AppraisalForm::whereIn("id",$getselectedids)->get();

            foreach($appraisalforms as $appraisalform){
                $alreadySent = \DB::table('notifications')
                    ->where('notifiable_id', $appraisalform->assessor_user_id)
                    ->where('notifiable_type', "App\Models\User")
                    ->where('type', "App\Notifications\AppraisalFormsNotify")
                    ->whereRaw("data::jsonb ->> 'appraisalform_id' = ?", [$appraisalform->id])
                    ->exists();


                if (! $alreadySent) {
                    $title = "You received new Appraisal Form \"$appraisalform->assformcat->name\"";
                    Notification::send($appraisalform->assessoruser,new AppraisalFormsNotify($appraisalform->id,$appraisalform->assformcat->id,$title,$appraisalform->appraisal_cycle_id));
                }
            }


            return response()->json(["success"=>"Selected forms have been sent successfully"]);
        }catch(Exception $e){
            Log::error($e->getMEssage());
            return response()->json(["status"=>"failed","message"=>$e->getMessage()]);
        }
    }

    public function assessordashboard(Request $request){
        // $leaves = Leave::all();
        // $datas = [
        //     "totalleaves" => $leaves->count(),
        //     "approved" => $leaves->where("stage_id",1)->count(),
        //     "pending" => $leaves->where("stage_id",2)->count(),
        //     "rejeted" => $leaves->where("stage_id",3)->count(),
        // ];
        // return response()->json($datas);
        // dd($request->assessor_user_id);
        $activecycle = AppraisalCyclesController::activecycle();
        $datas = [];
        $assessor_user_id = $request->assessor_user_id;

        if($activecycle){
            $appraisalforms = AppraisalForm::where('assessor_user_id',$assessor_user_id)
                                ->where("appraisal_cycle_id",$activecycle->id)
                                ->get();
            // dd($appraisalforms);

            $datas = [
                "totalappraisalforms" => $appraisalforms->count(),
                "inprogress" => $appraisalforms->where("status_id",20)->count(),
                "notstarted" => $appraisalforms->where("status_id",21)->count(),
                "done" => $appraisalforms->where("status_id",19)->count()
            ];

            
        }
        return response()->json($datas);
    }
}
