<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentRemark;

class DocumentRemarkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $remarks = [
            ["id" => 1, "document_remark" => "Non Moved Return"],
            ["id" => 2, "document_remark" => "Consignment Return"],
            ["id" => 3, "document_remark" => "Product Damage Return"],
            ["id" => 4, "document_remark" => "Product Expire Return"],
            ["id" => 5, "document_remark" => "Product Return for Over Stock"],
            ["id" => 6, "document_remark" => "Promotion End Return"],
            ["id" => 7, "document_remark" => "Supplier Wants the items back"],
            ["id" => 8, "document_remark" => "Product Qty & Unit Wrong Return"],
            ["id" => 9, "document_remark" => "Product Wrong Return"],
            ["id" => 10, "document_remark" => "Supplier Vendor Wrong Return"],
            ["id" => 11, "document_remark" => "Out off stock Return"],
            ["id" => 12, "document_remark" => "Exchange Return"],
        ];

        foreach ($remarks as $remark) {
            DocumentRemark::create($remark);
        }
    }
}
