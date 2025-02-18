<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permissions = [
            ["permission_id" => 1,"name" => "view-dashboard-return-total","group_name"=>"dashboard"],
            ["permission_id" => 2,"name" => "view-dashboard-return-finish","group_name"=>"dashboard"],
            ["permission_id" =>3,"name" => "view-dashboard-return-pending","group_name"=>"dashboard"],
            ["permission_id" =>4,"name" =>"view-dashboard-exchange-total","group_name"=>"dashboard"],
            ["permission_id" =>5,"name" =>"view-dashboard-exchange-finish","group_name"=>"dashboard"],
            ["permission_id" =>6,"name" =>"view-dashboard-exchange-pending","group_name"=>"dashboard"],
            ["permission_id" =>7,"name" =>"view-dashboard-overdue-exchange-document","group_name"=>"dashboard"],
            ["permission_id" =>8,"name" =>"view-dashboard-total-user","group_name"=>"dashboard"],
            ["permission_id" =>9,"name" =>"view-dashboard-total-supplier","group_name"=>"dashboard"],
            ["permission_id" =>10,"name" =>"view-dashboard-total-branch","group_name"=>"dashboard"],
            ["permission_id" =>11,"name" =>"view-dashboard-total-role","group_name"=>"dashboard"],
            ["permission_id" =>12,"name" =>"create-document","group_name"=>"document"],
            ["permission_id" =>13,"name" =>"view-documents","group_name"=>"document"],
            ["permission_id" =>14,"name" =>"edit-document","group_name"=>"document"],
            ["permission_id" =>15,"name" =>"edit-document-no","group_name"=>"document"],
            ["permission_id" =>16,"name" =>"edit-document-type","group_name"=>"document"],
            ["permission_id" =>17,"name" =>"edit-document-date","group_name"=>"document"],
            ["permission_id" =>18,"name" =>"edit-document-supplier","group_name"=>"document"],
            ["permission_id" =>19,"name" =>"edit-document-remark-type","group_name"=>"document"],
            ["permission_id" =>20,"name" =>"edit-document-category","group_name"=>"document"],
            ["permission_id" =>21,"name" =>"edit-document-delivery-date","group_name"=>"document"],
            ["permission_id" =>22,"name" =>"update-document","group_name"=>"document"],
            ["permission_id" =>23,"name" =>"delete-document","group_name"=>"document"],
            ["permission_id" =>24,"name" =>"view-document-delivery-date","group_name"=>"document"],
            ["permission_id" =>25,"name" =>"view-document-operation-remark","group_name"=>"document-remark"],
            ["permission_id" =>26,"name" =>"edit-document-operation-remark","group_name"=>"document-remark"],
            ["permission_id" =>27,"name" =>"view-document-merchandising-remark","group_name"=>"document-remark"],
            ["permission_id" =>28,"name" =>"edit-document-merchandising-remark","group_name"=>"document-remark"],
            ["permission_id" =>29,"name" =>"view-document-accounting-remark","group_name"=>"document-remark"],
            ["permission_id" =>30,"name" =>"edit-document-accounting-remark","group_name"=>"document-remark"],
            ["permission_id" =>31,"name" =>"view-document-pending-remark","group_name"=>"document-remark"],
            ["permission_id" =>32,"name" =>"edit-document-pending-remark","group_name"=>"document-remark"],
            ["permission_id" =>33,"name" =>"view-document-operation-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>34,"name" =>"edit-document-operation-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>35,"name" =>"view-document-rgout-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>36,"name" =>"edit-document-rgout-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>37,"name" =>"view-document-accounting-cn-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>38,"name" =>"edit-document-accounting-cn-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>39,"name" =>"view-document-rgin-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>40,"name" =>"edit-document-rgin-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>41,"name" =>"view-document-accounting-db-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>42,"name" =>"edit-document-accounting-db-attach-file","group_name"=>"document-attach"],
            ["permission_id" =>43,"name" =>"update-document-bm-complete","group_name"=>"document-update"],
            ["permission_id" =>44,"name" =>"update-document-bm-reject","group_name"=>"document-update"],
            ["permission_id" =>45,"name" =>"update-document-ch-complete","group_name"=>"document-update"],
            ["permission_id" =>46,"name" =>"update-document-ch-reject","group_name"=>"document-update"],
            ["permission_id" =>47,"name" =>"update-document-mm-complete","group_name"=>"document-update"],
            ["permission_id" =>48,"name" =>"update-document-mm-reject","group_name"=>"document-update"],
            ["permission_id" =>49,"name" =>"update-document-rgout-complete","group_name"=>"document-update"],
            ["permission_id" =>50,"name" =>"update-document-rgout-reject","group_name"=>"document-update"],
            ["permission_id" =>51,"name" =>"update-document-cn-complete","group_name"=>"document-update"],
            ["permission_id" =>52,"name" =>"update-document-cn-reject","group_name"=>"document-update"],
            ["permission_id" =>53,"name" =>"update-document-rgin-complete","group_name"=>"document-update"],
            ["permission_id" =>54,"name" =>"update-document-rgin-reject","group_name"=>"document-update"],
            ["permission_id" =>55,"name" =>"update-document-db-complete","group_name"=>"document-update"],
            ["permission_id" =>56,"name" =>"update-document-db-reject","group_name"=>"document-update"],
            ["permission_id" =>57,"name" =>"update-document-supplier-cancel","group_name"=>"document-update"],
            ["permission_id" =>58,"name" =>"update-document-deducted","group_name"=>"document-update"],
            ["permission_id" =>59,"name" =>"export-document-cn","group_name"=>"document-export"],
            ["permission_id" =>60,"name" =>"export-document-db","group_name"=>"document-export"],
            ["permission_id" =>61,"name" =>"export-dcoument-rg-out","group_name"=>"document-export"],
            ["permission_id" =>62,"name" =>"export-document-pending","group_name"=>"document-export"],
            ["permission_id" =>63,"name" =>"export-document-admin","group_name"=>"document-export"],
            ["permission_id" =>64,"name" =>"add-product","group_name"=>"product"],
            ["permission_id" =>65,"name" =>"edit-product","group_name"=>"product"],
            ["permission_id" =>66,"name" =>"edit-product-product-code","group_name"=>"product"],
            ["permission_id" =>67,"name" =>"edit-product-rg-no","group_name"=>"product"],
            ["permission_id" =>68,"name" =>"edit-product-qty","group_name"=>"product"],
            ["permission_id" =>69,"name" =>"edit-product-bm-qty","group_name"=>"product"],
            ["permission_id" =>70,"name" =>"edit-product-mer-qty","group_name"=>"product"],
            ["permission_id" =>71,"name" =>"edit-product-rgout-qty","group_name"=>"product"],
            ["permission_id" =>72,"name" =>"edit-product-rgin-qty","group_name"=>"product"],
            ["permission_id" =>73,"name" =>"edit-product-attachfile","group_name"=>"product"],
            ["permission_id" =>74,"name" =>"edit-product-remark","group_name"=>"product"],
            ["permission_id" =>75,"name" =>"delete_product","group_name"=>"product"],
            ["permission_id" =>76,"name" =>"view-suppliers","group_name"=>"supplier"],
            ["permission_id" =>77,"name" =>"view-branches","group_name"=>"branch"],
            ["permission_id" =>78,"name" =>"create-user","group_name"=>"user"],
            ["permission_id" =>79,"name" =>"edit-user","group_name"=>"user"],
            ["permission_id" =>80,"name" =>"view-users","group_name"=>"user"],
            ["permission_id" =>81,"name" =>"delete-user","group_name"=>"user"],
            ["permission_id" =>82,"name" =>"update-profile","group_name"=>"user"],
            ["permission_id" =>83,"name" =>"create-role","group_name"=>"role"],
            ["permission_id" =>84,"name" =>"edit-role","group_name"=>"role"],
            ["permission_id" =>85,"name" =>"view-roles","group_name"=>"role"],
            ["permission_id" =>86,"name" =>"delete-role","group_name"=>"role"],
            ["permission_id" =>87,"name" =>"my-document-operation","group_name"=>"my-document"],
            ["permission_id" =>88,"name" =>"my-document-bm","group_name"=>"my-document"],
            ["permission_id" =>89,"name" =>"my-document-ch","group_name"=>"my-document"],
            ["permission_id" =>90,"name" =>"my-document-mm","group_name"=>"my-document"],
            ["permission_id" =>91,"name" =>"my-document-rgout","group_name"=>"my-document"],
            ["permission_id" =>92,"name" =>"my-document-account-cn","group_name"=>"my-document"],
            ["permission_id" =>93,"name" =>"my-document-account-db","group_name"=>"my-document"],
            ["permission_id" =>94,"name" =>"my-document-rgin","group_name"=>"my-document"],
            ["permission_id" =>95,"name" =>"change_to_previous_status","group_name"=>"my-document"],
            ["permission_id" =>96,"name" =>"view-faqs","group_name"=>"faqs"],
            ["permission_id" =>97,"name" =>"edit-faqs","group_name"=>"faqs"],
         ];
      
         foreach ($permissions as $permission) {
            Permission::create($permission);
         }
    }
}
