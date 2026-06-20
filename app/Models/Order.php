<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'status', 
    'payment_method', 
    'payment_status',
    'tracking_number',
    'admin_notes',
    'paid_at',
    ])]
class Order extends Model
{
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function shippingZone(): BelongsTo {
        return $this->belongsTo(ShippingZone::class);
    }

    public function items(): HasMany {
        return $this->hasMany(OrderItem::class);
    }

    public function statusHistories(): HasMany {
        return $this->hasMany(OrderStatusHistory::class);
    }
}
