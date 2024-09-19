<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['customer_name', 'merchant_name'];

    public function items()
    {
        return $this->belongsToMany(Item::class, 'order_item')->withPivot('quantity')->withTimestamps();
    }
}
