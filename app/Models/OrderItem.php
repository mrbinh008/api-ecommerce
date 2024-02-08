<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    public $table = 'order_items';
    protected $fillable = ['order_id', 'product_id','variant_id', 'quantity', 'price', 'total', 'shipping_id'];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function shipping()
    {
        return $this->belongsTo(Shipping::class);
    }
}
