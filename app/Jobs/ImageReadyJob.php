<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ImageReadyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    private $sourcingDocuments;
    public function __construct($sourcingDocuments)
    {
        $this->sourcingDocuments = $sourcingDocuments;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        foreach ($this->sourcingDocuments->sortByDesc('id') as $document) {
            foreach ($document->sourcingproduct->sortBy('id') as $product) {
                // **Group images by 'row' value**
                $groupedImages = $product->sourcingproductimage->groupBy('row');

                foreach ($groupedImages as $rowKey => $images) {
                    $imageColumnIndex = ord('F'); // Start inserting images from column F

                    foreach ($images as $image) {
                        $imagePath = public_path("storage/" . $image->media_link);
                        if (!file_exists($imagePath)) {
                            $imagePath = public_path("storage/" . getSourcingImage($image));
                        }
                    }
                }
            }
        }
    }
}
