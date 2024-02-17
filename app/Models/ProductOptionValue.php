<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOptionValue extends Model
{
    use HasFactory;

    public $table = 'product_option_values';

    protected $fillable = ['product_id', 'product_option_id', 'option_value'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productOption()
    {
        return $this->belongsTo(ProductOption::class);
    }


}
