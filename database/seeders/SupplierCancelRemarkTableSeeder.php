<?php

namespace Database\Seeders;

use App\Models\SupplierCancelRemark;
use Illuminate\Database\Seeder;

class SupplierCancelRemarkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $supplier_cancel_remarks = [
            ["id" => 1, "supplier_cancel_remark_eng" => "Supplier OOS", "supplier_cancel_remark_mm" => "Supplier OOS"],
            ["id" => 2, "supplier_cancel_remark_eng" => "Overdue", "supplier_cancel_remark_mm" => "Overdue"],
            ["id" => 3, "supplier_cancel_remark_eng" => "Vendor Name wrong", "supplier_cancel_remark_mm" => "Vendor Name wrong"],

        ];

        foreach ($supplier_cancel_remarks as $supplier_cancel_remark) {
            SupplierCancelRemark::create($supplier_cancel_remark);
        }
    }
}
