<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    public $table = 'categories';
    protected $fillable = ['parent_id', 'category_name', 'slug', 'category_description', 'icon', 'active'];
    protected $hidden = ['created_at', 'updated_at'];
    protected $casts = [
        'active' => 'boolean',
    ];
    protected $with = ['children'];

    public function getIconAttribute($value)
    {
        return $value ? env('APP_URL') . "/" . $value : null;
    }

    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function productCategories()
    {
        return $this->hasMany(ProductCategory::class);
    }

    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    public function scopeParent($query)
    {
        return $query->where('parent_id', null);
    }

    public function scopeChildren($query, $parentId)
    {
        return $query->where('parent_id', $parentId);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('category_name', 'like', '%' . $search . '%');
    }

    public function scopeChangeStatus($query, $id)
    {
        $category = $query->find($id);
        $category->active = !$category->active;
        $category->save();
        return $category;
    }

    public function scopeGetALlWithName($query)
    {
        return $query->select('id', 'category_name');
    }


}
