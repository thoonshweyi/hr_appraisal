<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\DeptGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DeptGroupsController extends Controller
{
    public function index(){

        $deptgroups = DeptGroup::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("deptgroups.index",compact("deptgroups","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:dept_groups",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $deptgroup = new DeptGroup();
       $deptgroup->name = $request["name"];
       $deptgroup->slug = Str::slug($request["name"]);
       $deptgroup->status_id = $request["status_id"];
       $deptgroup->user_id = $user_id;
       $deptgroup->save();
       return redirect(route("deptgroups.index"))->with('success',"Dept Group created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:dept_groups,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $deptgroup = DeptGroup::findOrFail($id);
        $deptgroup->name = $request["edit_name"];
        $deptgroup->slug = Str::slug($request["edit_name"]);
        $deptgroup->status_id = $request["edit_status_id"];
        $deptgroup->user_id = $user_id;
        $deptgroup->save();
        return redirect(route("deptgroups.index"))->with('success',"Dept Group updated successfully");
    }

    public function destroy(string $id)
    {
        $deptgroup = DeptGroup::findOrFail($id);
        $deptgroup->delete();
        return redirect()->back()->with('success',"Rating Scale deleted successfully");
    }

    public function changestatus(Request $request){
        $deptgroup = DeptGroup::findOrFail($request["id"]);
        $deptgroup->status_id = $request["status_id"];
        $deptgroup->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }


   public function excel_import(Request $request){
        // dd($request);
        // return redirect(route("deptgroups.index"))->with('success',"Dept Group has been successfully imported");

        return response()->json(['message' => 'File uploaded successfully!']);


   }
}
