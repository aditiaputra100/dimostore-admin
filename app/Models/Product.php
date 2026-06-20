<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'name', 
    'description', 
    'sku', 
    'price', 
    'original_price', 
    'stock', 
    'weight', 
    'main_image', 
    'status', 
    'sold_count', 
    'deleted_at'
    ])]
class Product extends Model
{
    public function cartItems(): HasMany {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany {
        return $this->hasMany(OrderItem::class);
    }
}
