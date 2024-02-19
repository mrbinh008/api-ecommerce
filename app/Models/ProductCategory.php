<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductCategory extends Model
{
    use HasFactory;

    public $table = 'product_categories';

    protected $fillable = [
        'product_id',
        'category_id',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWhereCategoryId($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    public function scopeDeleteCategory($query, $productId, $arrayCategoryId)
    {
        return $query->where('product_id', $productId)->whereIn('category_id', $arrayCategoryId)->delete();
    }
}
