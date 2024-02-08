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
        'regular_price',
        'discount_price',
        'quantity',
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

    public function attributes()
    {
        return $this->belongsToMany(Attribute::class, 'product_attributes');
    }

    public function images()
    {
        return $this->hasMany(Gallery::class);
    }

    public function variant()
    {
        return $this->hasMany(Variant::class);
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'product_coupons');
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class, 'product_tags');
    }
}
