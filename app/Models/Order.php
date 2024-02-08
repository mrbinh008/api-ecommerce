<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    public $table = 'orders';

    protected $fillable = ['user_id', 'order_number', 'status', 'order_approved_at', 'order_completed_at'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
