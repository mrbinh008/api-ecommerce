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

    public function getAll()
    {
        $brand = Brand::query()->select('id', 'name')->get();
        return responseCustom($brand, 200, 'Get data success');
    }

    public function store(BrandRequest $request)
    {
        if ($request->hasFile('logo')) {
            $logo = $this->uploadImage($request->file('logo'), 'logo')['path'];
        }
        try {
            $brand = Brand::create([
                'name' => $request->name,
                'slug' => \Str::slug($request->name, '-'),
                'logo' => $logo ?? null,
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

    public function update(BrandRequest $request,int $id) : \Illuminate\Http\JsonResponse
    {
        try {
            $brand = Brand::query()->whereId($id);
            if (!$brand->first()) {
                return responseCustom([], 404, 'Brand not found');
            }
            $logo = $brand->first()->logo;
            if ($request->hasFile('logo')&& $request->logo !== null) {
                $this->deleteImage($logo);
                $logo = $this->uploadImage($request->file('logo'), 'logo')['path'];
            }
            $brand->update([
                'name' => $request->name,
                'slug' => \Str::slug($request->name, '-'),
                'logo' => $logo,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'featured' => $request->featured,
            ]);
            return responseCustom($brand->first(), 200, 'Update brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Update brand failed');
        }
    }

    public function delete($id)
    {
        try {
            $brand = Brand::query()->find($id);
            if ($brand->products->isNotEmpty()) {
                return responseCustom([], 400,  'Brand has product');
            }
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

    public function search(Request $request)
    {
        $brand = Brand::query()->search($request->keyword)->paginate(10);
        return responsePaginate($brand, $brand->items(), 200, 'Get data success');
    }

    public function changeStatus($id)
    {
        try {
            $brand = Brand::query()->find($id);
            if (!$brand) {
                return responseCustom([], 404, 'Brand not found');
            }
            $brand->is_active = !$brand->is_active;
            $brand->save();
            return responseCustom($brand, 200, 'Change status brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Change status brand failed');
        }
    }

    public function changeFeatured($id)
    {
        try {
            $brand = Brand::query()->find($id);
            if (!$brand) {
                return responseCustom([], 404, 'Brand not found');
            }
            $brand->featured = !$brand->featured;
            $brand->save();
            return responseCustom($brand, 200, 'Change status feature brand success');
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            return responseCustom([], 500, 'Change status feature brand failed');
        }
    }


}
