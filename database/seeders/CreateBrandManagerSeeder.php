<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateBrandManagerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $users = [
            [
                'name' => 'Zay Yar Phyoe Paing',
                'email' => 'thomaszaw@gmail.com',
                'employee_id' => '000-000459',
                'password' => bcrypt('123456')
            ]
        ];


        $role = Role::where('name' , 'BranchManager')->first();

        foreach ($users as $value) {
            $user = User::create($value);
            $user->assignRole([$role->id]);
        }
       
    }
}
