<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductShipping extends Model
{
    use HasFactory;

    public $table = 'product_shipping';

    protected $fillable = [
        'product_id',
        'shipping_id',
        'ship_charge',
        'is_free',
        'estimated_days'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }
}
