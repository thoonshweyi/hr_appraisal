<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateRoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
        $data= [
            [
                "name" => "OperationPerson",
            ],
            [
                "name" => "BranchManager",
            ],
            [
                "name" => "CategoryHead",
            ],
            [
                "name" => "MerchandisingManager",
            ],
            [
                "name" => "RGOut",
            ],
            [
                "name" => "Accounting",
            ],
            [
                "name" => "RGIn",
            ]
        ];

        foreach ($data as $value) {
            $role = Role::create($value);
            if($role->name == "OperationPerson"){
                $permissions = Permission::whereIn('permission_id', ['1','2','3','4'])->pluck('id', 'id')->all();
            }else if($role->name == "BranchManager"){
                $permissions = Permission::whereIn('id', ['2','3','4','5','6'])->pluck('id', 'id')->all();
            }else if($role->name == "CategoryHead"){
                $permissions = Permission::whereIn('id', ['2','3','7','8'])->pluck('id', 'id')->all();
            }else if($role->name == "MerchandisingManager"){
                $permissions = Permission::whereIn('id', ['2','3','9','10'])->pluck('id', 'id')->all();
            }else if($role->name == "RGOut"){
                $permissions = Permission::whereIn('id', ['2','3','11','15'])->pluck('id', 'id')->all();
            }else if($role->name == "Accounting"){
                $permissions = Permission::whereIn('id', ['2','3','12','14','16'])->pluck('id', 'id')->all();
            }else if($role->name == "RGIn"){
                $permissions = Permission::whereIn('id', ['2','3','13'])->pluck('id', 'id')->all();
            }
            $role->syncPermissions($permissions);
        }
    }
}
