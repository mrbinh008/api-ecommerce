<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variant extends Model
{
    use HasFactory;

    public $table = 'variants';

    protected $fillable = [
        'product_id',
        'variant_attribute_value_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variantAttributeValue()
    {
        return $this->belongsTo(VariantAttributeValue::class);
    }

}
