<?php

namespace App\Http\Controllers\Api;

use Carbon\Carbon;
use App\Models\PeerToPeer;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use App\Http\Controllers\Controller;
use App\Http\Resources\AppraisalCyclesResource;

class AppraisalCyclesController extends Controller
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

    public function assessorformsdashboard($id){
        $peertopeers = PeerToPeer::where("appraisal_cycle_id",$id);
        $appraisalforms = AppraisalForm::where("appraisal_cycle_id",$id);

        $totalassessors = $peertopeers
                            ->distinct("assessor_user_id")
                            ->count();

        $assessmentforms = $total = PeerToPeer::where("appraisal_cycle_id",$id)->selectRaw('COUNT(DISTINCT (assessor_user_id, ass_form_cat_id)) as total')
        ->value('total');

        $readys = $appraisalforms->count();

        $pendings = $assessmentforms - $readys;



        $datas = [
            "totalassessors" => $totalassessors,
            "assessmentforms" => $assessmentforms,
            "readys" => $readys,
            "pendings" => $pendings,
        ];

        return response()->json($datas);
    }

    public function bybranchesdashboard($id){
        // $appraisalforms = AppraisalForm::where("appraisal_cycle_id",$id)
        // ->with('assessoruser.employee.branch')
        // ->get()
        // ->groupBy("status_id","branch_id");

        // dd($appraisalforms);


        $appraisalforms = AppraisalForm::where("appraisal_cycle_id", $id)
            ->with("assessoruser.employee.branch")
            ->get();

        $report = $appraisalforms->groupBy(function($form){
            return $form->assessoruser->employee->branch->branch_name ?? 'Unknown';
        })->map(function($formsByBranch){
            $total = $formsByBranch->count();
            $assessorCount = $formsByBranch->pluck('assessor_user_id')->unique()->count();


            $allStatuses = [19,20,21];
            $statuses = [];
            foreach ($allStatuses as $statusId) {
                $statuses[$statusId] = ["count" => 0, "percentage" => 0];
            }

            // Fill actual counts
            foreach ($formsByBranch->groupBy("status_id") as $statusId => $formsByStatus) {
                $statuses[$statusId]["count"] = $formsByStatus->count();
                $statuses[$statusId]["percentage"] = $total > 0
                    ? round(($formsByStatus->count() / $total) * 100, 2)
                    : 0;
            }

            return [
                "assessors" => $assessorCount,
                "statuses"  => $statuses
            ];
        });

        return response()->json($report, 200, [], JSON_PRETTY_PRINT);

    }

    public function appraisalformdashboard($id)
    {
        $appraisalforms = AppraisalForm::where("appraisal_cycle_id", $id)->get();

        $total = $appraisalforms->groupBy("assessor_user_id")->count();

        $completed   = $appraisalforms->where("status_id", 19)->count();
        $inprogress  = $appraisalforms->where("status_id", 20)->count();
        $notstarted  = $appraisalforms->where("status_id", 21)->count();

        $datas = [
            "totalemployees" => $total,
            "completed"      => $completed,
            "inprogress"     => $inprogress,
            "notstarted"     => $notstarted,
        ];

        return response()->json($datas, 200, [], JSON_PRETTY_PRINT);
    }

    public static function activecycle(){
        $todayStr = Carbon::now()->toDateString(); // Get only 'YYYY-MM-DD'

        $appraisalcycle = AppraisalCycle::whereDate('action_start_date', "<=", $todayStr)
                            ->whereDate('action_end_date', ">=", $todayStr)->first();

        // dd($appraisalcycle);
        if($appraisalcycle){
            return new AppraisalCyclesResource($appraisalcycle);
        }else{
            return null;
        }

    }
}

