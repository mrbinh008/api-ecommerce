<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;

    public $table = 'product_skus';

    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function values()
    {
        return $this->hasMany(SkuValue::class,  'sku_id');
    }

    public function images()
    {
        return $this->hasMany(Gallery::class);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
