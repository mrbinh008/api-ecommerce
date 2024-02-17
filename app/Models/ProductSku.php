<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSku extends Model
{
    use HasFactory;

    public $table = 'product_skus';

    protected $fillable = ['product_id', 'sku', 'regular_price', 'discount_price', 'quantity'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSkuValues()
    {
        return $this->hasMany(ProductSkuValue::class);
    }


}
