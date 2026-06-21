<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['min_weight', 'max_weight']), WithoutTimestamps]
class ShippingRate extends Model
{
    public function shippingZone(): BelongsTo {
        return $this->belongsTo(ShippingZone::class);
    }
}
