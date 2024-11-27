<?php

namespace App\Traits;

use App\Models\ProductImage;

trait ProductImagesUpload
{
    /**
     * Handle multiple image uploads.
     *
     * @param array $images
     * @param int $productId
     * @param string $directory
     * @return void
     */
    public function ProductImagesUpload(array $images, $productId, $directory = 'uploads'): void
    {
        foreach ($images as $image) {
            if ($image->isValid()) {
                // Generate unique file name
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs($directory, $fileName, 'public');

                // Save the image path in the database
                ProductImage::create([
                    'product_id' => $productId,
                    'image_path' => $path,
                ]);
            }
        }
    }
}
