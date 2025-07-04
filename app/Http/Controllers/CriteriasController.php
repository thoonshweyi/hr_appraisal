<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Criteria;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\CriteriaImport;
use App\Imports\CriteriasAllImport;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;


class CriteriasController extends Controller
{
    private $max_total_excellent = 100;
    private $max_total_good = 84;
    private $max_total_meet_standard = 67;
    private $max_total_below_standard = 40;
    private $max_total_weak = 19;
    private $max_totals = [];

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-add-on', ['only' => ['index']]);
        $this->middleware('permission:create-add-on', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-add-on', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-add-on', ['only' => ['destroy']]);

         $this->max_totals = [
            "max_total_excellent" => $this->max_total_excellent,
            "max_total_good" => $this->max_total_good,
            "max_total_meet_standard" => $this->max_total_meet_standard,
            "max_total_below_standard" => $this->max_total_below_standard,
            "max_total_weak" => $this->max_total_weak,
        ];
    }
    public function index(){

        $criterias = Criteria::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("criterias.index",compact("criterias","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:dept_groups",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $criteria = new Criteria();
       $criteria->name = $request["name"];
       $criteria->slug = Str::slug($request["name"]);
       $criteria->status_id = $request["status_id"];
       $criteria->user_id = $user_id;
       $criteria->save();
       return redirect(route("criterias.index"))->with('success',"Criteria created successfully");
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:dept_groups,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $criteria = Criteria::findOrFail($id);
        $criteria->name = $request["edit_name"];
        $criteria->slug = Str::slug($request["edit_name"]);
        $criteria->status_id = $request["edit_status_id"];
        $criteria->user_id = $user_id;
        $criteria->save();
        return redirect(route("criterias.index"))->with('success',"Criteria updated successfully");
    }

    public function destroy(string $id)
    {
        $criteria = Criteria::findOrFail($id);
        $criteria->delete();
        return redirect()->back()->with('success',"Criteria deleted successfully");
    }

    public function changestatus(Request $request){
        $criteria = Criteria::findOrFail($request["id"]);
        $criteria->status_id = $request["status_id"];
        $criteria->save();

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
        //     //     Excel::import(new CriteriaImport, $request->file('file'));
        //     // }
        //     Excel::import(new CriteriaImport, $request->file('file'));

        // }
        $ass_form_cat_id = $request->ass_form_cat_id;

        \DB::beginTransaction();
        try {
            $file = $request->file('file');
            Excel::import(new CriteriaImport($ass_form_cat_id,$this->max_totals), $file);

            \DB::commit();
            return redirect()->back()->with('success',"Criteria excel imported successfully");

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


   public function all_excel_import(Request $request)
   {
        $request->validate([
            'file' => 'required|mimes:xls,xlsx|max:2048',
        ]);


          // Multi Images Upload
        //   if($request->hasFile('file')){
        //     // dd('hay');
        //     // foreach($request->file("file") as $image){
        //     //     Excel::import(new CriteriaImport, $request->file('file'));
        //     // }
        //     Excel::import(new CriteriaImport, $request->file('file'));

        // }
        // $ass_form_cat_id = $request->ass_form_cat_id;

        \DB::beginTransaction();
        try {
            $file = $request->file('file');
            Excel::import(new CriteriasAllImport($this->max_totals), $file);

            \DB::commit();
            return redirect()->back()->with('success',"Criteria excel imported successfully");

        }catch (ExcelImportValidationException $e) {
            // If validation fails, show the error message to the user
            \DB::rollback();
            Log::info($e);
            return back()->with('validation_errors', $e->getMessage());
        } catch (\Exception $e) {
            \DB::rollback();
            Log::info($e);
            // Handle the exception and notify the user
            return redirect()->back()->with('error', "System Error:".$e->getMessage());
        }




   }
}
