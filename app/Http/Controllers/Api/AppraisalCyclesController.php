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
}
