<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\DeptGroup;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\DeptGroupImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;

class DeptGroupsController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-fixed-analysis', ['only' => ['index']]);
        $this->middleware('permission:create-fixed-analysis', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-fixed-analysis', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-fixed-analysis', ['only' => ['destroy']]);
    }

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
        return redirect()->back()->with('success',"Dept Group deleted successfully");
    }

    public function changestatus(Request $request){
        $deptgroup = DeptGroup::findOrFail($request["id"]);
        $deptgroup->status_id = $request["status_id"];
        $deptgroup->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }


   public function excel_import(Request $request)
   {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:2048',
        ]);


          // Multi Images Upload
        //   if($request->hasFile('file')){
        //     // dd('hay');
        //     // foreach($request->file("file") as $image){
        //     //     Excel::import(new DeptGroupImport, $request->file('file'));
        //     // }
        //     Excel::import(new DeptGroupImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new DeptGroupImport, $file);

            \DB::commit();
            return redirect(route("deptgroups.index"))->with('success',"Dept Group excel imported successfully");

        }catch (ExcelImportValidationException $e) {
            // If validation fails, show the error message to the user
            \DB::rollback();
            return back()->with('validation_errors', $e->getMessage());
        } catch (\Exception $e) {
            \DB::rollback();
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }




   }

}
