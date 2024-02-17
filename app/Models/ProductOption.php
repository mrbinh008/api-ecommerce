<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    use HasFactory;

    public $table = 'product_options';

    protected $fillable = ['product_id', 'option_name'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productOptionValues()
    {
        return $this->hasMany(ProductOptionValue::class);
    }


}
