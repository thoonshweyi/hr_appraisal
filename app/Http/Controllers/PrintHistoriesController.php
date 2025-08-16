<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PrintHistoriesController extends Controller
{

    public function store(Request $request)
    {
        dd("hay");
        // $this->validate($request,[
        //     "name" => "required|max:50|unique:print_histories",
        //     "status_id" => "required|in:1,2",
        // ]);

       $user = Auth::user();
       $user_id = $user->id;


    //    $printhistory = PrintHistory::create([
    //         'appraisal_cycle_id' => ,
    //         'printed_at' => now(),
    //         'user_id' => $user_id,
    //    ]);

       return redirect(route("criterias.index"))->with('success',"Criteria created successfully");
    }

}
