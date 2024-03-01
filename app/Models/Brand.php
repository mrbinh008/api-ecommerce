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

    protected $casts = [
        'is_active' => 'boolean',
        'featured' => 'boolean',
    ];

    public function getLogoAttribute($value)
    {
        return env('APP_URL').'/' . $value;
    }
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function scopeSearch($query,$search)
    {
        return $query->where('name','like','%'.$search.'%');
    }
}
