<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Gallery extends Model
{
    use HasFactory;

    public $table = 'galleries';

    protected $fillable = ['product_id', 'product_sku_id', 'name', 'path'];

    public $timestamps = false;

    public function getPathAttribute($value): string
    {
        return config('app.url') . '/'. $value;
    }

    public static function boot(): void
    {
        parent::boot();
        static::deleting(function ($gallery) {
            if ($gallery->path) {
                $path = str_replace(env('APP_URL') . '/', '', $gallery->path);
                if (file_exists($path)) {
                    unlink($path);
                }
            }
        });
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productSku(): BelongsTo
    {
        return $this->belongsTo(ProductSku::class);
    }

    public function scopeWhereProductId($query, $productId): mixed
    {
        return $query->where('product_id', $productId);
    }
}
