<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables;

class SupplierController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:view-suppliers', ['only' => ['index']]);
    }

    public function index(Request $request)
    {
        try{
            if ($request->ajax()) {
                $supplier_code=  (!empty($_GET["supplier_code"])) ? ($_GET["supplier_code"]) : ('');
                $supplier_name=  (!empty($_GET["supplier_name"])) ? ($_GET["supplier_name"]) : ('');

                $result =  Supplier::query();
                if($supplier_code != ""){
                    $result = $result->where('vendor_code', 'like', '%' . $supplier_code . '%');
                }
                if($supplier_name != ""){
                    $result = $result->where('vendor_name', 'like', '%' . $supplier_name . '%');
                }
                return DataTables::of($result)->make(true);          
            }
            return view('suppliers.index');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to show Suppliers!');
        }
    }

    public function create()
    {
        try{
            return view('suppliers.create');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to show Create Form!');
        }
    }

    public function store(Request $request)
    {
        try{
            request()->validate([
                'supplier_code' => 'required',
                'supplier_name' => 'required'

            ]);

            $request['created_by']  = Auth::id();
            Supplier::create($request->all());

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier created successfully.');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to Store Supplier!');
        }
    }

    public function show(Supplier $supplier)
    {
        try{
            return view('suppliers.show', compact('supplier'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to load Supplier!');
        }
    }

    public function edit(Supplier $supplier)
    {
        try{
            return view('suppliers.edit', compact('supplier'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to load Edit Form!');
        }
    }

    public function update(Request $request, Supplier $supplier)
    {
        try{
            request()->validate([
                'vendor_code' => 'required',
                'vendor_name' => 'required'
            ]);

            $update = [];
            if ($files = $request->file('img')) {
                $destinationPath = 'public/image/'; 
                // $profileImage = date('YmdHis') . "." . $files->getClientOriginalExtension();
                $org_file_name = $files->getClientOriginalName();
                $extension = $files->getClientOriginalExtension();
                $filename = $org_file_name . $extension;
                $files->move($destinationPath, $filename);
                $update['img'] = "$org_file_name";
            }

            $update['vendor_code'] = $request->get('vendor_code');
            $update['vendor_name'] = $request->get('vendor_name');
            $update['created_by']  = Auth::id();
            $supplier->update($request->all());

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier updated successfully');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to update Supplier!');
        }
    }

    public function destroy(Supplier $supplier)
    {
        try{
            $supplier->delete();

            return redirect()->route('suppliers.index')
                ->with('success', 'Supplier deleted successfully');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("suppliers.index"))
                ->with('error', 'Fail to delete Supplier!');
        }
    }
}
