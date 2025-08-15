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

function getImage($item)
{
    //  dd($item);
    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
    {
        $ftp_file = Storage::disk('ftp1')->get($item->media_link);
    }else{
        try {
            $ftp_file = Storage::disk('ftp3')->get($item->media_link);
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            try {
                $ftp_file = Storage::disk('ftp1')->get($item->media_link);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                $ftp_file = null;
            }
        }
    }
    Storage::disk('public')->put($item->media_link, $ftp_file);
    $per_image = ImportProductImage::whereId($item->id)->first();
    // dd($per_image);
    return $per_image->media_link;
}

function getSourcingImage($item)
{
    //  dd($item);
    if(strtotime(Carbon::now()->format('Y-m-d')) < strtotime('2024-04-02'))
    {
        $ftp_file = Storage::disk('ftp1')->get($item->media_link);
    }else{
        try {
            $ftp_file = Storage::disk('ftp3')->get($item->media_link);
        } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
            try {
                $ftp_file = Storage::disk('ftp1')->get($item->media_link);
            } catch (\Illuminate\Contracts\Filesystem\FileNotFoundException $e) {
                $ftp_file = null;
            }
        }
    }

    Storage::disk('public')->put($item->media_link, $ftp_file);
    $per_image = SourcingProductImage::whereId($item->id)->first();
    // dd($per_image);
    return $per_image->media_link;
}

function discount_amt($item, $import_product)
{
    // dd('hi');
    $amount = ($item->mer_percentage / 100) * $item->seperate_qty * $import_product->product_price;
    // dd('jj');
    return $amount;
}

function total_discount_amt($product_col, $import_product)
{
    // dd($import_product);
    $products = $import_product->Products;
    $amount = 0;
    foreach ($products as $product) {
        $amount += $product->damage_percentage;
    }
    return $amount;
}

function total_qtysourcing($sourcingdoc)
{
    // dd($sourcingdoc->id);
    $sourcingproducts = SourcingProduct::where('document_id',$sourcingdoc->id)->get();

    $totalqty = 0;
    foreach($sourcingproducts as $sourcingproduct){
        $totalqty += $sourcingproduct->stock_quantity;
    }
    // dd($totalqty);
    return $totalqty;
}

function total_qtylogistic($logisticdoc)
{
    // dd($sourcingdoc->id);
    $importproducts = ImportProduct::where('document_id',$logisticdoc->id)->get();

    $totalqty = 0;
    foreach($importproducts as $importproduct){
        $totalqty += $importproduct->stock_quantity;
    }
    // dd($totalqty);
    return $totalqty;
}


function total_after_discount_amt($product_col, $import_product)
{
    $products = $import_product->Products;
    $amount = 0;
    foreach ($products as $product) {
        $amount += $product->percentage_amount;
    }
    return $amount;
}

function total_damage_qty($sourcing_product)
{
    $qty = SourcingProductImage::where('sourcing_product_id', $sourcing_product->id)->groupBy('row', 'seperate_qty')->pluck('seperate_qty');
    // dd($qty);
    $total_qty = 0;
    foreach ($qty as $item) {
        $total_qty += $item;
    }
    // dd($total_qty);
    return $total_qty;
}

function after_discount_amt($item, $import_product)
{
    return ((($item->seperate_qty * $import_product->product_price) - discount_amt($item, $import_product)));
}

function check_reject_status($sourcing_doc)
{
    // dd($sourcing_doc->document_status);
    if ($sourcing_doc->document_status == 3 || $sourcing_doc->document_status == 5 || $sourcing_doc->document_status == 20 || $sourcing_doc->document_status == 22 || $sourcing_doc->document_status == 23) {
        return 1;
    } else {
        return 2;
    }
}
function sourcing_docs($url)
{
    // dd($url);
    if ($url == url('/sourcing_documents?type=1')) {
        $sourcing_documents = SourcingDocument::where('document_type', 1)->latest()->paginate(10);
        return $sourcing_documents;
    } elseif ($url == url('/sourcing_documents?type=2')) {
        $sourcing_documents = SourcingDocument::where('document_type', 2)->latest()->paginate(10);
        return $sourcing_documents;
    } else {
        $sourcing_documents = SourcingDocument::where('document_type', 3)->latest()->paginate(10);
        return $sourcing_documents;
    }
}

