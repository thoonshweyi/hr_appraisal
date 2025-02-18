<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;
use Yajra\DataTables\Contracts\DataTable;

class BranchController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:view-branches', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        try{
            if ($request->ajax()) {
                $branch_name =  (!empty($_GET["branch_name"])) ? ($_GET["branch_name"]) : ('');
                $branch_short_name =  (!empty($_GET["branch_short_name"])) ? ($_GET["branch_short_name"]) : ('');
    
                $result =  Branch::query();
                if ($branch_name != "") {
                    $result = $result->Where('branch_name', 'like', '%' . $branch_name . '%');
                }
                if ($branch_short_name != "") {
                    $result = $result->Where('branch_short_name', 'like', '%' . $branch_short_name . '%');
                }
                return DataTables::of($result)->make(true);
            }
            return view('branches.index');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to load Data!');
        }
       
    }

    public function create()
    {
        try{
            return view('branches.create');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to show Create Form!');
        }
    }

    public function store(Request $request)
    {
        try{
            request()->validate([
                'branch_name' => 'required',
                'branch_address' => 'required'
            ]);

            $request['created_by']  = Auth::id();
            Branch::create($request->all());

            return redirect()->route('branches.index')
                ->with('success', 'Brand created successfully.');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to Store Branch!');
        }
    }

    public function show(Branch $branch)
    {
        try{
            return view('branches.show', compact('branch'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to load Branch!');
        }
    }

    public function edit(Branch $branch)
    {
        try{
            return view('branches.edit', compact('branch'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to load Edit Form!');
        }
    }

    public function update(Request $request, Branch $branch)
    {
        try{
          //
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to update Branch!');
        }
    }

    public function destroy(Branch $branch)
    {
        try{
            $branch->delete();
            return redirect()->route('branches.index')
                ->with('success', 'Brand deleted successfully');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("branches.index"))
                ->with('error', 'Fail to delete Branch!');
        }
    }
}
