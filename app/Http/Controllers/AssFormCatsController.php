<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\AssFormCat;
use App\Models\RatingScale;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\PositionLevelImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;

class AssFormCatsController extends Controller
{
    public function index(){

        $assformcats = AssFormCat::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("assformcats.index",compact("assformcats","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:position_levels",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $assformcat = new AssFormCat();
       $assformcat->name = $request["name"];
       $assformcat->status_id = $request["status_id"];
       $assformcat->user_id = $user_id;
       $assformcat->save();
       return redirect(route("assformcats.index"))->with('success',"AssFormCat created successfully");;
    }


    public function edit(Request $request, string $id){
        $assformcat = AssFormCat::find($id);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();

        $ratingscales = RatingScale::orderBy('id', 'asc')->get();


        return view("assformcats.edit",compact("assformcat","statuses","ratingscales"));
    }


    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "name" => ["required","max:50","unique:position_levels,name,".$id],
            "status_id" => ["required","in:1,2"],
        ]);

        dd($request);

        $user = Auth::user();
        $user_id = $user["id"];

        $assformcat = AssFormCat::findOrFail($id);
        $assformcat->name = $request["name"];
        $assformcat->status_id = $request["status_id"];
        $assformcat->user_id = $user_id;
        $assformcat->save();
        return redirect(route("assformcats.index"))->with('success',"AssFormCat updated successfully");
    }

    public function destroy(string $id)
    {
        $assformcat = AssFormCat::findOrFail($id);
        $assformcat->delete();
        return redirect()->back()->with('success',"AssFormCat deleted successfully");
    }

    public function changestatus(Request $request){
        $assformcat = AssFormCat::findOrFail($request["id"]);
        $assformcat->status_id = $request["status_id"];
        $assformcat->save();

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
        //     //     Excel::import(new AssFormCatImport, $request->file('file'));
        //     // }
        //     Excel::import(new AssFormCatImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new AssFormCatImport, $file);

            \DB::commit();
            return redirect(route("assformcats.index"))->with('success',"AssFormCat excel imported successfully");

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
