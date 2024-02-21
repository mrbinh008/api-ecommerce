<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;
    public $table = 'brands';
    protected $fillable = [
        'name',
        'slug',
        'logo',
        'description',
        'is_active',
        'featured',
    ];

    public function products()
    {
        return $this->hasMany(Product::class);
    }
}
