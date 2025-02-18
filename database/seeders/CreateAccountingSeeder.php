<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateAccountingSeeder extends Seeder
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
                'name' => 'Accounting',
                'email' => 'accounting@gmail.com',
                'employee_id' => '000-000999',
                'password' => bcrypt('123456')
            ],
           
        ];


        $role = Role::where('name' , 'Accounting')->first();

        foreach ($users as $value) {
            $user = User::create($value);
            $user->assignRole([$role->id]);
        }
       
    }
}
