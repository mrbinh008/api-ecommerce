<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductCategoryRequest;
use App\Models\ProductCategory;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Store product category
     *
     * @param ProductCategoryRequest $request : product_id,category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductCategoryRequest $request)
    {
        try {
            $product_id = $request->input('product_id');
            $data = [];
            foreach ($request->category as $categoryId) {
                $data[] = [
                    'product_id' => $product_id,
                    'category_id' => $categoryId,
                ];
            }
            ProductCategory::query()->insert($data);
            return responseCustom([], 201, 'Create product category success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Create product category failed');
        }
    }

    /**
     * Update product category
     *
     * @param ProductCategoryRequest $request : product_id,category_id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductCategoryRequest $request)
    {
        try {
            $product_id = $request->input('product_id');
            $newCategories = collect($request->category);
            $existingCategories = ProductCategory::query()->whereProductId($product_id)->pluck('category_id');
            $categoriesToAdd = $newCategories->diff($existingCategories);
            $categoriesToRemove = $existingCategories->diff($newCategories);

            foreach ($categoriesToAdd as $categoryId) {
                $dataCreate[]=[
                    'product_id' => $product_id,
                    'category_id' => $categoryId,
                ];
            }
            ProductCategory::query()->insert($dataCreate);
            if (!$categoriesToRemove->isEmpty()) {
                ProductCategory::query()->deleteCategory($product_id, $categoriesToRemove);
            }

            return responseCustom([], 200, 'Update product category success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Update product category failed');
        }
    }
}
