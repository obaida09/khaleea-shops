<?php

namespace App\Traits;

use App\Models\PostImage;

trait PostImagesUpload
{
    /**
     * Handle multiple image uploads.
     *
     * @param array $images
     * @param int $postId
     * @param string $directory
     * @return void
     */
    public function PostImagesUpload(array $images, string $postId, string $directory = 'uploads'): void
    {
        foreach ($images as $image) {
            if ($image->isValid()) {
                // Generate unique file name
                $fileName = time() . '_' . $image->getClientOriginalName();
                $path = $image->storeAs($directory, $fileName, 'public');

                // Save the image path in the database
                PostImage::create([
                    'post_id' => $postId,
                    'image_path' => $path,
                ]);
            }
        }
    }
}
