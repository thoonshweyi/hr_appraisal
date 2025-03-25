<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\Criteria;
use App\Models\Rankable;
use App\Models\AssFormCat;
use App\Models\RatingScale;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\PositionLevel;
use App\Models\AttachFormType;
use Illuminate\Support\Facades\Log;
use App\Imports\PositionLevelImport;
use Illuminate\Support\Facades\Auth;
use Maatwebsite\Excel\Facades\Excel;
use App\Exceptions\ExcelImportValidationException;

class AssFormCatsController extends Controller
{

    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-add-on', ['only' => ['index']]);
        $this->middleware('permission:create-add-on', ['only' => ['create', 'store']]);
        $this->middleware('permission:edit-add-on', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-add-on', ['only' => ['destroy']]);
    }
    public function index(Request $request){

        $results = AssFormCat::query();
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();

        $filter_name = $request->filter_name;
        $filter_position_level_id = $request->filter_position_level_id;
        $filter_attachformtype_id = $request->filter_attachformtype_id;


        if (!empty($filter_name)) {
            $results = $results->where('name', 'like', '%'.$filter_name.'%');
        }

        if (!empty($filter_position_level_id)) {
            $results = $results->whereHas('positionlevels',function($query) use($filter_position_level_id){
                $query->where('position_levels.id',$filter_position_level_id);
            });
        }

        if (!empty($filter_attachformtype_id)) {
            $results = $results->where('attach_form_type_id', $filter_attachformtype_id);
        }

        $assformcats = $results->orderBy('id','asc')->paginate(10);

        // dd($statuses);
        return view("assformcats.index",compact("assformcats","statuses","positionlevels","attachformtypes"));
    }



    public function create(Request $request){
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();

        $ratingscales = RatingScale::orderBy('id', 'asc')->get();

        $total_excellent =  0;
        $total_good =  0;
        $total_meet_standard = 0;
        $total_below_standard =  0;
        $total_weak =  0;

        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();

        // dd($total_good);

        return view("assformcats.create",compact("statuses","ratingscales","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak","positionlevels","attachformtypes"));
    }


    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:255|unique:position_levels",
            "status_id" => "required|in:1,2",
            "position_level_ids" => "required|array",
            "position_level_ids.*"=>"required|string",
            "attach_form_type_id" => "required",
        ],[
            'position_level_ids.*.required' => 'Please enter position level values.',
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $assformcat = new AssFormCat();
       $assformcat->name = $request["name"];
       $assformcat->status_id = $request["status_id"];
       $assformcat->user_id = $user_id;
       $assformcat->attach_form_type_id = $request["attach_form_type_id"];
       $assformcat->save();

       $position_level_ids = $request->position_level_ids;

        foreach($position_level_ids  as $key=>$value){
            $rankable = [
                "position_level_id"=>$value,
                "rankable_id"=>$assformcat["id"],
                // "rankable_type"=>$request["rankable_type"]
                "rankable_type"=> "App\Models\AssFormCat"
            ];
            Rankable::insert($rankable);
        }


       return redirect(route("assformcats.index"))->with('success',"AssFormCat created successfully");
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
        $total_weak =  Criteria::where('ass_form_cat_id',$id)->sum('weak');

        $positionlevels = PositionLevel::where('status_id',1)->orderBy('id')->get();
        $attachformtypes = AttachFormType::where('status_id',1)->orderBy('id')->get();

        // dd($total_good);
        // dd($assformcat->positionlevels->pluck('id')->toArray());

        return view("assformcats.edit",compact("assformcat","statuses","ratingscales","criterias","total_excellent","total_good","total_meet_standard","total_below_standard","total_weak","positionlevels","attachformtypes"));
    }


    public function update(Request $request, string $id)
    {

        // \DB::beginTransaction();



        $this->validate($request,[
            "name" => ["required","max:50","unique:position_levels,name,".$id],
            "status_id" => ["required","in:1,2"],
            "names" => "required|array",
            "names.*"=>"required|string",
            "excellents" => "required|array",
            "excellents.*"=>"required|string",
            "goods" => "required|array",
            "goods.*"=>"required|string",
            "meet_standards" => "required|array",
            "meet_standards.*"=>"required|string",
            "below_standards" => "required|array",
            "below_standards.*"=>"required|string",
            "weaks" => "required|array",
            "weaks.*"=>"required|string",
            "position_level_ids" => "required|array",
            "position_level_ids.*"=>"required|string",
            "attach_form_type_id" => "required",

        ],[
            'names.*.required' => 'Please enter criteria name values.',
            'excellents.*.required' => 'Please enter excellent values.',
            'goods.*.required' => 'Please enter good values.',
            'meet_standards.*.required' => 'Please enter meet standard values.',
            'below_standards.*.required' => 'Please enter below standard values.',
            'weaks.*.required' => 'Please enter weak values.',
            'position_level_ids.*.required' => 'Please enter position level values.',
        ]);



        $user = Auth::user();
        $user_id = $user["id"];

        $assformcat = AssFormCat::findOrFail($id);
        $assformcat->name = $request["name"];
        $assformcat->status_id = $request["status_id"];
        $assformcat->user_id = $user_id;
        $assformcat->attach_form_type_id = $request["attach_form_type_id"];
        $assformcat->save();


        $names = $request->names;
        $excellents = $request->excellents;
        $goods = $request->goods;
        $meet_standards = $request->meet_standards;
        $below_standards = $request->below_standards;
        $weaks = $request->weaks;
        $status_ids = $request->status_ids;

        $position_level_ids = $request->position_level_ids;


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
                    "weak" => $weaks[$idx],
                ]);
            }
        }



        $position_level_ids = $request->position_level_ids;
        $assformcat->positionlevels()->sync($position_level_ids);

        return redirect(route("assformcats.index"))->with('success',"AssFormCat updated successfully");
    }

    public function destroy(string $id)
    {
        $assformcat = AssFormCat::findOrFail($id);
        $assformcat->delete();


        $assformcat->criterias()->delete();
        $assformcat->positionlevels()->detach();

        return redirect()->back()->with('success',"AssFormCat deleted successfully");
    }

    public function changestatus(Request $request){
        $assformcat = AssFormCat::findOrFail($request["id"]);
        $assformcat->status_id = $request["status_id"];
        $assformcat->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }


}
