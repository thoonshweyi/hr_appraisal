<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\BranchType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\BranchTypeImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;


class BranchTypesController extends Controller
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

        $branchtypes = BranchType::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("branchtypes.index",compact("branchtypes","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:dept_groups",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $branchtype = new BranchType();
       $branchtype->name = $request["name"];
       $branchtype->slug = Str::slug($request["name"]);
       $branchtype->status_id = $request["status_id"];
       $branchtype->user_id = $user_id;
       $branchtype->save();
       return redirect(route("branchtypes.index"))->with('success',"BranchType created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:dept_groups,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $branchtype = BranchType::findOrFail($id);
        $branchtype->name = $request["edit_name"];
        $branchtype->slug = Str::slug($request["edit_name"]);
        $branchtype->status_id = $request["edit_status_id"];
        $branchtype->user_id = $user_id;
        $branchtype->save();
        return redirect(route("branchtypes.index"))->with('success',"BranchType updated successfully");
    }

    public function destroy(string $id)
    {
        $branchtype = BranchType::findOrFail($id);
        $branchtype->delete();
        return redirect()->back()->with('success',"BranchType deleted successfully");
    }

    public function changestatus(Request $request){
        $branchtype = BranchType::findOrFail($request["id"]);
        $branchtype->status_id = $request["status_id"];
        $branchtype->save();

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
        //     //     Excel::import(new BranchTypeImport, $request->file('file'));
        //     // }
        //     Excel::import(new BranchTypeImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new BranchTypeImport, $file);

            \DB::commit();
            return redirect(route("branchtypes.index"))->with('success',"BranchType excel imported successfully");

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
