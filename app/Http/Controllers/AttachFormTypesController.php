<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\AttachFormType;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Imports\AttachFormTypeImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;


class AttachFormTypesController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-fixed-analysis', ['only' => ['index']]);
        $this->middleware('permission:create-fixed-analysis', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-fixed-analysis', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-fixed-analysis', ['only' => ['destroy']]);
    }
    public function index(Request $request){
        $filter_name = $request->filter_name;

        // $attachformtypes = AttachFormType::orderBy('id','asc')->paginate(10);
        $results = AttachFormType::query();

        if (!empty($filter_name)) {
            $results = $results->where('name', 'like', '%'.$filter_name.'%');
        }

        $attachformtypes = $results->orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();

        // dd($statuses);
        return view("attachformtypes.index",compact("attachformtypes","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:attach_form_types",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $attachformtype = new AttachFormType();
       $attachformtype->name = $request["name"];
       $attachformtype->slug = Str::slug($request["name"]);
       $attachformtype->status_id = $request["status_id"];
       $attachformtype->user_id = $user_id;
       $attachformtype->save();
       return redirect(route("attachformtypes.index"))->with('success',"AttachFormType created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:attach_form_types,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $attachformtype = AttachFormType::findOrFail($id);
        $attachformtype->name = $request["edit_name"];
        $attachformtype->slug = Str::slug($request["edit_name"]);
        $attachformtype->status_id = $request["edit_status_id"];
        $attachformtype->user_id = $user_id;
        $attachformtype->save();
        return redirect(route("attachformtypes.index"))->with('success',"AttachFormType updated successfully");
    }

    public function destroy(string $id)
    {
        $attachformtype = AttachFormType::findOrFail($id);
        $attachformtype->delete();
        return redirect()->back()->with('success',"AttachFormType deleted successfully");
    }

    public function changestatus(Request $request){
        $attachformtype = AttachFormType::findOrFail($request["id"]);
        $attachformtype->status_id = $request["status_id"];
        $attachformtype->save();

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
        //     //     Excel::import(new AttachFormTypeImport, $request->file('file'));
        //     // }
        //     Excel::import(new AttachFormTypeImport, $request->file('file'));

        // }

        \DB::beginTransaction();

        try {
            $file = $request->file('file');
            Excel::import(new AttachFormTypeImport, $file);

            \DB::commit();
            return redirect(route("attachformtypes.index"))->with('success',"AttachFormType excel imported successfully");

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
