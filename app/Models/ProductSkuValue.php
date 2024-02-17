<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSkuValue extends Model
{
    use HasFactory;

    public $table = 'product_sku_values';

    protected $fillable = ['product_id', 'product_sku_id', 'product_option_id', 'product_option_value_id'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }

    public function productOptionValue()
    {
        return $this->belongsTo(ProductOptionValue::class);
    }


}
