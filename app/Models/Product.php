<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function categories():BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'product_categories');
    }


    public function images():HasMany
    {
        return $this->hasMany(Gallery::class)->where('product_sku_id', '=', null);
    }

    public function brand():BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function coupons():BelongsToMany
    {
        return $this->belongsToMany(Coupon::class, 'product_coupons');
    }

    public function tags():BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }

    public function skus():HasMany
    {
        return $this->hasMany(ProductSku::class);
    }

    public function options():HasMany
    {
        return $this->hasMany(Option::class);
    }


    public function optionValues():HasMany
    {
        return $this->hasMany(OptionValue::class);
    }

    public function skuValues():HasMany
    {
        return $this->hasMany(SkuValue::class);
    }

    public function scopeWhereBrandId($query, $brandId): mixed
    {
        return $query->where('brand_id', $brandId);
    }

    public function scopeSearch($query, $keyword):mixed
    {
        return $query->where('product_name', 'like', '%' . $keyword . '%');
    }
}
