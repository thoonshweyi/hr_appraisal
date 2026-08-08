<?php

use App\Models\{CNDBDocument, ImportProductImage, SourcingProductImage, SourcingDocument, LogisticsDocument};
use App\Models\ImportProduct;
use App\Models\SourcingProduct;
use App\Notifications\AppraisalFormsNotify;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Session;
use Symfony\Component\Console\Logger\ConsoleLogger;

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
    
    if ($authuser->hasRole('HR Authorized') && ($branch_ids && !$branch_ids->contains('7') || $authuser->email == "allbranchhr@gmail.com") ) {
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

function getUserData(){
    return Auth::user();
}

function sendNotification($user, $appraisalform, $title)
{
    $type = "App\Notifications\AppraisalFormsNotify";
    $getnoti = \DB::table("notifications")->where("notifiable_id",$user->id)->where("type",$type)->where('data->appraisalform_id',$appraisalform->id)->pluck('id');
    
    // dd($getnoti);
    if(count($getnoti) > 0){
        \DB::table("notifications")->where('id',$getnoti)->update(["read_at" => null]);
        return false;
    }
    

    return Notification::send($user, new AppraisalFormsNotify($appraisalform->id ?? null, $appraisalform->ass_form_cat_id ?? null, $title ?? null, $appraisalform->appraisal_cycle_id ?? null));
}