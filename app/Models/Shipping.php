<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipping extends Model
{
    use HasFactory;

    public $table = 'shippings';

    protected $fillable = [
        'name',
        'is_active',
        'icon',
    ];
}
