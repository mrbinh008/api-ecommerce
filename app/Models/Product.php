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
        'product_note',
        'published'
    ];

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }


    public function images()
    {
        return $this->hasMany(Gallery::class);
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

    public function productOptions()
    {
        return $this->hasMany(ProductOption::class);
    }

    public function productSkus()
    {
        return $this->hasMany(ProductSku::class);
    }

    public function productSkuValues()
    {
        return $this->hasMany(ProductSkuValue::class);
    }

    public function productOptionValue()
    {
        return $this->hasMany(ProductOptionValue::class);
    }
}
