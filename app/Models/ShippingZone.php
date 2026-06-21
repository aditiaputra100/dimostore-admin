<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['name', 'province', 'is_active']), WithoutTimestamps]
class ShippingZone extends Model
{
    public function shippingRates(): HasMany {
        return $this->hasMany(ShippingRate::class);
    }

    public function orders(): HasMany {
        return $this->hasMany(Order::class);
    }
}
