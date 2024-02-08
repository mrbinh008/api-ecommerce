<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantValue extends Model
{
    use HasFactory;

    public $table = 'variant_values';

    protected $fillable = [
        'variant_id',
        'price',
        'quantity',
    ];
}
