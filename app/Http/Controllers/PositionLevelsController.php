<?php

namespace App\Http\Controllers;


use App\Models\Status;
use App\Models\PositionLevel;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\PositionLevelImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;



class PositionLevelsController extends Controller
{
    public function index(){

        $positionlevels = PositionLevel::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("positionlevels.index",compact("positionlevels","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:position_levels",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $positionlevel = new PositionLevel();
       $positionlevel->name = $request["name"];
       $positionlevel->slug = Str::slug($request["name"]);
       $positionlevel->status_id = $request["status_id"];
       $positionlevel->user_id = $user_id;
       $positionlevel->save();
       return redirect(route("positionlevels.index"))->with('success',"PositionLevel created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:position_levels,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $positionlevel = PositionLevel::findOrFail($id);
        $positionlevel->name = $request["edit_name"];
        $positionlevel->slug = Str::slug($request["edit_name"]);
        $positionlevel->status_id = $request["edit_status_id"];
        $positionlevel->user_id = $user_id;
        $positionlevel->save();
        return redirect(route("positionlevels.index"))->with('success',"PositionLevel updated successfully");
    }

    public function destroy(string $id)
    {
        $positionlevel = PositionLevel::findOrFail($id);
        $positionlevel->delete();
        return redirect()->back()->with('success',"PositionLevel deleted successfully");
    }

    public function changestatus(Request $request){
        $positionlevel = PositionLevel::findOrFail($request["id"]);
        $positionlevel->status_id = $request["status_id"];
        $positionlevel->save();

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
        //     //     Excel::import(new PositionLevelImport, $request->file('file'));
        //     // }
        //     Excel::import(new PositionLevelImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new PositionLevelImport, $file);

            \DB::commit();
            return redirect(route("positionlevels.index"))->with('success',"PositionLevel excel imported successfully");

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
