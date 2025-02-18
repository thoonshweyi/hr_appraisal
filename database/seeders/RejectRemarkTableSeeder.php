<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\RejectRemark;

class RejectRemarkTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $reject_remarks = [
            ["id" => 1, "remark_eng" => "Duplicate Document", "remark_mm" => "Document ၂စောင် ရိုက်မိခြင်း"],
            ["id" => 2, "remark_eng" => "Change Document Type", "remark_mm" => "Document အမျိုးအစားပြောင်းသွားခြင်း"],
            ["id" => 3, "remark_eng" => "Wrong Choice of Product or Category or Supplier Name", "remark_mm" => "Product, Category နှင့် Supplier မှားရွေးမိခြင်း"],
            ["id" => 4, "remark_eng" => "Products being Sold out during Return/Exchange Process", "remark_mm" => "Request တင်ထားသောပစ္စည်းများ ရောင်းထွက်သွား၍ မလိုအပ်တော့ခြင်း"],
            ["id" => 5, "remark_eng" => "Direct Exchange on spot by Supplier", "remark_mm" => "Supplier မှ ပစ္စည်း ဒဲ့ချိန်းပေးသွားခြင်း"],
            ["id" => 6, "remark_eng" => "Stock Adjust on Process", "remark_mm" => "Stock Adjust လုပ်ထားပါသဖြင့်"],
            ["id" => 7, "remark_eng" => "Supplier can't provide Exchange or Return", "remark_mm" => "Supplier မှ Exchange or Return မပေးပါသဖြင့်"],

        ];

        foreach ($reject_remarks as $reject_remark) {
            RejectRemark::create($reject_remark);
        }
    }
}
