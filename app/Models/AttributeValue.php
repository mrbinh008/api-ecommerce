<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeValue extends Model
{
    use HasFactory;

    public $table = 'attribute_values';
    protected $fillable = ['attribute_id', 'attribute_value'];

    public function attribute()
    {
        return $this->belongsTo(Attribute::class);
    }

}
