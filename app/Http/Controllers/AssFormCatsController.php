<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Criteria;
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
        $criterias = Criteria::where('ass_form_cat_id',$id)->get();

        $total_excellent =  Criteria::where('ass_form_cat_id',$id)->sum('excellent');
        $total_good =  Criteria::where('ass_form_cat_id',$id)->sum('good');
        $total_meet_standard =  Criteria::where('ass_form_cat_id',$id)->sum('meet_standard');
        $total_below_standard =  Criteria::where('ass_form_cat_id',$id)->sum('below_standard');
        $total_week =  Criteria::where('ass_form_cat_id',$id)->sum('week');

        // dd($total_good);

        return view("assformcats.edit",compact("assformcat","statuses","ratingscales","criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_week"));
    }


    public function update(Request $request, string $id)
    {
        $keys = [];
        $dynamicvalidations = [];
        $ratingscales = RatingScale::orderBy('id','asc')->paginate(10);
        foreach($ratingscales as $ratingscale){
            $key = $keys[] = Str::snake($ratingscale['name'])."s";
            $dynamicvalidations[$key] = 'required|array';
            $dynamicvalidations[$key."*"] = 'required|number';
        }
        // dd([
        //     "name" => ["required","max:50","unique:position_levels,name,".$id],
        //     "status_id" => ["required","in:1,2"],
        //     "names" => "required|array",
        //     "names.*"=>"required|string",
        //     ...$dynamicvalidations
        // ]);


        $dynamicmsgs = [];
        $dynkeys = array_keys($dynamicvalidations);
        // dd($dynkeys);
        foreach($dynkeys as $idx=>$dynkey){
            $dynamicmsgs[$dynkey.".required"] = "Please enter criteria ".$dynkey." values";
        }
        // dd($ratingscales[0]['name']);
        // dd($dynamicmsgs);
        // $this->validate($request,[
        //     "name" => ["required","max:50","unique:position_levels,name,".$id],
        //     "status_id" => ["required","in:1,2"],
        //     "names" => "required|array",
        //     "names.*"=>"required|string",
        //     ...$dynamicvalidations
        // ],[
        //     'names.*.required' => 'Please enter criteria name values.',
        //     ...$dynamicmsgs
        // ]);

        // dd($request);

        // \DB::beginTransaction();


        $user = Auth::user();
        $user_id = $user["id"];

        $assformcat = AssFormCat::findOrFail($id);
        $assformcat->name = $request["name"];
        $assformcat->status_id = $request["status_id"];
        $assformcat->user_id = $user_id;
        $assformcat->save();


        $names = $request->names;
        $excellents = $request->excellents;
        $goods = $request->goods;
        $meet_standards = $request->meet_standards;
        $below_standards = $request->below_standards;
        $weeks = $request->weeks;
        $status_ids = $request->status_ids;

        // dd($excellents);
        $criterias = Criteria::where('ass_form_cat_id',$id)->delete();

        if(!empty($names)){
            foreach($names as $idx=>$name){
                // dd($excellents[$idx]);
                $criteria = Criteria::create([
                    "name" => $name,
                    "status_id" => $status_ids[$idx],
                    "user_id" => $user_id,
                    "ass_form_cat_id" => $assformcat->id,

                    "excellent" => $excellents[$idx],
                    "good" => $goods[$idx],
                    "meet_standard" => $meet_standards[$idx],
                    "below_standard" => $below_standards[$idx],
                    "week" => $weeks[$idx],
                ]);
            }
        }





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
