<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SkuValue extends Model
{
    use HasFactory;

    public $table = 'sku_values';

    protected $fillable = [
        'product_id',
        'sku_id',
        'option_id',
        'value_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function sku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function value()
    {
        return $this->belongsTo(OptionValue::class);
    }
}
