<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::query()->where('parent_id',null)->paginate(10);
        return responsePaginate($categories, $categories->items(), 200, 'Get list category success');
    }
    /**
     * Display a listing of the resource.
     */
    public function getChildren(string $parentId)
    {
        $categories = Category::query()->where('parent_id',$parentId)->paginate(10);
        return responsePaginate($categories, $categories->items(), 200, 'Get list category success');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            $category=Category::create(
                [
                    'parent_id' => $request->parent_id ?? null,
                    'category_name' => $request->category_name,
                    'slug' => \Str::slug($request->category_name,'-'), // use Illuminate\Support\Str; (imported in the top of the file
                    'category_description' => $request->category_description,
                    'icon' => $request->icon ?? '',
                    'active' => $request->active ?? '1',
                ]
            );
            return responseCustom($category, 200, 'Create category success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Create category fail');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id);
        if (!$category) return responseCustom(null, 404, 'Category not found');
        return responseCustom($category, 200, 'Get category success');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) return responseCustom(null, 404, 'Category not found');
            $category->update(
                [
                    'parent_id' => $request->parent_id ?? null,
                    'category_name' => $request->category_name,
                    'slug' => \Str::slug($request->category_name,'-'), // use Illuminate\Support\Str; (imported in the top of the file
                    'category_description' => $request->category_description,
                    'icon' => $request->icon ?? '',
                    'active' => $request->active ?? '1',
                ]
            );
            return responseCustom($category, 200, 'Update category success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Update category fail');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) return responseCustom(null, 404, 'Category not found');
            $category->delete();
            return responseCustom($category, 200, 'Delete category success');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Delete category fail');
        }
    }
}
