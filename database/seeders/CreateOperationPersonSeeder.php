<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class CreateOperationPersonSeeder extends Seeder
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
                'name' => 'Shoon Lae Wi',
                'email' => 'shoonlaewi1@gmail.com',
                'employee_id' => '003-000576',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Aye Mya Thu Zar',
                'email' => 'ayemyaThuzar@gmail.com',
                'employee_id' => '003-000098',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Naing Lin Aung',
                'email' => 'nainglinaung@gmail.com',
                'employee_id' => '003-000688',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Wanna Tun',
                'email' => 'wannatun@gmail.com',
                'employee_id' => '003-000648',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Zin Pwint Thu',
                'email' => 'zinpwintthu@gmail.com',
                'employee_id' => '003-000485',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Myat Ko',
                'email' => 'myatko@gmail.com',
                'employee_id' => '003-000162',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Aye Thandar Cho',
                'email' => 'ayethandarcho@gmail.com',
                'employee_id' => '003-000572',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Ei Tha Zin Khing',
                'email' => 'eithazinkhing@gmail.com',
                'employee_id' => '003-000101',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Aung Zaw Htet',
                'email' => 'aungzawhtet@gmail.com',
                'employee_id' => '003-000264',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Pyae Phyo Kyaw',
                'email' => 'pyaephyokyaw@gmail.com',
                'employee_id' => '003-000690',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
            [
                'name' => 'Mee Mee Min',
                'email' => 'meemeemin@gmail.com',
                'employee_id' => '003-000719',
                'branch_id' => '1',
                'password' => bcrypt('123456')
            ],
        ];


        $role = Role::where('name' , 'OperationPerson')->first();

        foreach ($users as $value) {
            $user = User::create($value);
            $user->assignRole([$role->id]);
        }
       
    }
}
