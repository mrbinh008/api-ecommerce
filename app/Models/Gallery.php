<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Gallery extends Model
{
    use HasFactory;
    public $table = 'galleries';
    protected $fillable = ['product_id', 'image','is_main','thumbnail'];
}
