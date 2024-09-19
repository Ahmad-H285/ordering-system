<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['name'];

    public function orders()
    {
        return $this->belongsToMany(Order::class, 'order_item')->withPivot('quantity')->withTimestamps();
    }

    public function ingredients()
    {
        return $this->belongsToMany(Ingredient::class, 'item_ingredient')->withPivot('weight')->withTimestamps();
    }
}
