<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    use HasFactory;

    public $table = 'attributes';
    protected $fillable = ['attribute_name'];

    public function products()
    {
        return $this->belongsToMany(Product::class, 'attribute_product', 'attribute_id', 'product_id');
    }

}
