<?php

namespace App\Traits;

use Illuminate\Http\Request;
use File;

trait ImageUploadTrait {

    public function uploadImage(Request $request, $inputName, $path)
    {
        if($request->hasFile($inputName)){
            $file = $request->file($inputName);
            $extension = $file->getClientOriginalExtension();
            $fileName = 'media_'.uniqid().'.'.$extension;
            $file->storeAs($path, $fileName, 'public');
            $uploadedImagePath = 'storage/'.$path.'/'.$fileName;

            // Convert the uploaded image to webp
            $webpImagePath = 'storage/'.$path.'/'.pathinfo($fileName, PATHINFO_FILENAME).'.webp';
            $this->encodeToWebp($uploadedImagePath, $webpImagePath);

            return [
                'name' => $fileName,
                'path' => $webpImagePath,
            ];
        }
    }

    public function uploadMultiImage(Request $request, $inputName, $path)
    {
        $imageData = [];
        if($request->hasFile($inputName)){

            $images = $request->{$inputName};

            foreach($images as $image){

                $ext = $image->getClientOriginalExtension();
                $imageName = 'media_'.uniqid().'.'.$ext;

                $image->storeAs($path, $imageName, 'public');
                $uploadedImagePath = 'storage/'.$path.'/'.$imageName;

                // Convert the uploaded image to webp
                $webpImagePath = 'storage/'.$path.'/'.pathinfo($imageName, PATHINFO_FILENAME).'.webp';
                $this->encodeToWebp($uploadedImagePath, $webpImagePath);

                $imageData[] =  [
                    'name' => $imageName,
                    'path' => $webpImagePath,
                ];
            }
            return $imageData;
        }
    }


    public function updateImage(Request $request, $inputName, $path, $oldPath=null)
    {
        if($request->hasFile($inputName)){
            if(File::exists(storage_path($oldPath))){
                File::delete(storage_path($oldPath));
            }

            $image = $request->file($inputName);
            $ext = $image->getClientOriginalExtension();
            $imageName = 'media_'.uniqid().'.'.$ext;

            $image->storeAs($path, $imageName);

           return $path.'/'.$imageName;
       }
    }

    /** Handle Delte File */
    public function deleteImage(string $path)
    {
        if(File::exists($path)){
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
    private function encodeToWebp($sourceImage, $outputImage, $quality = 80) {
        $extension = pathinfo($sourceImage, PATHINFO_EXTENSION);
        switch(strtolower($extension)) {
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
                throw new Exception('Unsupported image format');
        }
        imagewebp($image, $outputImage, $quality);
        imagedestroy($image);
        if (file_exists($sourceImage)) {
            unlink($sourceImage);
        }
    }
}

