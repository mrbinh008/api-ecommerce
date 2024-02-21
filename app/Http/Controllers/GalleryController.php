<?php

namespace App\Http\Controllers;

use App\Models\Gallery;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use ImageUploadTrait;

    public function store(Request $request)
    {
        $this->validate($request, [
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $image = $this->uploadMultiImage($request, 'image', 'product-gallery');
//        $res=Gallery::query()->insert($image);
        $res = [];
        foreach ($image as $img) {
            $gallery=Gallery::query()->create($img);
            $res[] = $gallery->id;
        }
        return responseCustom($res,200, 'Image uploaded successfully');
    }

    public function destroy($id)
    {
        $gallery = Gallery::query()->findOrFail($id);
        $gallery->delete();
        return response()->json(['message' => 'Image deleted successfully']);
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'image.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);
        $productId= $request->product_id;
        $image = $this->uploadMultiImage($request, 'image', 'product-gallery');
        $imagePaths = [];
        foreach ($image as $img) {
            $imagePaths[] = [
                'product_id' => $productId,
                'image' => $img,
            ];
        }
        Gallery::query()->where('id', $id)->update($imagePaths);
        return response()->json(['message' => 'Image updated successfully']);
    }
}
