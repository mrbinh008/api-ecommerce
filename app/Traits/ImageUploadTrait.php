<?php

namespace App\Traits;

use Illuminate\Http\Request;
use File;

trait ImageUploadTrait
{

    public function uploadImage(mixed $file, string $path = "upload"): array
    {
        $extension = $file->getClientOriginalExtension();
        $fileName = 'media_' . uniqid();
        $file->storeAs($path, $fileName.'.'. $extension, 'public');
        $uploadedImagePath = 'storage/' . $path . '/' . $fileName.'.'. $extension;
        if (in_array($extension, ['svg', 'webp'])) {
            return [
                'name' => $fileName.'.'. $extension,
                'path' => $uploadedImagePath,
            ];
        }
        $webpImagePath = 'storage/' . $path . '/' . pathinfo($fileName.'.'. $extension, PATHINFO_FILENAME) . '.webp';
        $this->encodeToWebp($uploadedImagePath, $webpImagePath);

        return [
            'name' => $fileName . '.webp',
            'path' => $webpImagePath,
        ];
    }

    public function uploadMultiImage(mixed $file, string $path = "upload"): array
    {
        $imageData = [];
        foreach ($file as $image) {
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_' . uniqid();
            $image->storeAs($path, $imageName. '.' . $ext, 'public');
            $uploadedImagePath = 'storage/' . $path . '/' . $imageName. '.' . $ext;
            if (in_array($ext, ['svg', 'webp'])) {
                $imageData[] = [
                    'name' => $imageName,
                    'path' => $uploadedImagePath,
                ];
            }
            // Convert the uploaded image to webp
            $webpImagePath = 'storage/' . $path . '/' . pathinfo($imageName. '.' . $ext, PATHINFO_FILENAME) . '.webp';
            $this->encodeToWebp($uploadedImagePath, $webpImagePath);

            $imageData[] = [
                'name' => $imageName . '.webp',
                'path' => $webpImagePath,
            ];
        }
        return $imageData;
    }


    // Controller method to handle file upload
//    public function uploadFileBase64(mixed $files, string $path = "upload"): array
//    {
//        $dataImage = [];
//        foreach ($files as $file) {
//            $image_64 = $file['thumbUrl'];
//            $extension = explode('/', explode(':', substr($image_64, 0, strpos($image_64, ';')))[1])[1];
//            $replace = substr($image_64, 0, strpos($image_64, ',') + 1);
//            $image = str_replace($replace, '', $image_64);
//            $image = str_replace(' ', '+', $image);
//            $imageName = 'media_'.uniqid() . '.' . $extension;
//            $filePath =  $path . '/' . $imageName;
//            \Storage::disk('public')->put($filePath, base64_decode($image));
//            $dataImage[] = [
//                'name' => $imageName,
//                'path' => 'storage/' .$filePath,
//            ];
//        }
//        return $dataImage;
//    }

    public function uploadFileBase64(array $files, string $path = "upload"): array
    {
        $dataImages = [];

        foreach ($files as $fileData) {
            $base64Image = $fileData;
            $uniqueFilename = $this->generateUniqueFilename($fileData);
            $filePath = $path . '/' . $uniqueFilename;

            try {
                // Save the image directly without resizing
                $this->saveBase64Image($base64Image, $filePath);

                $dataImages[] = [
                    'name' => $uniqueFilename,
                    'path' => 'storage/' . $filePath,
                ];
            } catch (\Exception $e) {
                // Handle error gracefully
                // For example, log the error and continue with the next image
                \Log::error('Error saving image: ' . $e->getMessage());
            }
        }

        return $dataImages;
    }

    private function saveBase64Image(string $base64Image, string $filePath): void
    {
        $imageData = substr($base64Image, strpos($base64Image, ',') + 1);
        $imageData = str_replace(' ', '+', $imageData);
        \Storage::disk('public')->put($filePath, base64_decode($imageData));
    }

    private function generateUniqueFilename(string $base64Image): string
    {
        $extension = $this->getImageExtension($base64Image);
        return 'media_' . uniqid() . '.' . $extension;
    }

    private function getImageExtension(string $base64Image): string
    {
        $mime = explode(';', $base64Image)[0];
        $mime = explode(':', $mime)[1];
        return explode('/', $mime)[1];
    }


    /**
     * @param mixed $file
     * @param string $path : default is 'upload'
     * @param string|null $oldPath
     * @return array[]
     */
    public function updateImage(mixed $file, string $path = 'upload', string $oldPath = null): array
    {
        if (File::exists($oldPath)) {
            File::delete($oldPath);
        }
        $ext = $file->getClientOriginalExtension();
        $fileName = 'media_' . uniqid() . '.' . $ext;
        $file->storeAs($path, $fileName, 'public');
        $uploadedImagePath = 'storage/' . $path . '/' . $fileName;
        if (in_array($ext, ['svg', 'webp'])) {
            return [
                'name' => $fileName,
                'path' => $uploadedImagePath,
            ];
        }
        $webpImagePath = 'storage/' . $path . '/' . pathinfo($fileName, PATHINFO_FILENAME) . '.webp';
        $this->encodeToWebp($uploadedImagePath, $webpImagePath);
        return [
            'name' => $fileName,
            'path' => $webpImagePath,
        ];
    }

    /** Handle Delte File */
    public function deleteImage(string $path)
    {
        if (File::exists($path)) {
            File::delete($path);
        }
    }

    /**
     * Encode image to webp
     *
     * @param $sourceImage
     * @param $outputImage
     * @param int $quality
     * @throws Exception
     */
    private function encodeToWebp($sourceImage, $outputImage, $quality = 80)
    {
        $extension = pathinfo($sourceImage, PATHINFO_EXTENSION);
        switch (strtolower($extension)) {
            case 'jpeg':
            case 'jpg':
                $image = imagecreatefromjpeg($sourceImage);
                break;
            case 'png':
                $image = imagecreatefrompng($sourceImage);
                break;
            case 'gif':
                $image = imagecreatefromgif($sourceImage);
                break;
            case 'bmp':
                $image = imagecreatefrombmp($sourceImage);
                break;
            default:
                return;
        }
        imagewebp($image, $outputImage, $quality);
        imagedestroy($image);
        if (file_exists($sourceImage)) {
            unlink($sourceImage);
        }
    }
}

