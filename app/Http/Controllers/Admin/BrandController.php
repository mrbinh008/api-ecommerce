<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\BrandRequest;
use App\Models\Brand;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class BrandController extends Controller
{
    use ImageUploadTrait;

    public function index()
    {
        $brand = Brand::query()->paginate(10);
        return responsePaginate($brand, $brand->items(), 200, 'Get data success');
    }

    public function store(BrandRequest $request)
    {
        if ($request->hasFile('logo')) {
            $logo = $this->uploadImage($request, 'logo', 'logo');
        }
        try {
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name, '-'),
                'logo' => $logo['path'] ?? null,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'featured' => $request->featured,
            ]);
            return responseCustom($brand, 200, 'Create brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Create brand failed');
        }
    }

    public function show($id)
    {
        $brand = Brand::query()->whereId($id)->first();
        if (!$brand) {
            return responseCustom([], 404, 'Brand not found');
        }
        return responseCustom($brand, 200, 'Get brand success');
    }

    public function update(BrandRequest $request)
    {
        try {
            $brand = Brand::query()->whereId($request->id);
            $logo = $brand->first()->logo;
            if ($request->hasFile('logo')) {
                $this->deleteImage($logo);
                $logo = $this->uploadImage($request, 'logo', 'logo');
                $logo = $logo['path'];
            }
            $brand->update([
                'name' => $request->name,
                'slug' => \Str::slug($request->name, '-'),
                'logo' => $logo,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'featured' => $request->featured,
            ]);
            return responseCustom($brand, 200, 'Update brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Update brand failed');
        }
    }

    public function delete($id)
    {
        try {
            $brand = Brand::query()->find($id);
            if (!$brand) {
                return responseCustom([], 404, 'Brand not found');
            }
            if ($brand->logo){
                $this->deleteImage($brand->logo);
            }
            $brand->delete();
            return responseCustom([], 200, 'Delete brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Delete brand failed');
        }
    }
}
