<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OptionValue extends Model
{
    use HasFactory;

    public $table = 'option_values';

    protected $fillable = [
        'product_id',
        'option_id',
        'value_name',
    ];

    public function option()
    {
        return $this->belongsTo(Option::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function skuValues()
    {
        return $this->hasMany(SkuValue::class);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWhereOptionId($query, $optionId)
    {
        return $query->where('option_id', $optionId);
    }
}