function logistic_docs($url)
{
    // dd($url==url('/logistics_documents?type=1'));
    if ($url == url('/logistics_documents?type=1')) {
        $logistic_documents = LogisticsDocument::where('document_type', 1)->latest()->paginate(10);
        // dd($logistic_documents);
        return $logistic_documents;
    } elseif ($url == url('/logistics_documents?type=2')) {
        $logistic_documents = LogisticsDocument::where('document_type', 2)->latest()->paginate(10);
        return $logistic_documents;
    }
    // else{
    //     $logistic_documents = LogisticsDocument::where('document_type',3)->latest()->paginate(10);
    // return $logistic_documents;
    // }

}

function check_sourcing_percentage($sourcing)
{
    $sourcing_product_id = $sourcing->Products->pluck('id');
    $percentages = SourcingProductImage::whereIn('sourcing_product_id', $sourcing_product_id)->pluck('sourcing_percentage')->toArray();
    if (in_array(0, $percentages)) {
        return false;
    } else {

        return true;
    }
}
function check_mer_percentage($sourcing)
{
    $sourcing_product_id = $sourcing->Products->pluck('id');
    $percentages = SourcingProductImage::whereIn('sourcing_product_id', $sourcing_product_id)->pluck('mer_percentage')->toArray();
    if (in_array(0, $percentages)) {
        return false;
    } else {

        return true;
    }
}

function get_auth_user()
{
    $user = Auth::guard()->user();
    return $user;
}


