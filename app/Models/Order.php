<?php

namespace App\Models;

use App\OrderStatus;
use App\PaymentMethod;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\UseFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
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
#[UseFactory(OrderFactory::class)]
class Order extends Model
{
    use HasFactory;
    protected function casts(): array
    {
        return [
            'paid_at' => 'datetime',
            'payment_method' => PaymentMethod::class,
            'status' => OrderStatus::class,
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

    public function isDone(OrderStatus $targetStatus): bool {
        $currentStatus = $this->status;

        if (!$currentStatus) {
            return false;
        }

        return $targetStatus->getIndex() < $currentStatus->getIndex();
    }
}
