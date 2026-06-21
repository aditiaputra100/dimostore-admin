<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
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
    ]),
    UseFactory(ProductFactory::class)]
class Product extends Model
{
    use HasFactory;

    public function category(): BelongsTo {
        return $this->belongsTo(Category::class);
    }

    public function cartItems(): HasMany {
        return $this->hasMany(CartItem::class);
    }

    public function orderItems(): HasMany {
        return $this->hasMany(OrderItem::class);
    }
}
