<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Status;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GradesController extends Controller
{
    public function index(){

        $grades = Grade::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("grades.index",compact("grades","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:rating_scales",
            "status_id" => "required|in:1,2",
            "from_rate" => "required|numeric|min:0|max:100",
            "to_rate" => "required|numeric|min:0|max:100"
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $grade = new Grade();
       $grade->name = $request["name"];
       $grade->slug = Str::slug($request["name"]);
       $grade->status_id = $request["status_id"];
       $grade->from_rate = $request["from_rate"];
       $grade->to_rate = $request["to_rate"];
       $grade->user_id = $user_id;
       $grade->save();
       return redirect(route("grades.index"))->with('success',"Grade created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:rating_scales,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
            "edit_from_rate" => "required|numeric|min:0|max:100",
            "edit_to_rate" => "required|numeric|min:0|max:100"
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $grade = Grade::findOrFail($id);
        $grade->name = $request["edit_name"];
        $grade->slug = Str::slug($request["edit_name"]);
        $grade->status_id = $request["edit_status_id"];
        $grade->from_rate = $request["edit_from_rate"];
        $grade->to_rate = $request["edit_to_rate"];
        $grade->user_id = $user_id;
        $grade->save();
        return redirect(route("grades.index"))->with('success',"Grade updated successfully");
    }

    public function destroy(string $id)
    {
        $grade = Grade::findOrFail($id);
        $grade->delete();
        return redirect()->back()->with('success',"Grade deleted successfully");
    }

    public function changestatus(Request $request){
        $grade = Grade::findOrFail($request["id"]);
        $grade->status_id = $request["status_id"];
        $grade->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }
}
