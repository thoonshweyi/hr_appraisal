<?php

namespace App\Exports;

use App\Models\Document;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class DocumentExport implements FromQuery,WithHeadings,WithColumnFormatting
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

    public function __construct($fromDate,$toDate,$document_no,$document_type,$document_branch,$document_status,$category)
    {
        $this->fromDate = $fromDate;
        $this->toDate = $toDate;
        $this->document_no = $document_no;
        $this->document_type = $document_type;
        $this->document_branch = $document_branch;
        $this->document_status = $document_status;
        $this->category = $category;
    }

    public function columnFormats(): array
    {
        return [
            'A' => NumberFormat::FORMAT_NUMBER,
        ];
    }

    public function query()
    {
        $result = Document::query();
        if ($this->document_no != "") {
            $result = $result->where('documents.document_no', 'ilike', '%' . $this->document_no . '%');
        }
    
        if (!empty($this->fromDate)) :
            $dateStr = str_replace("/", "-", $this->fromDate);
            $fromDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('documents.created_at', '>=', $fromDate);
        endif;
        if (!empty($this->toDate)) :
            $dateStr = str_replace("/", "-", $this->toDate);
            $toDate = date('Y/m/d H:i:s', strtotime($dateStr));
            $result = $result->whereDate('documents.created_at', '<=', $toDate);
        endif;
        if ($this->document_type != "") {
            $result = $result->where('documents.document_type', $this->document_type);
        }
        if ($this->document_status != "0") {
            $result = $result->where('documents.document_status', $this->document_status);
        }

        if ($this->document_branch != "") {
            $result = $result->where('documents.branch_id', $this->document_branch);
        }

        if($this->category != "0"){
            $result = $result->where('documents.category_id', $this->category);
        }
        $result = $result->select('document_no','document_type','document_no','document_no','document_no');
    
        return $result;
    }
}
