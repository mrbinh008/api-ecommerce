<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    use HasFactory;

    public $table = 'user_addresses';

    protected $fillable = [
        'user_id',
        'address',
        'wards',
        'district',
        'province',
        'postal_code',
        'phone_number',
        'is_default',
    ];
}
