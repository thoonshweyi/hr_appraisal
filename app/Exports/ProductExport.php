<?php

namespace App\Exports;

use App\Models\Product;
use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class ProductExport implements FromQuery,WithHeadings,WithColumnFormatting
{
    public function headings(): array
    {
        return [
            'Productcode',
            'Quantity',
            'Price/Unit',
            'Discount(Amount)',
            'RemarkItem',
        ];
    }

    public function __construct($document_id)
    {
        $this->document_id = $document_id;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function query()
    {
        $document_status = Document::find($this->document_id)->document_status;
        if($document_status == 8){
            return Product::query()->where('document_id', $this->document_id)->select('product_code_no','operation_rg_out_actual_quantity','product_unit','discount_amount','operation_remark');
        }elseif($document_status == 10){
            return Product::query()->where('document_id', $this->document_id)->select('product_code_no','operation_rg_in_actual_quantity','product_unit','discount_amount','operation_remark');
        }
    }
}
