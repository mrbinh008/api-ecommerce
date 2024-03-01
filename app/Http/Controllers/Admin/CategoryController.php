<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Traits\ImageUploadTrait;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    use ImageUploadTrait;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $categories = Category::query()->parent()->paginate($request->limit ?? 10);
        return responsePaginate($categories,$categories->items(), 200, 'Lấy danh sách thể loại thành công');
    }

    /**
     * Display a listing of the resource.
     */
    public function getAll()
    {
        $categories= \DB::query()->select('id', 'category_name')->from('categories')->get();
        return responseCustom($categories, 200, 'Lấy danh sách thể loại thành công');
    }

    public function getChildren($parentId)
    {
        $categories = Category::query()->children($parentId)->withCount('children')->paginate(10);
        return responsePaginate($categories,$categories->items(), 200, 'Lấy danh sách thể loại con thành công');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CategoryRequest $request)
    {
        try {
            if ($request->hasFile('icon')) {
                $icon = $this->uploadImage($request->file('icon'), 'category')['path'];
            }
            $category = Category::create(
                [
                    'parent_id' => $request->parent_id ?? null,
                    'category_name' => $request->category_name,
                    'slug' => \Str::slug($request->category_name, '-'),
                    'category_description' => $request->category_description,
                    'icon' => $icon ?? null,
                    'active' => $request->active ?? '1',
                ]
            );
            return responseCustom($category, 200, 'Tạo thể loại thành công');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Tạo thể loại thất bại');
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $category = Category::find($id)->load('children');
        if (!$category) return responseCustom(null, 404, 'Thể loại không tồn tại');
        return responseCustom($category, 200, 'Lấy thông tin thể loại thành công');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CategoryRequest $request, string $id)
    {
        try {
            $category = Category::find($id);
            if (!$category) return responseCustom(null, 404, 'Thể loại không tồn tại');
            if ($request->hasFile('icon')) {
                $icon = $this->updateImage($request->file('icon'), 'category', $category->icon)['path'];
            }
            $category->update(
                [
                    'parent_id' => $request->parent_id ?? null,
                    'category_name' => $request->category_name,
                    'slug' => \Str::slug($request->category_name, '-'),
                    'category_description' => $request->category_description,
                    'icon' => $icon ?? null,
                    'active' => $request->active ?? true,
                ]
            );
            return responseCustom($category, 200, 'Cập nhật thể loại thành công');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Cập nhật thể loại thất bại');
        }
    }

    public function changeStatus($id)
    {
        try {
            $category = Category::changeStatus($id);
            if (!$category) return responseCustom(null, 404, 'Thể loại không tồn tại');
            return responseCustom($category, 200, 'Thay đổi trạng thái thành công');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Thay đổi trạng thái thất bại');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(int $id)
    {
        try {
            $category = Category::find($id);
            if ($category->productCategories->isNotEmpty()) return responseCustom([], 400, 'Thể loại này đang được sử dụng, không thể xóa');
            if (!$category) return responseCustom([], 404, 'Không tìm thấy thể loại cần xóa');
            $category->delete();
            return responseCustom($category, 200, 'Xóa thể loại thành công');
        } catch (\Exception $e) {
            \Log::error($e->getMessage());
            return responseCustom([], 500, 'Xóa thể loại thất bại');
        }
    }
}
