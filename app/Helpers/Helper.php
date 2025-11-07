<?php

use Carbon\Carbon;
use App\Models\ImportProduct;
use App\Models\SourcingProduct;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Database\Capsule\Manager as Capsule;
use Symfony\Component\Console\Logger\ConsoleLogger;
use App\Models\{CNDBDocument, ImportProductImage, SourcingProductImage, SourcingDocument, LogisticsDocument};

function number_convert($string)
{
    $mm = ['၀', '၁', '၂', '၃', '၄', '၅', '၆', '၇', '၈', '၉'];
    $lang = config('app.locale');
    $num = range(0, 9);
    switch ($lang) {
        case 'mm':
            return str_replace($num, $mm, $string);
            break;

        case 'en':
            return str_replace($mm, $num, $string);
            break;

        default:
            return $string;
            break;
    }
}



function get_auth_user()
{
    $user = Auth::guard()->user();
    return $user;
}


function clearSession()
{
    Session::remove('document_no');
    Session::remove('document_from_date');
    Session::remove('old_from_date');
    Session::remove('old_to_date');
    Session::remove('document_to_date');
    Session::remove('branch_id');
    Session::remove('document_status');
    Session::remove('toDate');
    Session::remove('fromDate');
    Session::remove('category_id');
    Session::remove('category');
    Session::remove('next_step');
    Session::remove('brand');
}

function getBrand()
{
    $conn = DB::connection('pgsql2');
    $brand = $conn->select("SELECT *
        FROM master_data.master_product_brand
    ");
    return $brand;
}




function adminHRAuthorize(){
    $roles = Auth::user()->roles->pluck('name');
    $adminauthorize = $roles->contains('Admin') || $roles->contains('HR Authorized');

    return $adminauthorize;
}

function branchHR(){
    $authuser = Auth::user();
    $branch_ids = $authuser->branches->pluck('branch_id');
    
    if ($authuser->hasRole('HR Authorized') && ($branch_ids && !$branch_ids->contains('7')) && $authuser->email != "hradmin@gmail.com") {
        return true;
    }
    return false;
}



function clearFilterSection(){
    session()->forget([
        'filter_employee_name',
        'filter_employee_code',
        'filter_branch_id',
        'filter_position_level_id',
        'filter_subdepartment_id',
        'filter_section_id',
        'filter_sub_section_id',
    ]);
}
