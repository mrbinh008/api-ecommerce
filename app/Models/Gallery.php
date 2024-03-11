<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;

    public $table = 'galleries';
    protected $fillable = ['product_id', 'product_sku_id', 'name', 'path'];

    public function getPathAttribute($value)
    {
        return env('APP_URL') . '/' . $value;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }
}
