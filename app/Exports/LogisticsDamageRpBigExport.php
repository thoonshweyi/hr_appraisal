<?php

namespace App\Exports;

use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithDrawings;
use Maatwebsite\Excel\Concerns\WithHeadings;

use PhpOffice\PhpSpreadsheet\Style\Alignment;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;

class LogisticsDamageRpBigExport implements FromCollection, WithHeadings, WithDrawings, ShouldAutoSize, WithEvents, WithColumnFormatting, WithMapping,WithColumnWidths
{
    private $logisticDocuments;
    private $imagePaths = []; // To store image paths and positions
    private $totalRows = 0;

    public function __construct($logisticDocuments)
    {
        $this->logisticDocuments = $logisticDocuments;
        // dd($this->logisticDocuments);
    }


    public function collection()
    {
        $data = collect();
        $this->imagePaths = []; // Reset image storage
        $rowCounter = 2; // Start from row 2 (first row is the heading)
        $totalDamageQty = 0; // Initialize total qty

        foreach ($this->logisticDocuments->sortByDesc('id') as $document) {
            foreach ($document->Products->sortBy('id') as $product) {
                $excelRowIndex = $rowCounter; // Keep track of the row index for images

                // **Group images by 'row' value**
                // dd($product->importproductimage);
                $groupedImages = $product->importproductimage->groupBy('row');
                foreach ($groupedImages as $rowKey => $images) {
                    $imageColumnIndex = ord('F'); // Start inserting images from column F
                    foreach ($images as $image) {
                        $imagePath = public_path("storage/" . $image->media_link);
                        if (!file_exists($imagePath)) {
                            $imagePath = public_path("storage/" . getImage($image));
                        }

                        if (file_exists($imagePath)) {
                            $coordinates = chr($imageColumnIndex) . $excelRowIndex; // Convert back to letter
                            $this->imagePaths[] = [
                                'path' => $imagePath,
                                'coordinates' => $coordinates,
                            ];
                            $imageColumnIndex++; // Move to next column for next image
                        }
                    }

                    // **Push product row**
                    $data->push([
                        'Document No'   => $document->document_no,
                        'Product Name'  => $product->product_name,
                        'Product Code'  => (string) $product->product_code_no,
                        'Branch'        => $document->branches->branch_name,
                        'Damage Qty'    => $image->seperate_qty,
                        '', '', '', '', '', '', '', // Empty placeholders for images (F to L)
                        'Brand' => $product->product_brand_name,
                        'Remark' => $product->remark ?? '', // Ensure "Remark" is at column M
                    ]);

                    // **Update total damage quantity**
                    $totalDamageQty += $image->seperate_qty;

                    $excelRowIndex++; // Move to the next row for the next group of images
                    $rowCounter++; // Ensure rowCounter is also updated
                }
            }
        }

        // **Add "Total" row at the end**
        $data->push([
            'Document No'   => '', // Empty
            'Product Name'  => '',
            'Product Code'  => '',
            'Branch'        => 'Total', // "Total" label in the Branch column
            'Damage Qty'    => $totalDamageQty, // Total sum of Damage Qty
            '', '', '', '', '', '', '', // Empty placeholders for images
            'Brand' => '',
            'Remark' => '', // Remark empty for total row
        ]);

        $this->totalRows = $rowCounter; // Update total row count

        return $data;
    }


    public function headings(): array
    {
        return [
            'Document No',
            'Product Name',
            'Product Code',
            'Branch',
            'Damage Qty',
            'Photos', // Images will be placed dynamically starting from column F
            '', '', '', '', '', '',// Empty placeholders for merged columns (F to L)
            'Brand',
            'Remark'  // Ensure this is the last column (M)
        ];
    }

    public function drawings()
    {
        $drawings = [];

        foreach ($this->imagePaths as $imgData) {
            if ($this->isImageFile($imgData['path'])) { //  Check if it's an image
                $drawing = new Drawing();
                $drawing->setPath($imgData['path']);
                $drawing->setResizeProportional(false); // Disable proportional scaling
                $drawing->setWidth(140); // Adjust width as needed
                $drawing->setHeight(80); // Adjust height as needed
                $drawing->setCoordinates($imgData['coordinates']);
                $drawings[] = $drawing;
            } else {
                \Log::warning("Skipping non-image file: " . $imgData['path']);
            }
        }

        return $drawings;
    }

    private function isImageFile($filePath)
    {
        if (!file_exists($filePath)) {
            return false; // File does not exist
        }

        $imageInfo = @getimagesize($filePath); // Suppress errors for invalid images
        return $imageInfo !== false; // Returns true if the file is an image
    }

    public function map($row): array
    {
        return [
            $row['Document No'],
            $row['Product Name'],
            "\t" . $row['Product Code'],
            $row['Branch'],
            $row['Damage Qty'],
            '', '', '', '', '', '', '', // Empty placeholders for merged columns (F to L)
            $row['Brand'],
            $row['Remark'],
        ];
    }

    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT, // Force 'Product Code' column to be text
        ];
    }



    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // $totalRows = count($this->logisticDocuments) + 1; // Include heading row
                // Set default row height dynamically
                foreach (range(1, $this->totalRows) as $row) {
                    $rowHeightPx = 80;
                    $rowHeight = $rowHeightPx * 0.75; // Convert pixels to Excel row height scale
                    $sheet->getRowDimension($row)->setRowHeight($rowHeight);
                }

                // Set column width (Optional, for better visibility)
                // foreach (range('A', 'G') as $column) { // Adjust column range based on data
                //     $sheet->getColumnDimension($column)->setAutoSize(true);
                // }

                // Set vertical and horizontal alignment
                $sheet->getStyle('A1:N' . $this->totalRows)
                    ->getAlignment()
                    ->setVertical(Alignment::VERTICAL_CENTER)
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);


                  // **Style Heading Row (Row 1)**
                $sheet->getStyle('A1:N1')->applyFromArray([
                    'font' => [
                        'bold' => true,        // Bold text
                        'size' => 18,          // Font size
                        'color' => ['rgb' => 'FFFFFF'], // White font color
                    ],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '4F81BD'], // Blue background color
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER, // Center align text
                        'vertical' => Alignment::VERTICAL_CENTER, // Center vertically
                    ],
                    'borders' => [
                        'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN, // Thin border
                        'color' => ['rgb' => 'BFBFBF'], // Light gray border
                    ],
                ],
                ]);
                // Merge the "Photos" heading across 7 columns (F to L)
                $sheet->mergeCells('F1:L1');

            },
        ];
    }
    public function columnWidths(): array
    {
        $columnWidthPx = 140;
        $columnWidth = $columnWidthPx / 7;
        return [
            'F' => $columnWidth,
            'G' => $columnWidth,
            'H' => $columnWidth,
            'I' => $columnWidth,
            'J' => $columnWidth,
            'K' => $columnWidth,
            'L' => $columnWidth,
            'M' => $columnWidth
        ];
    }
}
