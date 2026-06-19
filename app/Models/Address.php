<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['recipient_name', 'phone', 'address_line', 'city', 'province', 'postal_code', 'is_default'])]
class Address extends Model
{
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }
}
