<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\WithoutTimestamps;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['status', 'note']), WithoutTimestamps]
class OrderStatusHistory extends Model
{
    public function order(): BelongsTo {
        return $this->belongsTo(Order::class);
    }

    public function createdBy(): BelongsTo {
        return $this->belongsTo(Admin::class);
    }
}
