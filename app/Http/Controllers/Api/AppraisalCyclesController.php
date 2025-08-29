<?php

namespace App\Http\Controllers\Api;

use App\Models\PeerToPeer;
use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Http\Controllers\Controller;

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

        $assessmentforms = $total = PeerToPeer::selectRaw('COUNT(DISTINCT (assessor_user_id, ass_form_cat_id)) as total')
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

            $statuses = $formsByBranch->groupBy("status_id")->map(function($formsByStatus) use ($total) {
                return [
                    "count" => $formsByStatus->count(),
                    "percentage" => $total > 0
                        ? round(($formsByStatus->count() / $total) * 100, 2)
                        : 0
                ];
            });

            return [
                "assessors" => $assessorCount,
                "statuses"  => $statuses
            ];
        });

        return response()->json($report, 200, [], JSON_PRETTY_PRINT);

    }
}



// {
//     "Lanthit": {
//         "Completed":{
//             count:
//             percentage;
//         }
//     }
// }
