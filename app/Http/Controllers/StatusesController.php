<?php

namespace App\Http\Controllers;

use App\Models\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatusesController extends Controller
{
    public function index(){

        $statuses = Status::paginate(10);
        return view("statuses.index",compact("statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|unique:statuses,name",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $status = new Status();
       $status->name = $request["name"];
       $status->slug = Str::slug($request["name"]);
       $status->user_id = $user_id;

       $status->save();
       return redirect(route("statuses.index"))->with('success',"Status created successfully");
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "name" => "required|unique:statuses,name,".$id,
        ]);

       $user = Auth::user();
       $user_id = $user['id'];

       $status = Status::findOrFail($id);
       $status->name = $request["name"];
       $status->slug = Str::slug($request["name"]);
       $status->user_id = $user_id;

       $status->save();
       return redirect(route("statuses.index"))->with('success',"Status updated successfully");
    }


    public function destroy(string $id)
    {
        $status = Status::findOrFail($id);
        $status->delete();
        return redirect()->back()->with('success',"Status deleted successfully");
    }


}
