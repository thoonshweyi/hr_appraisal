<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateCategoryHeadSeeder extends Seeder
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
                'name' => 'Thomas Zaw',
                'email' => 'thomaszaw1@gmail.com',
                'employee_id' => '000-000460',
                'password' => bcrypt('123456')
            ],
        ];


        $role = Role::where('name' , 'CategoryHead')->first();

        foreach ($users as $value) {
            $user = User::create($value);
            $user->assignRole([$role->id]);
        }
       
    }
}
