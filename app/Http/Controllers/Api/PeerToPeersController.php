<?php

namespace App\Http\Controllers\Api;

use App\Models\PeerToPeer;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PeerToPeersController extends Controller
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


    public function dashboard(){

    }

    public function assessmentnetwork($assessor_user_id,$appraisal_cycle_id){
        $totalassessees = PeerToPeer::where('assessor_user_id',$assessor_user_id)
                        ->where('appraisal_cycle_id',$appraisal_cycle_id)
                        ->count();

        $totalassessors = PeerToPeer::where('assessee_user_id',$assessor_user_id)
        ->where('appraisal_cycle_id',$appraisal_cycle_id)
        ->count();


        $assessmentnetworksrcs = [
            "Employee's Assessors" => $totalassessors,
            "Employee's Assessees" => $totalassessees,
       ];

       return response()->json([
            "assessmentnetworksrcs" => $assessmentnetworksrcs
       ]);


    }
}
