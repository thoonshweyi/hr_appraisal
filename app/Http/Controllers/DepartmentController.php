<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DepartmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    function __construct()
    {
        // $this->middleware('permission:Department-list|Department-create|Department-edit|Department-delete', ['only' => ['index', 'show']]);
        // $this->middleware('permission:Department-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:Department-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:Department-delete', ['only' => ['destroy']]);
    }
    public function index()
    {
        $departments = Department::latest()->paginate(5);
        return view('departments.index', compact('departments'))
            ->with('i', (request()->input('page', 1) - 1) * 5);
    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    // {pluck('name','name')->
    {
        $branches = Branch::all();
        return view('departments.create', compact('branches'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        request()->validate([
            'name' => 'required',
            'branch_id' => 'required'
        ]);
        Department::create($request->all());
        return redirect()->route('departments.index')
            ->with('success', 'Department created successfully.');
    }
    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Department  $Department
     * @return \Illuminate\Http\Response
     */
    public function show(Department $department)
    {
        return view('departments.show', compact('department'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Departments  $Department
     * @return \Illuminate\Http\Response
     */
    public function edit(Department $department)
    {
        $branches = Branch::all();
        // $branches = Branch::all();
        return view('Departments.edit', compact('department', 'branches'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Departments  $Department
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Department $department)
    {
        request()->validate([
            'name' => 'required',

        ]);
        $update = [];
        if ($files = $request->file('img')) {
            $destinationPath = 'public/image/'; // upload path
            // $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
            $org_file_name = $files->getClientOriginalName();
            $extension = $files->getClientOriginalExtension();
            $filename = $org_file_name . $extension;
            $files->move($destinationPath, $filename);
            $update['img'] = "$org_file_name";
        }
        $update['name'] = $request->get('name');
        $update['created_by']  = Auth::id();
        $department->update($request->all());
        return redirect()->route('departments.index')
            ->with('success', 'Department updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Departments  $Department
     * @return \Illuminate\Http\Response
     */
    public function destroy(Department $department)
    {
        $department->delete();
        return redirect()->route('departments.index')
            ->with('success', 'Department deleted successfully');
    }
}
