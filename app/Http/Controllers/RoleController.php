<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Artisan;
use Spatie\Permission\Models\Permission;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:view-roles', ['only' => ['index']]);
        $this->middleware('permission:create-role', ['only' => ['create', 'store', 'generate_doc_no']]);
        $this->middleware('permission:edit-role', ['only' => ['edit', 'update']]);
        $this->middleware('permission:delete-role', ['only' => ['destroy']]);
    }

    protected function connection()
    {
        return new Role();
    }

    public function index(Request $request)
    {
        try{
            if ($request->ajax()) {
                $role_name=  (!empty($_GET["role_name"])) ? ($_GET["role_name"]) : ('');

                $result =  $this->connection();
                if($role_name != ""){
                    $result = $result->Where('name', 'like', '%' . $role_name . '%');
                }
                $result = $result->get();
                return DataTables::of($result)
                ->editColumn('role_name', function ($data) {
                    return $data->name ? $data->name  : '';
                })
                ->addColumn('action', function ($data) {
                    return 'action';
                })
                ->make(true);
            }
            return view('roles.index');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("home"))
                ->with('error', 'Fail to show Roles!');
        }
    }

    public function create(Request $request)
    {
        try{
            $dashboard_permission = Permission::where('group_name','dashboard')->get();
            $document_permission = Permission::where('group_name','document')->get();
            $document_remark = Permission::where('group_name','document-remark')->get();
            $document_attach = Permission::where('group_name','document-attach')->get();
            $document_export = Permission::where('group_name','document-export')->get();
            $document_update = Permission::where('group_name','document-update')->get();

            $product_permission = Permission::where('group_name','product')->get();
            $user_permission = Permission::where('group_name','user')->get();
            $role_permission = Permission::where('group_name','role')->get();
            $supplier_permission = Permission::where('group_name','supplier')->get();
            $branch_permission = Permission::where('group_name','branch')->get();
            $faqs_permissions = Permission::where('group_name','faqs')->get();

            $my_document = Permission::where('group_name','my-document')->get();
            return view('roles.create',compact('dashboard_permission','document_permission','document_remark',
        'document_attach','document_export','document_update','product_permission','user_permission','role_permission','supplier_permission','branch_permission','my_document','faqs_permissions'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("roles.index"))
                ->with('error', 'Fail to show create File!');
        }
    }

    public function store(Request $request)
    {
        // try{
            // dd($request->permission);
            $this->validate($request, [
                'name' => 'required|unique:roles,name',
                'permission' => 'required',
                'profile_image' => 'required|max:2048|mimes:png',
            ]);

            $filename = $request->name.'.png';
            $request->profile_image->move(public_path('images/user'), $filename);
            $role = Role::create(['name' => $request->input('name')]);
            $role->syncPermissions($request->input('permission'));

            return redirect()->route('roles.index')
                            ->with('success','Role created successfully');
        // }catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("roles.index"))
        //         ->with('error', 'Fail to store Role!');
        // }
    }

    public function show($id)
    {
        try{
            $role = Role::find($id);
            $rolePermissions = Permission::join("role_has_permissions","role_has_permissions.permission_id","=","permissions.id")
                ->where("role_has_permissions.role_id",$id)
                ->get();

            return view('roles.show',compact('role','rolePermissions'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("roles.index"))
                ->with('error', 'Fail to show Role!');
        }
    }

    public function edit($id)
    {
        try{
            $role = Role::find($id);

            $dashboard_permission = Permission::where('group_name','dashboard')->get();
            $document_permission = Permission::where('group_name','document')->get();
            $document_remark = Permission::where('group_name','document-remark')->get();
            $document_attach = Permission::where('group_name','document-attach')->get();
            $document_export = Permission::where('group_name','document-export')->get();
            $document_update = Permission::where('group_name','document-update')->get();

            $product_permission = Permission::where('group_name','product')->get();
            $user_permission = Permission::where('group_name','user')->get();
            $role_permission = Permission::where('group_name','role')->get();
            $supplier_permission = Permission::where('group_name','supplier')->get();
            $branch_permission = Permission::where('group_name','branch')->get();

            $my_document = Permission::where('group_name','my-document')->get();
            $faqs_permissions = Permission::where('group_name','faqs')->get();
            $rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                ->all();

            return view('roles.edit',compact('role','dashboard_permission','document_permission','document_remark',
            'document_attach','document_export','document_update','product_permission','user_permission','role_permission','supplier_permission','branch_permission','my_document','rolePermissions','faqs_permissions'));
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("roles.index"))
                ->with('error', 'Fail to show Role Edit!');
        }
    }

    public function update(Request $request, $id)
    {
        // try{
            $this->validate($request, [
                'name' => 'required',
                'permission' => 'required',
            ]);
            if ($request->hasFile('profile_image')) {
                $this->validate($request, [
                    'profile_image' => 'required|max:2048|mimes:png',
                ]);
                $filename = $request->name.'.png';
                $request->profile_image->move(public_path('images/user'), $filename);
                Artisan::call('cache:clear');
            }
            $role = Role::find($id);
            $role->name = $request->input('name');
            $role->save();

            $role->syncPermissions($request->input('permission'));

            return redirect()->route('roles.index')
                            ->with('success','Role updated successfully');
        // }catch (\Exception $e) {
        //     Log::debug($e->getMessage());
        //     return redirect()
        //         ->intended(route("roles.index"))
        //         ->with('error', 'Fail to update Role!');
        // }
    }

    public function destroy($id)
    {
        try{
            DB::table("roles")->where('id',$id)->delete();
            return redirect()->route('roles.index')
                            ->with('success','Role deleted successfully');
        }catch (\Exception $e) {
            Log::debug($e->getMessage());
            return redirect()
                ->intended(route("roles.index"))
                ->with('error', 'Fail to delete Role!');
        }
    }
}
