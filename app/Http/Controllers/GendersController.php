<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Gender;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\GenderImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;


class GendersController extends Controller
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

        $genders = Gender::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("genders.index",compact("genders","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:dept_groups",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $gender = new Gender();
       $gender->name = $request["name"];
       $gender->slug = Str::slug($request["name"]);
       $gender->status_id = $request["status_id"];
       $gender->user_id = $user_id;
       $gender->save();
       return redirect(route("genders.index"))->with('success',"Gender created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:dept_groups,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $gender = Gender::findOrFail($id);
        $gender->name = $request["edit_name"];
        $gender->slug = Str::slug($request["edit_name"]);
        $gender->status_id = $request["edit_status_id"];
        $gender->user_id = $user_id;
        $gender->save();
        return redirect(route("genders.index"))->with('success',"Gender updated successfully");
    }

    public function destroy(string $id)
    {
        $gender = Gender::findOrFail($id);
        $gender->delete();
        return redirect()->back()->with('success',"Gender deleted successfully");
    }

    public function changestatus(Request $request){
        $gender = Gender::findOrFail($request["id"]);
        $gender->status_id = $request["status_id"];
        $gender->save();

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
        //     //     Excel::import(new GenderImport, $request->file('file'));
        //     // }
        //     Excel::import(new GenderImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new GenderImport, $file);

            \DB::commit();
            return redirect(route("genders.index"))->with('success',"Gender excel imported successfully");

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