function CN_to_DB(array $data): array
{

    $supplier_id    = $data['supplier'];
    $doc_no         = $data['doc_no'];
    $conn           = DB::connection('pgsql2');
    $req_date       = $data['doc_date']->format('Y-m-d');
    $req_time       = $data['doc_date']->format('Y-m-d H:i:s');
    $due_date       = $data['due_date'];
    $branch_code    = $data['branch_code'];
    $branch_id      = $data['branch_id'];
    $today          = Carbon::now()->format('Y-m-d');
    $now          = Carbon::now()->format('Y-m-d H:i:s');
    $acc_remark_id  = $data['acc_remark_id'];
    $pono           = $data['ref_doc_no'];
    // dd($acc_remark_id);
    $hd_remark      = $data['hd_remark'];
    $credit_day     = (int)$data['credit_day'];
    $remark_id = $data['remark_id'];
    $total_price = 0;
    $price = [];

    $vat_product = [];
    $no_vat_product = [];
    for ($i = 0; $i < count($data['product_code']); $i++) {
        if($data['ll_vat_no_vat'][$i] == 5 ){
            $vat_product['product_code'][]        = $data['product_code'][$i];
            $vat_product['product_name'][]        = $data['product_name'][$i];
            $vat_product['RG_out_qty'][]          = $data['RG_out_qty'][$i];
            $vat_product['avg_cost'][]            = $data['avg_cost'][$i];
            $vat_product['avg_cost_total'][]      = $data['avg_cost_total'][$i];
            $vat_product['remark'][]              = $data['remark'][$i];
        }elseif($data['ll_vat_no_vat'][$i] === 0){
            $no_vat_product['product_code'][]     = $data['product_code'][$i];
            $no_vat_product['product_name'][]     = $data['product_name'][$i];
            $no_vat_product['RG_out_qty'][]       = $data['RG_out_qty'][$i];
            $no_vat_product['avg_cost'][]         = $data['avg_cost'][$i];
            $no_vat_product['avg_cost_total'][]   = $data['avg_cost_total'][$i];
            $no_vat_product['remark'][]           = $data['remark'][$i];
        }
    }

    $emp_id = get_auth_user()->employee_id;
    $user_id = $conn->select("
    select employeeid from hremployee.employee where employeecode = '$emp_id'
    ");
    $user_id = $user_id[0]->employeeid;

    $vendor_name = $conn->select("
        select vendor_name as vendorname from configure.setap_vendor where vendor_id = $supplier_id
    ");

    $vendor_name = $vendor_name[0]->vendorname;


    $loop = [];
    if(count($vat_product) > 0){
        $loop[] = 'vat';
    }
    if(count($no_vat_product) > 0){
        $loop[] = 'no_vat';
    }
    $cn = [];

    for($l = 0 ; $l < count($loop) ; $l ++){

        if($loop[$l] == 'vat'){
            $total_price    =array_sum($vat_product['avg_cost_total']) ;
            $tax = 1.05;
            $acc_no = ['2131001','1194001','5130009'] ;
            $vatrate = 5;
        }elseif($loop[$l] == 'no_vat'){

            $total_price    =  array_sum($no_vat_product['avg_cost_total']) ;
            $tax = 1;
            $acc_no = ['2131001','5130009'] ;
            $vatrate = 0;
        }

        $doc_id = $conn->select("
        select max(poinvid) from purchaseorder.pur_creditnotehd
        ");
        $poinvid = $doc_id[0]->max + 1;

        $cn_doc = $conn->select("
        select
        case
        when
        (
            select count(docuno) from purchaseorder.pur_creditnotehd where docudate::date = '$today' and brchid = $branch_id
        ) = 0
        then
        (
            select doc_no||'-0001' as prdocno from
            (
                select replace((select 'CN'||(select branch_short_name from master_data.master_branch where branch_id=$branch_id)||
                                (select right((select ('$today')::text),8)::text)), '-', '') as doc_no
            ) aa
        )
        else
        (
            select (left((select max(docuno) as max_date from purchaseorder.pur_creditnotehd where docudate::date = '$today' and brchid = $branch_id),-4)||
            TO_CHAR(((right((select max(docuno) as max_date from purchaseorder.pur_creditnotehd where docudate::date = '$today' and brchid = $branch_id),4)::integer +1)), 'fm0000')) as doc_no
        )
        end as docuno
    ");

    $cn_doc = $cn_doc[0]->docuno;
    $checkCN = $conn->select("SELECT EXISTS (
    SELECT 1
    FROM purchaseorder.pur_creditnotehd
    WHERE invno = '$doc_no'
	and docustatus<>'C'
) AS invno_exists;
");

// dd($checkCN[0]->invno_exists,$doc_no);

    if($checkCN[0]->invno_exists==true)
    {
        return [];
    }
    else{

    $conn->select("
                INSERT INTO purchaseorder.pur_creditnotehd
                (
                    poinvid,
                    empid,
                    vatgroupid,
                    brchid,
                    vendorid,
                    apid,
                    vendorname,
                    docudate,
                    docuno,
                    invno,
                    invdate,
                    exchrate,
                    docutype,
                    vatrate,
                    vattype,
                    endcrdtdate,
                    crdtdays,
                    multicurr,
                    docustatus,
                    remark_id,
                    remark ,

                    sumgoodamnt,
                    netamnt,

                    billaftrdiscamnt,
                    totabaseamnt,
                    currentinvamnt,
                    vatamnt,
                    effect_stock,
                    pono,

                    lastinvamnt,
                    sumincludeamnt,
                    sumexcludeamnt,
                    billdiscamnt,
                    basediscamnt,
                    advnamnt,
                    aftradvnamnt,
                    totaexcludeamnt,
                    remaamnt,
                    lastrevexchrate,
                    jobid,
                    resvamnt1,
                    excurrencycode
                )
                VALUES
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ", [
        $poinvid,
        $user_id,
        0,
        $branch_id,
        $supplier_id,
        $supplier_id,
        $vendor_name,
        $now,
        $cn_doc,
        $doc_no,
        $req_time,
        1,
        310,
        $vatrate,
        '2',
        $due_date,
        $credit_day,
        'N',
        'Y',
        $acc_remark_id,
        $hd_remark,

        $total_price,                                   //sumgoodamnt
        $total_price,                                   //netamnt

         $total_price / $tax,                           //billaftrdiscamnt
        $total_price / $tax,                            //totabaseamnt
        ($total_price / $tax) * -1,                     //currentinvamnt
        ($total_price) - ($total_price / $tax),         //vatamnt
        'true',
        $pono,


        0.0000,
        $total_price,
        0.0000,
        0.0000,
        0.0000,
        0.0000,
        $total_price / 1.05,
        $total_price / 1.05,
        $total_price,
        1,
        0,
        $total_price / 1.05,
        'MMK'
    ]);


    $vat_no_vat_t_c = $loop[$l] == 'vat' ? count($vat_product['product_code']) : count($no_vat_product['product_code']);

    for ($i = 0; $i < $vat_no_vat_t_c; $i++) {

        if($loop[$l] == 'vat'){
            $product_price= $vat_product['avg_cost'][$i];
            $product_code = $vat_product['product_code'][$i];
            $product_name = $vat_product['product_name'][$i];
            $rg_out_qty   = $vat_product['RG_out_qty'][$i];
            $remark       = $vat_product['remark'][$i] ?? "";
        }elseif($loop[$l] == 'no_vat'){
            $product_price= $no_vat_product['avg_cost'][$i];
            $product_code = $no_vat_product['product_code'][$i];
            $product_name = $no_vat_product['product_name'][$i];
            $rg_out_qty   = $no_vat_product['RG_out_qty'][$i];
            $remark       = $no_vat_product['remark'][$i] ?? "";
        }

        $product_id   = $conn->select("
        select product_id from master_data.master_product where product_code = '$product_code'
        ");
        $product_id = $product_id[0]->product_id;

        $product_unit_id   = $conn->select("
        select product_unit_id from master_data.master_product_multiprice where product_id = $product_id and branch_id = $branch_id and barcode_code = '$product_code'
        ");

        $product_unit_id = $product_unit_id[0]->product_unit_id;

        // $product_price = in_array($remark_id, $arr) ? 0 : $price[$i];
        $conn->insert("
                insert into purchaseorder.pur_creditnotedt
                (
                    poinvid,
                    listno,
                    goodid,
                    goodname,
                    goodunitid2,
                    goodqty2,
                    goodprice2,
                    gooddiscamnt,
                    goodamnt,
                    stockflag,
                    vattype,
                    vatrate,
                    poststock,
                    totaexcludeamnt,
                    invno,
                    invdate,
                    goodflag,
                    remark,

                    goodstockunitid,
                    goodstockqty,
                    goodaddcost,
                    goodstockrate1,
                    reclistno,
                    freeflag,
                    refeid
                )
                values
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
            ", [
            $poinvid,
            $i + 1,
            $product_id,
            $product_name,
            $product_unit_id,
            $rg_out_qty,
            $product_price,
            0,
            ($rg_out_qty * $product_price),
            -1,
            1,
            $vatrate,
            'N',
            ($rg_out_qty * $product_price),
            $doc_no,
            $req_time,
            'G',
            $remark,

            0,
            0.0000,
            0.0000,
            1,
            0,
            'N',
            0,
        ]);
    }

}
    // dd('yes');
    $conn->select("
                    INSERT INTO accpayable.acp_approveinvoice
                (
                    brchid,
                    vendorid,
                    docuno,
                    docudate,
                    docutype,
                    invoiceno,
                    invoicedate,
                    invtotaamnt,
                    lastinvamnt,
                    payamnt,
                    totapayinvamnt,
                    invexchrate,
                    goodtype,
                    vattype,
                    preflag,
                    poinvid,
                    g1active
                )
                VALUES
                (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ",[
            $branch_id,
            $supplier_id,
            $cn_doc,
            $req_time,
            310,
            $doc_no,
            $today,
            $total_price * -1,
            $total_price * -1,
            0,
            0,
            1,
            1,
            2,
            'N',
            $poinvid,
            'false'
        ]);


        $date = $conn->select("
        (select to_char(now()::date,'yyyyMM'))
        ");
        $date = $date[0]->to_char;
        $escaped_vendor_name = str_replace("'", "''", $vendor_name);
        for($k = 0; $k < count($acc_no) ; $k++){
            if ($acc_no[$k] == '2131001') {
                $doc_type = $conn->select("
                (select 'Trading AP.-General:domestic, '||'$escaped_vendor_name' as gldescription)
                ");
                $doc_type = $doc_type[0]->gldescription;

                $val = [
                    $date,
                    $k + 1,
                    'RB',
                    $acc_no[$k],
                    $branch_code,
                    $branch_code,
                    '',
                    $total_price,
                    0.00,
                    $cn_doc,
                    $today,
                    $doc_type,
                    310
                ];
            } elseif ($acc_no[$k] == '1194001') {
                $doc_type = $conn->select("
                (select 'Commercial Tax Input, '||'$escaped_vendor_name' as gldescription)
            ");
                $doc_type = $doc_type[0]->gldescription;

                $val = [
                    $date,
                    $k + 1,
                    'RB',
                    $acc_no[$k],
                    $branch_code,
                    $branch_code,
                    '',
                    0.00,
                    number_format($total_price - ($total_price / 1.05),2,'.',''),
                    $cn_doc,
                    $today,
                    $doc_type,
                    310
                ];
            } elseif ($acc_no[$k] == '5130009') {
                $doc_type = $conn->select("
                (select 'Goods return to supplier : domestic, '||'$escaped_vendor_name' as gldescription)
                ");
                $doc_type = $doc_type[0]->gldescription;
                $all_price = count($acc_no) == 3 ? number_format(($total_price / 1.05),2,'.','') : $total_price;
                $val = [
                    $date,
                    $k + 1,
                    'RB',
                    $acc_no[$k],
                    $branch_code,
                    $branch_code,
                    '',
                    0.00,
                    $all_price,
                    $cn_doc,
                    $today,
                    $doc_type,
                    310
                ];
            }
            $conn->select("
                insert into generalledger.glc_postt
                (
                    periodid,
                     listno,
                     journalcode,
                    accountcode,
                    brchcode,
                     jobcode,
                     department,
                    debitamnt,
                    creditamnt,
                    gldocuno,
                     gldocudate,
                    gldescription,
                    gldocutype
                )
                values
                (?,?,?,?,?,?,?,?,?,?,?,?,?);
            ",$val);

        }
        $cn[$loop[$l]] = $cn_doc;
    }

    return $cn;
    $conn = null;
}

function DB_to_DB(array $data): array
{
    // dd($data);
    $supplier_id    = $data['supplier'];
    $doc_no         = $data['doc_no'];
    $conn           = DB::connection('pgsql2');
    $req_date       = $data['doc_date']->format('Y-m-d');
    $req_time       = $data['doc_date']->format('Y-m-d H:i:s');
    $due_date       = $data['due_date'];
    $branch_code    = $data['branch_code'];
    $branch_id      = $data['branch_id'];
    $today          = Carbon::now()->format('Y-m-d');
    $now          = Carbon::now()->format('Y-m-d H:i:s');

    $credit_day     = (int)$data['credit_day'];
    $total_price    = array_sum($data['avg_cost_total']);
    // dd($total_price);
    $vat_product= [];
    $no_vat_product= [];
    // dd($no_vat_product);
    for ($i = 0; $i < count($data['product_code']); $i++) {
        if($data['ll_vat_no_vat'][$i] == 5 ){
            $vat_product['product_code'][]        = $data['product_code'][$i];
            $vat_product['product_name'][]        = $data['product_name'][$i];
            $vat_product['RG_in_qty'][]          = $data['RG_in_qty'][$i];
            $vat_product['avg_cost'][]            = $data['avg_cost'][$i];
            $vat_product['avg_cost_total'][]      = $data['avg_cost_total'][$i];

        }elseif($data['ll_vat_no_vat'][$i] === 0){
            $no_vat_product['product_code'][]     = $data['product_code'][$i];
            $no_vat_product['product_name'][]     = $data['product_name'][$i];
            $no_vat_product['RG_in_qty'][]       = $data['RG_in_qty'][$i];
            $no_vat_product['avg_cost'][]         = $data['avg_cost'][$i];
            $no_vat_product['avg_cost_total'][]   = $data['avg_cost_total'][$i];

        }
    }
    // dd($no_vat_product);
    $emp_id = get_auth_user()->employee_id;
    $user_id = $conn->select("
    select employeeid from hremployee.employee where employeecode = '$emp_id'
    ");
    $user_id = $user_id[0]->employeeid;

    $vendor_name = $conn->select("
        select vendor_name as vendorname from configure.setap_vendor where vendor_id = $supplier_id
    ");
    // dd($vendor_name);
    $vendor_name = $vendor_name[0]->vendorname;
    // dd($vendor_name);
    $loop = [];
    if(count($vat_product) > 0){
        $loop[] = 'vat';
    }
    if(count($no_vat_product) > 0){
        $loop[] = 'no_vat';
    }
    $db = [];
    // dd($loop);
    for($l = 0;$l < count($loop) ; $l++){

        if($loop[$l] == 'vat'){
            $total_price    = array_sum($vat_product['avg_cost_total']);
            $tax = 1.05;
            $acc_no = ['5130003','1194001','2131001'] ;
            $cn_doc_no      = $data['cn_db']['vat'];
            $vatrate        = 5;
            $currentinvamnt = $total_price / $tax  ;

        }elseif($loop[$l] == 'no_vat'){
            $total_price    = array_sum($no_vat_product['avg_cost_total']);
            $tax = 1;
            $acc_no = ['5130003','2131001'] ;
            $cn_doc_no      = $data['cn_db']['no_vat'];
            $vatrate        = 0;
            $currentinvamnt = 0;
        }

        $hd_remark      = $cn_doc_no;
        $doc_id = $conn->select("
            select max(poinvid) from purchaseorder.pur_debitnotehd
        ");
        $poinvid = $doc_id[0]->max + 1;
        // logger($poinvid);
    $db_doc = $conn->select("
            select
            case -- change approvedate & branch_code
            when
            (
                select count(docuno) from purchaseorder.pur_debitnotehd where docudate::date = '$today' and brchid = $branch_id
            ) = 0
            then
            (
                select doc_no||'-0001' as prdocno from
                (
                    select replace((select 'DB'||(select branch_short_name from master_data.master_branch where branch_id=$branch_id)||
                                    (select right((select ('$today')::text),8)::text)), '-', '') as doc_no
                ) aa
            )
            else
            (
                select (left((select max(docuno) as max_date from purchaseorder.pur_debitnotehd where docudate::date = '$today' and brchid = $branch_id),-4)||
                TO_CHAR(((right((select max(docuno) as max_date from purchaseorder.pur_debitnotehd where docudate::date = '$today' and brchid = $branch_id),4)::integer +1)), 'fm0000')) as doc_no

                )
            end as docuno
    ");
    $db_doc = $db_doc[0]->docuno;
//    dd($db_doc);
   $check = $conn->select("SELECT EXISTS (
    SELECT 1
    FROM purchaseorder.pur_debitnotehd
    WHERE invno = '$doc_no'
) AS invno_exists;
");
// Log::info($check,'check inv in erp');
// dd($check[0]->invno_exists,$doc_no);
// print_r($check);
if($check[0]->invno_exists==true)
{
    return [];
}
else{
    $conn->select("
    INSERT INTO purchaseorder.pur_debitnotehd
    (
        poinvid,
         empid,
         vatgroupid,
         brchid,
        vendorid,
        vendorname,
         docudate,
        docuno,
        invno,
        invdate,
        exchrate,
        docutype,
        vatrate,
        vattype,
        endcrdtdate,
        crdtdays,
        multicurr,
        docustatus,
        remark ,

        sumgoodamnt,
        netamnt,
        billaftrdiscamnt,
        totabaseamnt,
        currentinvamnt,
        vatamnt,
        update_stock_card,
        pono,

        shipno,
        lastinvamnt,
        lastinvno,
        sumincludeamnt,
        sumexcludeamnt,
        billdiscamnt,
        basediscamnt,
        advnamnt,
        aftradvnamnt,
        totaexcludeamnt,

        stockeffc,
        postgl,
        remaamnt,
        lastrevexchrate,
        apid,
        jobid,
        resvamnt1
    )
    VALUES
    (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
", [
$poinvid,
$user_id,
0,
$branch_id,
$supplier_id,
$vendor_name,
$now,
$db_doc,
$doc_no,
$req_time,
1,
311,
$vatrate,
'2',
$due_date,
$credit_day,
'N',
'N',
$hd_remark,

$total_price,               //sumgoodamnt
$total_price,               //netamnt
$total_price / $tax,        //billaftrdiscamnt
$total_price / $tax,        //totabaseamnt
$currentinvamnt,
($total_price) - ($total_price / $tax),
'true',
$cn_doc_no,

'',
0.0000,
'',
$total_price,          //sumincludeamnt
0.0000,
0.0000,
0.0000,
0.0000,
$total_price / 1.05,   //aftradvnamnt
$total_price / 1.05,  //totaexcludeamnt

'',
'',
$total_price,       //remaamnt
1,
$supplier_id,
0,
$total_price / 1.07 //resvamnt1
]);

$vat_no_vat_t_c = $loop[$l] == 'vat' ? count($vat_product['product_code']) : count($no_vat_product['product_code']);
for ($i = 0; $i < $vat_no_vat_t_c; $i++) {

if($loop[$l] == 'vat'){
$product_price= $vat_product['avg_cost'][$i];
$product_code = $vat_product['product_code'][$i];
$product_name = $vat_product['product_name'][$i];
$rg_in_qty   = $vat_product['RG_in_qty'][$i];

}elseif($loop[$l] == 'no_vat'){
$product_price= $no_vat_product['avg_cost'][$i];
$product_code = $no_vat_product['product_code'][$i];
$product_name = $no_vat_product['product_name'][$i];
$rg_in_qty   = $no_vat_product['RG_in_qty'][$i];
}

$product_id   = $conn->select("
select product_id from master_data.master_product where product_code = '$product_code'
");
$product_id = $product_id[0]->product_id;

$product_unit_id   = $conn->select("
select product_unit_id from master_data.master_product_multiprice where product_id = $product_id and branch_id = $branch_id
");

$product_unit_id = $product_unit_id[0]->product_unit_id;

$conn->insert("
    insert into purchaseorder.pur_debitnotedt
    (
        poinvid,
        listno,
        goodid,
        goodname,
        goodunitid2,
        goodqty2,
        goodprice2,
        gooddiscamnt,
        goodamnt,
        stockflag,
        vattype,
        vatrate,
        totaexcludeamnt,
        goodflag,
        remark,

        goodremaqty2,
        goodstockunitid,
        goodstockqty,
        goodaddcost,
        refeno,
        goodstockrate1,
        reclistno,
        freeflag,
        refeid,
        remagoodstockqty
    )
    values
    (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
", [
$poinvid,
$i + 1,
$product_id,
$product_name,
$product_unit_id,
$rg_in_qty,
$product_price,
0,
($rg_in_qty * $product_price),
-1,
1,
$vatrate,
($rg_in_qty * $product_price),
'G',
'',
0.0000,
0,
0.0000,
0.0000,
'',
1,
0,
'N',
0,
0.0000
]);
}

$conn->select("
    INSERT INTO accpayable.acp_approveinvoice(

        brchid,
        vendorid,
        docuno,
        docudate,
        docutype,
        invoiceno,
        invoicedate,
        invtotaamnt,
        lastinvamnt,
        payamnt,
        totapayinvamnt,
        invexchrate,
        goodtype,
        vattype,
        preflag,
        poinvid,
        g1active,
        shipno,
        endcrdtdate

    )
        VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)
        ", [
$branch_id,
$supplier_id,
$db_doc,
$req_time,
'311',
$doc_no,
$today,
$total_price,
$total_price,
0,
0,
1,
1,
2,
'N',
$poinvid,
'false',
'',
$due_date
]);

$acc_no = $vatrate == 5 ? ['5130003','1194001','2131001'] : ['5130003','2131001'];
$date = $conn->select("
(select to_char(now()::date,'yyyyMM'))
");
$escaped_vendor_name = str_replace("'", "''", $vendor_name);
$date = $date[0]->to_char;
$all = [];
for($k = 0; $k < count($acc_no) ; $k++){
$doc_type = $conn->select("
(select 'DebitNote By '||'$escaped_vendor_name' as gldescription)
");
$doc_type = $doc_type[0]->gldescription;
if ($acc_no[$k] == '2131001') {

    $val = [
        $date,
        $k + 1,
        'BV',
        $acc_no[$k],
        $branch_code,
        $branch_code,
        '',
        0.00,
        $total_price,
        $db_doc,
        $today,
        $doc_type,
        501
    ];


} elseif ($acc_no[$k] == '1194001') {


    $val = [
        $date,
        $k + 1,
        'BV',
        $acc_no[$k],
        $branch_code,
        $branch_code,
        '',
        number_format($total_price - ($total_price / 1.05),2,'.',''),
        0.00,
        $db_doc,
        $today,
        $doc_type,
        501
    ];
} elseif ($acc_no[$k] == '5130003') {

        $all_price = count($acc_no) == 3 ? number_format(($total_price / 1.05),2,'.','') : $total_price;

    $val = [
        $date,
        $k + 1,
        'BV',
        $acc_no[$k],
        $branch_code,
        $branch_code,
        '',
        $all_price,
        0.00,
        $db_doc,
        $today,
        $doc_type,
        501
    ];


}
$conn->select("
    insert into generalledger.glc_postt
    (
        periodid,
         listno,
         journalcode,
        accountcode,
        brchcode,
         jobcode,
         department,
        debitamnt,
        creditamnt,
        gldocuno,
         gldocudate,
        gldescription,
        gldocutype
    )
    values
    (?,?,?,?,?,?,?,?,?,?,?,?,?);
",$val);

}
$db[$loop[$l]] = $db_doc;
}
return $db;

}

    $conn = null;
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

function get_CN_doc($id)
{
    return CNDBDocument::where(['document_id'=>$id,'type'=>'cn'])->orderBy('id')->pluck('cn_db_no');
}

function get_DB_doc($id)
{
    return CNDBDocument::where(['document_id'=>$id,'type'=>'db'])->orderBy('id')->pluck('cn_db_no');
}




function adminHRAuthorize(){
    $roles = Auth::user()->roles->pluck('name');
    $adminauthorize = $roles->contains('Admin') || $roles->contains('HR Authorized');

    return $adminauthorize;
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
