<?php

namespace App\Http\Controllers\Api;

use App\Models\PrintHistory;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PrintHistoriesController extends Controller
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

        // $user = Auth::user();
        // $user_id = $user->id;

        $printhistory = PrintHistory::create([
            'assessor_user_id' => $request->assessor_user_id,
            'appraisal_cycle_id' => $request->appraisal_cycle_id,
            'printed_at' => now(),
            'user_id' => $request->user_id,
        ]);

        if($printhistory){
                return response()->json(["status"=>"success","data"=>$printhistory]);
        }
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
}
