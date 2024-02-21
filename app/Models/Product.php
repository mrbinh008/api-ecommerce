<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    public $table = 'products';

    protected $fillable = [
        'product_name',
        'sku',
        'slug',
        'brand_id',
        'description',
        'short_description',
        'product_weight',
        'is_published',
        'is_featured',
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }


    public function images()
    {
        return $this->hasMany(ProductGallery::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'product_coupons');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function skus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function options()
    {
        return $this->hasMany(Option::class);
    }

//    public function options()
//    {
//        return $this->hasManyThrough(Option::class, ProductSku::class);
//    }

    public function optionValues()
    {
        return $this->hasMany(OptionValue::class);
    }

    public function skuValues()
    {
        return $this->hasMany(SkuValue::class);
    }

    public function scopeWhereBrandId($query, $brandId)
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeSearch($query, $keyword)
    {
        return $query->where('product_name', 'like', '%' . $keyword . '%');
    }
}
