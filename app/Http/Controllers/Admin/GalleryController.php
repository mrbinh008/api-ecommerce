<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Gallery;
use App\Models\ProductGallery;
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
                $path = str_replace(config('app.url') . '/', '', $gallery->path);
                if (\File::exists($path)) {
                    \File::delete($path);
                }
                ProductGallery::query()->deleteGallery($id);
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
