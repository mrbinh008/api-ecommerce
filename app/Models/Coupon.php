<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    use HasFactory;

    public $table = 'coupons';
    protected $fillable = ['code', 'coupon_description', 'discount_value', 'discount_type', 'times_used','max_usage','coupon_start_date','coupon_end_date','status'];
}
