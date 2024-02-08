<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Slideshow extends Model
{
    use HasFactory;

    public $table = 'slideshows';

    protected $fillable = [
        'title',
        'clicks',
        'image',
        'is_active',
    ];
}
