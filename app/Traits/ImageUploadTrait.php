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
            \Storage::putFileAs($path, $file, $fileName);
//            $file->storeAs($path, $fileName);
            return $path.'/'.$fileName;
        }
    }


    public function uploadMultiImage(Request $request, $inputName, $path)
    {
        $imagePaths = [];

        if($request->hasFile($inputName)){

            $images = $request->{$inputName};

            foreach($images as $image){

                $ext = $image->getClientOriginalExtension();
                $imageName = 'media_'.uniqid().'.'.$ext;

                $image->storeAs($path, $imageName);

                $imagePaths[] =  $path.'/'.$imageName;
            }

            return $imagePaths;
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
        if(File::exists(storage_path($path))){
            File::delete(storage_path($path));
        }
    }
}

