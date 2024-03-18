<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    use ImageUploadTrait;

    public function store(Request $request): JsonResponse
    {
        try {
            $gallery = new Gallery();
            $gallery->fill($request->all());
            if ($request->hasFile('image')) {
                $image = $request->file('image');
                $imageData = $this->uploadImage($image, 'gallery');
                $gallery->product_id = 1;
                $gallery->path = $imageData['path'];
                $gallery->name = $imageData['name'];
            }
            $gallery->save();
            return response()->json($gallery, 201);
        } catch (\Exception $e) {
            \Log::error("GalleryController error:" . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $gallery = Gallery::find($id);
            if ($gallery) {
                if (\File::exists($gallery->path)) {
                    \File::delete($gallery->path);
                }
                $gallery->delete();
                return responseCustom([], 200, 'Gallery deleted successfully');
            }
            return responseCustom([], 404, 'Gallery not found', ['error' => 'Gallery not found']);
        } catch (\Exception $e) {
            \Log::error("GalleryController error:" . $e->getMessage());
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
