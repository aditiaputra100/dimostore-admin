<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

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
    //
}
