<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DocumentStatus;

class DocumentStatusTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $document_statuses = [
            ["id" => 1, "document_status" => "1", "document_status_name" => "Operation"],
            ["id" => 2, "document_status" => "2", "document_status_name" => "Br. Manager Checked"],
            ["id" => 3, "document_status" => "3", "document_status_name" => "Br. Manager Rejected"],
            ["id" => 4, "document_status" => "4", "document_status_name" => "Cat. Head Approved"],
            ["id" => 5, "document_status" => "5", "document_status_name" => "Cat. Head Rejected"],
            ["id" => 6, "document_status" => "6", "document_status_name" => "Mer. Manager Confirmed"],
            ["id" => 7, "document_status" => "7", "document_status_name" => "Mer. Manager Rejected"],
            ["id" => 8, "document_status" => "8", "document_status_name" => "RG Out Completed"],
            ["id" => 9, "document_status" => "9", "document_status_name" => "CN Completed"],
            ["id" => 10, "document_status" => "10", "document_status_name" => "RG In Completed"],
            ["id" => 11, "document_status" => "11", "document_status_name" => "DB Completed"],
            ["id" => 12, "document_status" => "12", "document_status_name" => "Supplier Cancelled"],
            ["id" => 13, "document_status" => "13", "document_status_name" => "Exchange Deducted"],
        ];

        foreach ($document_statuses as $document_status) {
            DocumentStatus::create($document_status);
        }
    }
}
