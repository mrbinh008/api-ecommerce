<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VariantAttributeValue extends Model
{
    use HasFactory;

    public $table = 'variant_attribute_values';

    protected $fillable = [
        'variant_id',
        'attribute_id',
    ];
}
