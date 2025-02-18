<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(PermissionTableSeeder::class);
        // $this->call(CreateAdminUserSeeder::class);
        // $this->call(CreateRoleTableSeeder::class);
        // $this->call(CreateOperationPersonSeeder::class);
        // $this->call(CreateBrandManagerSeeder::class);
        // $this->call(CreateCategoryHeadSeeder::class);
        // $this->call(CreateMerchandisingManagerSeeder::class);
        // $this->call(CreateRGOutSeeder::class);
        // $this->call(CreateAccountingSeeder::class);
        // $this->call(CreateRGInSeeder::class);
        // $this->call(RejectRemarkTableSeeder::class);
        // $this->call(DocumentRemarkTableSeeder::class);
        // $this->call(CategoryTableSeeder::class);
        // $this->call(DocumentStatusTableSeeder::class);
        $this->call(SupplierCancelRemarkTableSeeder::class);
    }
}
