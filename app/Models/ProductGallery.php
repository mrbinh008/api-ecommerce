<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductGallery extends Model
{
    use HasFactory;

    public $table = 'product_galleries';

    protected $fillable = [
        'product_id',
        'product_sku_id',
        'gallery_id',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    public function productSku()
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function scopeWhereProductId($query, $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeWhereProductSkuId($query, $productSkuId)
    {
        return $query->where('product_sku_id', $productSkuId);
    }

    public function scopeDeleteProductGallery($query, $productId)
    {
        return $query->where('product_id', $productId)->where('product_sku_id',null)->delete();
    }

    public function scopeDeleteGallery($query, $galleryId)
    {
        return $query->where('gallery_id', $galleryId)->delete();
    }
}
