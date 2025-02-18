<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;

class CategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $remarks = [
            ["id" => 1, "category_name" => "01-Cement and Block"],
            ["id" => 2, "category_name" => "02-Steel"],
            ["id" => 3, "category_name" => "03-Roofing/Ceiling/Wall"],
            ["id" => 13, "category_name" => "04-Sanitary Ware"],
            ["id" => 5, "category_name" => "05-Garden and Accessories"],
            ["id" => 7, "category_name" => "06-Hardware and Tools"],
            ["id" => 14, "category_name" => "07-Surface Covering"],
            ["id" => 8, "category_name" => "08-Door/Window/Wood"],
            ["id" => 9, "category_name" => "09-Electrical and Accessories"],
            ["id" => 10, "category_name" => "10-Home Appliance"],
            ["id" => 16, "category_name" => "11-Paint and Chemical"],
            ["id" => 19, "category_name" => "12-Houseware and Kitchen"],
            ["id" => 15, "category_name" => "13-Furniture and Bedding"],
            ["id" => 25, "category_name" => "14-Stationery & Digital Equipment"],
            ["id" => 24, "category_name" => "99-Accounting / Office Use"],
        ];

        foreach ($remarks as $remark) {
            Category::create($remark);
        }
    }
}
