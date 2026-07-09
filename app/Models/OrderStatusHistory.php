<?php

namespace App\Models;

use App\OrderStatus;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['status', 'note', 'created_by'])]
class OrderStatusHistory extends Model
{
    protected function casts(): array {
        return [
            'status' => OrderStatus::class
        ];
    }

    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(Admin::class, 'created_by', 'id');
    }

    public function isDone(OrderStatus $targetStatus): bool {
        $currentStatus = $this->status;

        if (!$currentStatus) {
            return false;
        }

        return $targetStatus->getIndex() < $currentStatus->getIndex();
    }
}
