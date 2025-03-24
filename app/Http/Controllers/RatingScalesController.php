<?php

namespace App\Http\Controllers;

use App\Models\Status;
use App\Models\RatingScale;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RatingScalesController extends Controller
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

        $ratingscales = RatingScale::orderBy('id','asc')->paginate(10);
        $statuses = Status::whereIn('id',[1,2])->orderBy('id')->get();
        // dd($statuses);
        return view("ratingscales.index",compact("ratingscales","statuses"));
    }

    public function store(Request $request)
    {
        $this->validate($request,[
            "name" => "required|max:50|unique:rating_scales",
            "status_id" => "required|in:1,2",
        ]);

       $user = Auth::user();
       $user_id = $user->id;

       $ratingscale = new RatingScale();
       $ratingscale->name = $request["name"];
       $ratingscale->slug = Str::slug($request["name"]);
       $ratingscale->status_id = $request["status_id"];
       $ratingscale->user_id = $user_id;
       $ratingscale->save();
       return redirect(route("ratingscales.index"))->with('success',"Raging Scale created successfully");;
    }

    public function update(Request $request, string $id)
    {
        $this->validate($request,[
            "edit_name" => ["required","max:50","unique:rating_scales,name,".$id],
            "edit_status_id" => ["required","in:1,2"],
        ]);

        $user = Auth::user();
        $user_id = $user["id"];

        $ratingscale = RatingScale::findOrFail($id);
        $ratingscale->name = $request["edit_name"];
        $ratingscale->slug = Str::slug($request["edit_name"]);
        $ratingscale->status_id = $request["edit_status_id"];
        $ratingscale->user_id = $user_id;
        $ratingscale->save();
        return redirect(route("ratingscales.index"))->with('success',"Raging Scale updated successfully");
    }

    public function destroy(string $id)
    {
        $ratingscale = RatingScale::findOrFail($id);
        $ratingscale->delete();
        return redirect()->back()->with('success',"Rating Scale deleted successfully");
    }

    public function changestatus(Request $request){
        $ratingscale = RatingScale::findOrFail($request["id"]);
        $ratingscale->status_id = $request["status_id"];
        $ratingscale->save();

        return response()->json(["success"=>"Stage Change Successfully"]);
   }
}
